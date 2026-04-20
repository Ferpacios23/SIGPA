<?php

namespace App\Http\Controllers\Secretaria;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use App\Models\PrestamoAula;
use App\Models\PrestamoEquipo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrestamoEquipoController extends Controller
{
    // ── Listado de préstamos de equipos ─────────────────────────
    public function index(Request $request)
    {
        $query = PrestamoEquipo::with(['user', 'equipo', 'prestamoAula.aula', 'aprobadoPor']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_prestamo', $request->fecha);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('user', fn($u) =>
                    $u->where('name', 'like', '%'.$request->search.'%')
                )
                ->orWhereHas('equipo', fn($e) =>
                    $e->where('nombre', 'like', '%'.$request->search.'%')
                      ->orWhere('codigo_inventario', 'like', '%'.$request->search.'%')
                );
            });
        }

        $prestamos = $query->orderByDesc('fecha_prestamo')
                           ->orderBy('hora_inicio')
                           ->paginate(15)
                           ->withQueryString();

        $equiposDisponibles = Equipo::disponibles()->orderBy('nombre')->get();
        $docentes = User::whereHas('profile.role', fn($q) => $q->where('slug', 'docente'))
                        ->with('profile')
                        ->get();
        $prestamosAulaActivos = PrestamoAula::with(['aula', 'user'])
                        ->whereIn('estado', ['aprobado', 'activo'])
                        ->whereDate('fecha_prestamo', today())
                        ->orderBy('hora_inicio')
                        ->get();
        $pendientesCount = PrestamoEquipo::where('estado', 'pendiente')->count();

        return view('secretaria.equipos.index', compact(
            'prestamos',
            'equiposDisponibles',
            'docentes',
            'prestamosAulaActivos',
            'pendientesCount',
        ));
    }

    // ── Crear préstamo de equipo ─────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'          => ['required', 'exists:users,id'],
            'equipo_id'        => ['required', 'exists:equipos,id'],
            'prestamo_aula_id' => ['nullable', 'exists:prestamos_aulas,id'],
            'fecha_prestamo'   => ['required', 'date', 'after_or_equal:today'],
            'hora_inicio'      => ['required', 'date_format:H:i'],
            'hora_fin'         => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'motivo'           => ['nullable', 'string', 'max:255'],
        ]);

        $equipo = Equipo::findOrFail($data['equipo_id']);

        // RF13: Verificar disponibilidad
        if (!$equipo->disponible || !$equipo->activo) {
            return back()->withInput()
                         ->with('error', 'El equipo seleccionado no está disponible.');
        }

        // RF6: Verificar que no haya préstamo activo solapado
        $conflicto = PrestamoEquipo::where('equipo_id', $data['equipo_id'])
            ->whereDate('fecha_prestamo', $data['fecha_prestamo'])
            ->whereIn('estado', ['pendiente', 'aprobado', 'activo'])
            ->where(function ($q) use ($data) {
                $q->where(function ($q2) use ($data) {
                    $q2->where('hora_inicio', '<', $data['hora_fin'])
                       ->where('hora_fin',    '>', $data['hora_inicio']);
                });
            })->exists();

        if ($conflicto) {
            return back()->withInput()
                         ->with('error', 'El equipo ya tiene un préstamo en ese horario.');
        }

        DB::transaction(function () use ($data, $equipo) {
            PrestamoEquipo::create([
                'user_id'          => $data['user_id'],
                'equipo_id'        => $data['equipo_id'],
                'prestamo_aula_id' => $data['prestamo_aula_id'] ?? null,
                'aprobado_por'     => Auth::id(),
                'fecha_prestamo'   => $data['fecha_prestamo'],
                'hora_inicio'      => $data['hora_inicio'],
                'hora_fin'         => $data['hora_fin'],
                'motivo'           => $data['motivo'] ?? null,
                'estado'           => 'aprobado',
            ]);

            // RF14: Marcar equipo como no disponible
            $equipo->update(['disponible' => false]);

            DB::table('historial_movimientos')->insert([
                'user_id'      => Auth::id(),
                'entidad_tipo' => PrestamoEquipo::class,
                'entidad_id'   => 0,
                'tipo_accion'  => 'prestamo_equipo_creado',
                'descripcion'  => "Préstamo del equipo '{$equipo->nombre}' registrado y aprobado.",
                'payload'      => json_encode([
                    'equipo_id'        => $equipo->id,
                    'codigo'           => $equipo->codigo_inventario,
                    'prestamo_aula_id' => $data['prestamo_aula_id'] ?? null,
                ]),
                'created_at'   => now(),
            ]);
        });

        return redirect()->route('secretaria.equipos.index')
                         ->with('success', 'Préstamo de equipo registrado y aprobado.');
    }

    // ── Aprobar préstamo pendiente ───────────────────────────────
    public function aprobar(PrestamoEquipo $prestamoEquipo)
    {
        if ($prestamoEquipo->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden aprobar préstamos pendientes.');
        }

        $equipo = $prestamoEquipo->equipo;

        if (!$equipo->disponible) {
            return back()->with('error', 'El equipo ya no está disponible.');
        }

        DB::transaction(function () use ($prestamoEquipo, $equipo) {
            $prestamoEquipo->update([
                'estado'       => 'aprobado',
                'aprobado_por' => Auth::id(),
            ]);
            $equipo->update(['disponible' => false]);
        });

        return back()->with('success', 'Préstamo de equipo aprobado.');
    }

    // ── Registrar entrega del equipo al docente ──────────────────
    public function entregar(PrestamoEquipo $prestamoEquipo)
    {
        if ($prestamoEquipo->estado !== 'aprobado') {
            return back()->with('error', 'Solo se puede registrar entrega de préstamos aprobados.');
        }

        $prestamoEquipo->update([
            'estado'       => 'activo',
            'entregado'    => true,
            'entregado_en' => now(),
        ]);

        DB::table('historial_movimientos')->insert([
            'user_id'      => Auth::id(),
            'entidad_tipo' => PrestamoEquipo::class,
            'entidad_id'   => $prestamoEquipo->id,
            'tipo_accion'  => 'entrega_equipo',
            'descripcion'  => "Equipo '{$prestamoEquipo->equipo?->nombre}' entregado al docente {$prestamoEquipo->user?->name}.",
            'payload'      => null,
            'created_at'   => now(),
        ]);

        return back()->with('success', 'Entrega registrada. El equipo está en uso.');
    }

    // ── Registrar devolución del equipo ─────────────────────────
    public function devolver(Request $request, PrestamoEquipo $prestamoEquipo)
    {
        if (!in_array($prestamoEquipo->estado, ['aprobado', 'activo'])) {
            return back()->with('error', 'Solo se puede registrar devolución de préstamos activos.');
        }

        $estadoDevolucion = $request->input('estado_devolucion', 'bueno');

        DB::transaction(function () use ($prestamoEquipo, $estadoDevolucion) {
            $prestamoEquipo->update([
                'estado'            => 'finalizado',
                'devuelto'          => true,
                'devuelto_en'       => now(),
                'estado_devolucion' => $estadoDevolucion,
            ]);

            // RF14.1: Liberar equipo al finalizar
            if ($prestamoEquipo->equipo) {
                $disponible = !in_array($estadoDevolucion, ['dañado', 'dado_de_baja']);
                $prestamoEquipo->equipo->update([
                    'disponible'   => $disponible,
                    'estado_fisico' => $estadoDevolucion,
                ]);
            }

            DB::table('historial_movimientos')->insert([
                'user_id'      => Auth::id(),
                'entidad_tipo' => PrestamoEquipo::class,
                'entidad_id'   => $prestamoEquipo->id,
                'tipo_accion'  => 'devolucion_equipo',
                'descripcion'  => "Equipo '{$prestamoEquipo->equipo?->nombre}' devuelto. Estado: {$estadoDevolucion}.",
                'payload'      => json_encode(['estado_devolucion' => $estadoDevolucion]),
                'created_at'   => now(),
            ]);
        });

        return back()->with('success', 'Devolución registrada. Equipo liberado.');
    }

    // ── Cancelar préstamo ────────────────────────────────────────
    public function cancelar(Request $request, PrestamoEquipo $prestamoEquipo)
    {
        if (in_array($prestamoEquipo->estado, ['finalizado', 'cancelado'])) {
            return back()->with('error', 'Este préstamo ya está cerrado.');
        }

        DB::transaction(function () use ($request, $prestamoEquipo) {
            $prestamoEquipo->update([
                'estado'             => 'cancelado',
                'cancelado_en'       => now(),
                'motivo_cancelacion' => $request->motivo ?? 'Cancelado por secretaría',
            ]);

            // Liberar equipo si aún no fue devuelto
            if ($prestamoEquipo->equipo && !$prestamoEquipo->devuelto) {
                $prestamoEquipo->equipo->update(['disponible' => true]);
            }
        });

        return back()->with('success', 'Préstamo de equipo cancelado.');
    }

    // ── Asignar equipo directamente a un préstamo de aula (HU-09) ──
    public function asignarALoan(Request $request, PrestamoAula $prestamo)
    {
        // RF7: Bloquear si el préstamo de aula no está activo/aprobado
        if (!in_array($prestamo->estado, ['aprobado', 'activo'])) {
            return back()->with('error', 'Solo se pueden asignar equipos a préstamos de aula aprobados o activos.');
        }

        $data = $request->validate([
            'equipo_id' => ['required', 'exists:equipos,id'],
        ]);

        $equipo = Equipo::findOrFail($data['equipo_id']);

        // RF13: Verificar disponibilidad
        if (!$equipo->disponible || !$equipo->activo) {
            return back()->with('error', 'El equipo seleccionado no está disponible.');
        }

        // RF6: Verificar que no ya esté asignado a este mismo préstamo
        $yaAsignado = PrestamoEquipo::where('equipo_id', $equipo->id)
            ->where('prestamo_aula_id', $prestamo->id)
            ->whereIn('estado', ['aprobado', 'activo'])
            ->exists();

        if ($yaAsignado) {
            return back()->with('error', 'Este equipo ya está asignado a este préstamo.');
        }

        // Verificar solapamiento de horario
        $conflicto = PrestamoEquipo::where('equipo_id', $equipo->id)
            ->whereDate('fecha_prestamo', $prestamo->fecha_prestamo)
            ->whereIn('estado', ['pendiente', 'aprobado', 'activo'])
            ->where(function ($q) use ($prestamo) {
                $q->where('hora_inicio', '<', $prestamo->hora_fin)
                  ->where('hora_fin',    '>', $prestamo->hora_inicio);
            })->exists();

        if ($conflicto) {
            return back()->with('error', 'El equipo ya tiene un préstamo en ese horario.');
        }

        DB::transaction(function () use ($prestamo, $equipo) {
            $pe = PrestamoEquipo::create([
                'user_id'          => $prestamo->user_id,
                'equipo_id'        => $equipo->id,
                'prestamo_aula_id' => $prestamo->id,
                'aprobado_por'     => Auth::id(),
                'fecha_prestamo'   => $prestamo->fecha_prestamo,
                'hora_inicio'      => $prestamo->hora_inicio,
                'hora_fin'         => $prestamo->hora_fin,
                'motivo'           => 'Asignado al préstamo de aula #'.$prestamo->id,
                'estado'           => 'aprobado',
            ]);

            // RF8: Actualizar disponibilidad
            $equipo->update(['disponible' => false]);

            DB::table('historial_movimientos')->insert([
                'user_id'      => Auth::id(),
                'entidad_tipo' => PrestamoEquipo::class,
                'entidad_id'   => $pe->id,
                'tipo_accion'  => 'asignacion_equipo_aula',
                'descripcion'  => "Equipo '{$equipo->nombre}' asignado al préstamo de aula #{$prestamo->id} ({$prestamo->aula?->codigo}).",
                'payload'      => json_encode([
                    'equipo_id'        => $equipo->id,
                    'prestamo_aula_id' => $prestamo->id,
                ]),
                'created_at'   => now(),
            ]);
        });

        return back()->with('success', "Equipo '{$equipo->nombre}' asignado al préstamo de aula.");
    }
}
