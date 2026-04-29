<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use App\Models\HorarioAcademico;
use App\Models\PrestamoAula;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocenteController extends Controller
{
    // ── Dashboard principal ──────────────────────────────────────
    public function index()
    {
        $docente = Auth::user();

        $lunes = Carbon::now()->startOfWeek(Carbon::MONDAY);

        $nombresD = ['lunes','martes','miercoles','jueves','viernes','sabado'];
        $diasConFecha = [];
        for ($i = 0; $i < 6; $i++) {
            $diasConFecha[$nombresD[$i]] = $lunes->copy()->addDays($i);
        }

        $horariosSemana = [];
        foreach ($nombresD as $dia) {
            $horariosSemana[$dia] = HorarioAcademico::with('aula')
                ->where('docente_id', $docente->id)
                ->where('dia_semana', $dia)
                ->where('activo', true)
                ->where('fecha_inicio', '<=', now()->toDateString())
                ->where('fecha_fin',    '>=', now()->toDateString())
                ->orderBy('hora_inicio')
                ->get();
        }

        $totalClases  = collect($horariosSemana)->flatten()->count();
        $pendientes   = PrestamoAula::where('user_id', $docente->id)->where('estado', 'pendiente')->count();
        $aprobadas    = PrestamoAula::where('user_id', $docente->id)
                            ->whereIn('estado', ['aprobado', 'activo'])
                            ->count();

        $solicitudesRecientes = PrestamoAula::with('aula')
            ->where('user_id', $docente->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('docente.dashboard', compact(
            'horariosSemana', 'diasConFecha', 'nombresD',
            'totalClases', 'pendientes', 'aprobadas',
            'solicitudesRecientes'
        ));
    }

    // ── Listado completo de solicitudes ─────────────────────────
    public function solicitudes(Request $request)
    {
        $docente = Auth::user();

        $query = PrestamoAula::with('aula')->where('user_id', $docente->id);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_prestamo', $request->fecha);
        }

        $solicitudes = $query->orderByDesc('fecha_prestamo')
                             ->orderBy('hora_inicio')
                             ->paginate(15)
                             ->withQueryString();

        $aulas = Aula::where('activa', true)->orderBy('nombre')->get();

        return view('docente.solicitudes', compact('solicitudes', 'aulas'));
    }

    // ── Crear nueva solicitud ───────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'aula_id'        => ['required', 'exists:aulas,id'],
            'fecha_prestamo' => ['required', 'date', 'after_or_equal:today'],
            'hora_inicio'    => ['required', 'date_format:H:i'],
            'hora_fin'       => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'motivo'         => ['nullable', 'string', 'max:255'],
        ]);

        if (HorarioAcademico::aulaOcupadaEn($data['aula_id'], $data['fecha_prestamo'], $data['hora_inicio'], $data['hora_fin'])) {
            return back()->withInput()
                ->with('error', 'El aula está bloqueada por un horario académico en esa franja horaria.');
        }

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
            return back()->withInput()
                ->with('error', 'El aula ya tiene un préstamo en ese horario. Intenta con otro horario o aula.');
        }

        PrestamoAula::create([
            'user_id'            => Auth::id(),
            'aula_id'            => $data['aula_id'],
            'fecha_prestamo'     => $data['fecha_prestamo'],
            'hora_inicio'        => $data['hora_inicio'],
            'hora_fin'           => $data['hora_fin'],
            'motivo'             => $data['motivo'] ?? null,
            'estado'             => 'pendiente',
            'tolerancia_minutos' => 15,
        ]);

        return redirect()->route('docente.solicitudes.index')
            ->with('success', 'Solicitud enviada. La secretaría revisará tu solicitud pronto.');
    }

    // ── Cancelar solicitud propia pendiente ─────────────────────
    public function cancelar(PrestamoAula $solicitud)
    {
        if ($solicitud->user_id !== Auth::id()) {
            abort(403);
        }

        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'Solo puedes cancelar solicitudes que estén en estado pendiente.');
        }

        $solicitud->update([
            'estado'             => 'cancelado',
            'cancelado_en'       => now(),
            'motivo_cancelacion' => 'Cancelado por el docente',
        ]);

        return back()->with('success', 'Solicitud cancelada correctamente.');
    }
}
