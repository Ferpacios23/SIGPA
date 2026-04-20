<?php
// app/Http/Controllers/Dashboard/SecretariaController.php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use App\Models\Equipo;
use App\Models\HorarioAcademico;
use App\Models\PrestamoAula;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SecretariaController extends Controller
{
    // ── Dashboard principal ──────────────────────────────────────
    public function index()
    {
        $aulasLibres   = Aula::where('estado', 'disponible')->where('activa', true)->count();
        $aulasOcupadas = Aula::where('estado', 'ocupada')->count();
        $totalAulas    = Aula::where('activa', true)->count();

        $prestamosHoy      = PrestamoAula::whereDate('fecha_prestamo', today())->count();
        $prestamosPendientes = PrestamoAula::where('estado', 'pendiente')->count();
        $prestamosActivos  = PrestamoAula::whereIn('estado', ['aprobado', 'activo'])->count();

        // Préstamos activos del día con relaciones
        $prestamosDeHoy = PrestamoAula::with(['user', 'aula'])
            ->whereDate('fecha_prestamo', today())
            ->whereIn('estado', ['pendiente', 'aprobado', 'activo'])
            ->orderBy('hora_inicio')
            ->get();

        // Solicitudes pendientes de aprobación
        $pendientes = PrestamoAula::with(['user', 'aula'])
            ->where('estado', 'pendiente')
            ->latest()
            ->take(8)
            ->get();

        // Aulas con su estado
        $aulas = Aula::where('activa', true)->orderBy('codigo')->get();

        // Horarios académicos activos para hoy
        $dias = ['domingo','lunes','martes','miercoles','jueves','viernes','sabado'];
        $diaHoy = $dias[now()->dayOfWeek];

        $horariosHoy = \App\Models\HorarioAcademico::with(['docente','aula'])
            ->where('dia_semana', $diaHoy)
            ->where('activo', true)
            ->where('fecha_inicio', '<=', today())
            ->where('fecha_fin',    '>=', today())
            ->orderBy('hora_inicio')
            ->get();

        return view('dashboard.secretaria', compact(
            'aulasLibres',
            'aulasOcupadas',
            'totalAulas',
            'prestamosHoy',
            'prestamosPendientes',
            'prestamosActivos',
            'prestamosDeHoy',
            'pendientes',
            'aulas',
            'horariosHoy',
        ));
    }

    // ── Listado de préstamos ─────────────────────────────────────
    public function prestamos(Request $request)
    {
        $query = PrestamoAula::with(['user', 'aula', 'aprobadoPor']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_prestamo', $request->fecha);
        }
        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) =>
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%')
            )->orWhereHas('aula', fn($q) =>
                $q->where('codigo', 'like', '%'.$request->search.'%')
            );
        }

        $prestamos = $query->orderByDesc('fecha_prestamo')
                           ->orderBy('hora_inicio')
                           ->paginate(15)
                           ->withQueryString();

        $aulas   = Aula::where('activa', true)->orderBy('codigo')->get();
        $docentes= User::whereHas('profile.role', fn($q) => $q->where('slug', 'docente'))
                       ->with('profile')
                       ->get();
        $prestamosPendientesCount = PrestamoAula::where('estado', 'pendiente')->count();
        $equiposDisponibles = Equipo::disponibles()->orderBy('nombre')->get();

        return view('secretaria.prestamos', compact(
            'prestamos', 'aulas', 'docentes',
            'prestamosPendientesCount', 'equiposDisponibles',
        ));
    }

    // ── Crear préstamo desde secretaría ─────────────────────────
    public function storePrestamo(Request $request)
    {
        $data = $request->validate([
            'user_id'       => ['required', 'exists:users,id'],
            'aula_id'       => ['required', 'exists:aulas,id'],
            'fecha_prestamo'=> ['required', 'date', 'after_or_equal:today'],
            'hora_inicio'   => ['required', 'date_format:H:i'],
            'hora_fin'      => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'motivo'        => ['nullable', 'string', 'max:255'],
            'tolerancia_minutos' => ['nullable', 'integer', 'min:5', 'max:60'],
        ]);

        // Verificar bloqueo por horario académico
        if (HorarioAcademico::aulaOcupadaEn($data['aula_id'], $data['fecha_prestamo'], $data['hora_inicio'], $data['hora_fin'])) {
            return back()
                ->withInput()
                ->with('error', 'El aula está bloqueada por un horario académico registrado en esa fecha y franja horaria.');
        }

        // Verificar conflicto de préstamos existentes
        $conflicto = PrestamoAula::where('aula_id', $data['aula_id'])
            ->whereDate('fecha_prestamo', $data['fecha_prestamo'])
            ->whereIn('estado', ['pendiente', 'aprobado', 'activo'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('hora_inicio', [$data['hora_inicio'], $data['hora_fin']])
                  ->orWhereBetween('hora_fin',  [$data['hora_inicio'], $data['hora_fin']])
                  ->orWhere(function ($q2) use ($data) {
                      $q2->where('hora_inicio', '<=', $data['hora_inicio'])
                         ->where('hora_fin',    '>=', $data['hora_fin']);
                  });
            })->exists();

        if ($conflicto) {
            return back()
                ->withInput()
                ->with('error', 'El aula ya tiene un préstamo en ese horario.');
        }

        PrestamoAula::create([
            'user_id'            => $data['user_id'],
            'aula_id'            => $data['aula_id'],
            'aprobado_por'       => Auth::id(),
            'fecha_prestamo'     => $data['fecha_prestamo'],
            'hora_inicio'        => $data['hora_inicio'],
            'hora_fin'           => $data['hora_fin'],
            'motivo'             => $data['motivo'] ?? null,
            'tolerancia_minutos' => $data['tolerancia_minutos'] ?? 15,
            'estado'             => 'aprobado', // Secretaría aprueba directamente
        ]);

        return redirect()->route('secretaria.prestamos')
                         ->with('success', 'Préstamo registrado y aprobado.');
    }

    // ── Aprobar préstamo pendiente ───────────────────────────────
    public function aprobar(PrestamoAula $prestamo)
    {
        if ($prestamo->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden aprobar préstamos pendientes.');
        }

        $prestamo->update([
            'estado'       => 'aprobado',
            'aprobado_por' => Auth::id(),
        ]);

        return back()->with('success', 'Préstamo aprobado correctamente.');
    }

    // ── Rechazar / cancelar préstamo ────────────────────────────
    public function cancelar(Request $request, PrestamoAula $prestamo)
    {
        if (in_array($prestamo->estado, ['finalizado', 'cancelado'])) {
            return back()->with('error', 'Este préstamo ya está cerrado.');
        }

        $prestamo->update([
            'estado'             => 'cancelado',
            'cancelado_en'       => now(),
            'motivo_cancelacion' => $request->motivo ?? 'Cancelado por secretaría',
        ]);

        // Si el aula estaba activa, liberarla
        if ($prestamo->aula && $prestamo->estado === 'activo') {
            $prestamo->aula->update(['estado' => 'disponible']);
        }

        return back()->with('success', 'Préstamo cancelado.');
    }

    // ── Finalizar préstamo activo ────────────────────────────────
    public function finalizar(PrestamoAula $prestamo)
    {
        if (!in_array($prestamo->estado, ['aprobado', 'activo'])) {
            return back()->with('error', 'Solo se pueden finalizar préstamos activos o aprobados.');
        }

        $prestamo->update(['estado' => 'finalizado']);

        // Liberar el aula
        if ($prestamo->aula) {
            $prestamo->aula->update(['estado' => 'disponible']);
        }

        return back()->with('success', 'Préstamo finalizado. Aula liberada.');
    }

    // ── Vista de aulas ───────────────────────────────────────────
    public function aulas(Request $request)
    {
        $aulas = Aula::where('activa', true)
            ->when($request->estado, fn($q, $v) => $q->where('estado', $v))
            ->when($request->search, fn($q, $v) =>
                $q->where('codigo', 'like', "%$v%")
                  ->orWhere('nombre', 'like', "%$v%")
            )
            ->orderBy('codigo')
            ->get();

        // Para cada aula disponible, obtener el préstamo activo si lo hay
        $aulas->each(function ($aula) {
            $aula->prestamo_activo = PrestamoAula::with('user')
                ->where('aula_id', $aula->id)
                ->whereIn('estado', ['aprobado', 'activo'])
                ->whereDate('fecha_prestamo', today())
                ->first();
        });

        return view('secretaria.aulas', compact('aulas'));
    }

    // ── Check-in de asistencia ───────────────────────────────────
    public function checkin(PrestamoAula $prestamo)
    {
        if ($prestamo->estado !== 'aprobado') {
            return back()->with('error', 'Solo se puede confirmar asistencia en préstamos aprobados.');
        }

        DB::transaction(function () use ($prestamo) {
            $prestamo->update([
                'estado'                   => 'activo',
                'asistencia_confirmada'    => true,
                'asistencia_confirmada_en' => now(),
            ]);

            if ($prestamo->aula) {
                $prestamo->aula->update(['estado' => 'ocupada']);
            }

            DB::table('historial_movimientos')->insert([
                'user_id'      => Auth::id(),
                'entidad_tipo' => PrestamoAula::class,
                'entidad_id'   => $prestamo->id,
                'tipo_accion'  => 'confirmacion_asistencia',
                'descripcion'  => "Check-in confirmado para el aula {$prestamo->aula?->codigo}.",
                'payload'      => json_encode([
                    'prestamo_id'       => $prestamo->id,
                    'confirmado_por'    => Auth::id(),
                    'confirmado_en'     => now()->toIso8601String(),
                ]),
                'created_at'   => now(),
            ]);
        });

        return back()->with('success', 'Asistencia confirmada. El aula ha sido marcada como ocupada.');
    }

    // ── Historial ────────────────────────────────────────────────
    public function historial(Request $request)
    {
        $desde = $request->get('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->get('hasta', now()->toDateString());

        $prestamos = PrestamoAula::with(['user', 'aula', 'aprobadoPor'])
            ->whereBetween('fecha_prestamo', [$desde, $hasta])
            ->whereIn('estado', ['finalizado', 'cancelado', 'liberado_por_tolerancia'])
            ->latest('fecha_prestamo')
            ->paginate(20)
            ->withQueryString();

        $resumen = [
            'finalizados' => PrestamoAula::whereBetween('fecha_prestamo', [$desde, $hasta])->where('estado', 'finalizado')->count(),
            'cancelados'  => PrestamoAula::whereBetween('fecha_prestamo', [$desde, $hasta])->where('estado', 'cancelado')->count(),
            'total'       => PrestamoAula::whereBetween('fecha_prestamo', [$desde, $hasta])->count(),
        ];

        return view('secretaria.historial', compact('prestamos', 'resumen', 'desde', 'hasta'));
    }
}