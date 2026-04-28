<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use App\Models\PrestamoAula;
use App\Models\PrestamoEquipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TecnicoController extends Controller
{
    private function scopeSalasTI($query)
    {
        return $query->whereRaw("LOWER(ubicacion_almacenamiento) LIKE ?", ['%sala ti%']);
    }

    // ── Dashboard ───────────────────────────────────────────────
    public function index()
    {
        $fueraDeServicio    = ['dañado', 'dado_de_baja'];
        $totalEquipos       = $this->scopeSalasTI(Equipo::where('activo', true))->count();
        $equiposDisponibles = $this->scopeSalasTI(Equipo::where('activo', true)->where('disponible', true))->count();
        $equiposPrestados   = $this->scopeSalasTI(
                                Equipo::where('activo', true)
                                      ->where('disponible', false)
                                      ->whereNotIn('estado_fisico', $fueraDeServicio)
                              )->count();
        $equiposDañados     = $this->scopeSalasTI(
                                Equipo::where('activo', true)
                                      ->whereIn('estado_fisico', $fueraDeServicio)
                              )->count();

        // Préstamos de aula activos/aprobados de hoy con sus equipos asignados
        $prestamosActivos = PrestamoAula::with(['user', 'aula', 'prestamosEquipos.equipo'])
            ->whereIn('estado', ['aprobado', 'activo'])
            ->whereDate('fecha_prestamo', today())
            ->orderBy('hora_inicio')
            ->get();

        // Equipos actualmente asignados (en préstamo activo hoy)
        $equiposAsignados = PrestamoEquipo::with(['equipo', 'prestamoAula.aula', 'user'])
            ->whereIn('estado', ['aprobado', 'activo'])
            ->whereDate('fecha_prestamo', today())
            ->orderBy('hora_inicio')
            ->get();

        // Últimas devoluciones del día
        $devolucionesHoy = PrestamoEquipo::with(['equipo', 'user'])
            ->where('devuelto', true)
            ->whereDate('devuelto_en', today())
            ->latest('devuelto_en')
            ->take(5)
            ->get();

        $disponibles = $this->scopeSalasTI(Equipo::disponibles())->orderBy('nombre')->get();

        return view('dashboard.tecnico', compact(
            'totalEquipos',
            'equiposDisponibles',
            'equiposPrestados',
            'equiposDañados',
            'prestamosActivos',
            'equiposAsignados',
            'devolucionesHoy',
            'disponibles',
        ));
    }

    // ── Asignaciones: préstamos de aula del día con gestión de equipos ──
    public function asignaciones(Request $request)
    {
        $fecha = $request->get('fecha', today()->toDateString());

        $prestamos = PrestamoAula::with(['user', 'aula', 'prestamosEquipos.equipo'])
            ->whereIn('estado', ['aprobado', 'activo'])
            ->whereDate('fecha_prestamo', $fecha)
            ->orderBy('hora_inicio')
            ->get();

        $equiposDisponibles = $this->scopeSalasTI(Equipo::disponibles())->orderBy('nombre')->get();

        return view('tecnico.asignaciones', compact('prestamos', 'equiposDisponibles', 'fecha'));
    }

    // ── Asignar equipo a un préstamo de aula ────────────────────
    public function asignar(Request $request, PrestamoAula $prestamo)
    {
        // Validar que el préstamo esté activo/aprobado
        if (!in_array($prestamo->estado, ['aprobado', 'activo'])) {
            return back()->with('error', 'Solo se pueden asignar equipos a préstamos aprobados o activos.');
        }

        $data = $request->validate([
            'equipo_id' => ['required', 'exists:equipos,id'],
        ]);

        $equipo = Equipo::findOrFail($data['equipo_id']);

        // Validar disponibilidad
        if (!$equipo->disponible || !$equipo->activo) {
            return back()->with('error', "El equipo '{$equipo->nombre}' no está disponible.");
        }

        // Validar que no esté ya asignado a este préstamo
        $yaAsignado = PrestamoEquipo::where('equipo_id', $equipo->id)
            ->where('prestamo_aula_id', $prestamo->id)
            ->whereIn('estado', ['aprobado', 'activo'])
            ->exists();

        if ($yaAsignado) {
            return back()->with('error', "El equipo '{$equipo->nombre}' ya está asignado a este préstamo.");
        }

        // Validar conflicto de horario con otro préstamo
        $conflicto = PrestamoEquipo::where('equipo_id', $equipo->id)
            ->whereDate('fecha_prestamo', $prestamo->fecha_prestamo)
            ->whereIn('estado', ['pendiente', 'aprobado', 'activo'])
            ->where('hora_inicio', '<', $prestamo->hora_fin)
            ->where('hora_fin',    '>', $prestamo->hora_inicio)
            ->exists();

        if ($conflicto) {
            return back()->with('error', "El equipo '{$equipo->nombre}' ya tiene asignación en ese horario.");
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
                'motivo'           => "Asignado por TI al préstamo #{$prestamo->id} — aula {$prestamo->aula?->codigo}",
                'estado'           => 'aprobado',
                'entregado'        => true,
                'entregado_en'     => now(),
            ]);

            $equipo->update(['disponible' => false]);

            DB::table('historial_movimientos')->insert([
                'user_id'      => Auth::id(),
                'entidad_tipo' => PrestamoEquipo::class,
                'entidad_id'   => $pe->id,
                'tipo_accion'  => 'asignacion_ti',
                'descripcion'  => "TI asignó equipo '{$equipo->nombre}' al aula {$prestamo->aula?->codigo} (préstamo #{$prestamo->id}).",
                'payload'      => json_encode([
                    'equipo_id'        => $equipo->id,
                    'prestamo_aula_id' => $prestamo->id,
                    'aula'             => $prestamo->aula?->codigo,
                ]),
                'created_at'   => now(),
            ]);
        });

        return back()->with('success', "Equipo '{$equipo->nombre}' asignado al aula {$prestamo->aula?->codigo}.");
    }

    // ── Registrar devolución de equipo ───────────────────────────
    public function devolver(Request $request, PrestamoEquipo $asignacion)
    {
        if (!in_array($asignacion->estado, ['aprobado', 'activo'])) {
            return back()->with('error', 'Este equipo ya fue devuelto o está cancelado.');
        }

        $estadoDevolucion = $request->input('estado_devolucion', 'bueno');

        DB::transaction(function () use ($asignacion, $estadoDevolucion) {
            $asignacion->update([
                'estado'            => 'finalizado',
                'devuelto'          => true,
                'devuelto_en'       => now(),
                'estado_devolucion' => $estadoDevolucion,
            ]);

            if ($asignacion->equipo) {
                $disponible = !in_array($estadoDevolucion, ['dañado', 'dado_de_baja']);
                $asignacion->equipo->update([
                    'disponible'    => $disponible,
                    'estado_fisico' => $estadoDevolucion,
                ]);
            }

            DB::table('historial_movimientos')->insert([
                'user_id'      => Auth::id(),
                'entidad_tipo' => PrestamoEquipo::class,
                'entidad_id'   => $asignacion->id,
                'tipo_accion'  => 'devolucion_ti',
                'descripcion'  => "TI registró devolución de '{$asignacion->equipo?->nombre}'. Estado: {$estadoDevolucion}.",
                'payload'      => json_encode(['estado_devolucion' => $estadoDevolucion]),
                'created_at'   => now(),
            ]);
        });

        return back()->with('success', "Equipo '{$asignacion->equipo?->nombre}' devuelto. Estado: {$estadoDevolucion}.");
    }

    // ── Inventario completo de equipos ───────────────────────────
    public function inventario(Request $request)
    {
        $query = $this->scopeSalasTI(Equipo::where('activo', true));

        if ($request->filled('estado')) {
            if ($request->estado === 'disponible') {
                $query->where('disponible', true);
            } elseif ($request->estado === 'prestado') {
                $query->where('disponible', false)
                      ->whereNotIn('estado_fisico', ['dañado', 'dado_de_baja']);
            } elseif ($request->estado === 'dañado') {
                $query->whereIn('estado_fisico', ['dañado', 'dado_de_baja']);
            }
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%'.$request->search.'%')
                  ->orWhere('codigo_inventario', 'like', '%'.$request->search.'%')
                  ->orWhere('marca', 'like', '%'.$request->search.'%');
            });
        }

        $equipos = $query->orderBy('nombre')->paginate(10)->withQueryString();

        // Para cada equipo no disponible, obtener la asignación activa
        $equipos->each(function ($eq) {
            if (!$eq->disponible) {
                $eq->asignacion_activa = PrestamoEquipo::with(['prestamoAula.aula', 'user'])
                    ->where('equipo_id', $eq->id)
                    ->whereIn('estado', ['aprobado', 'activo'])
                    ->latest()
                    ->first();
            }
        });

        return view('tecnico.inventario', compact('equipos'));
    }

    // ── Registrar nuevo equipo (TI) ──────────────────────────────
    public function storeEquipo(Request $request)
    {
        $data = $request->validate([
            'codigo_inventario'        => ['required', 'string', 'max:50', 'unique:equipos,codigo_inventario'],
            'nombre'                   => ['required', 'string', 'max:100'],
            'marca'                    => ['nullable', 'string', 'max:80'],
            'modelo'                   => ['nullable', 'string', 'max:80'],
            'descripcion'              => ['nullable', 'string', 'max:255'],
            'estado_fisico'            => ['required', 'in:bueno,regular,dañado,dado_de_baja'],
            'ubicacion_almacenamiento' => ['nullable', 'string', 'max:150'],
            'fecha_adquisicion'        => ['nullable', 'date'],
        ]);

        $disponible = !in_array($data['estado_fisico'], ['dañado', 'dado_de_baja']);

        Equipo::create($data + ['disponible' => $disponible, 'activo' => true]);

        return redirect()->route('tecnico.inventario')
                         ->with('success', "Equipo '{$data['nombre']}' registrado correctamente.");
    }

    // ── Cambiar estado físico / disponibilidad (TI) ───────────────
    public function cambiarEstado(Request $request, Equipo $equipo)
    {
        $data = $request->validate([
            'estado_fisico'            => ['required', 'in:bueno,regular,dañado,dado_de_baja'],
            'observacion'              => ['nullable', 'string', 'max:200'],
            'ubicacion_almacenamiento' => ['nullable', 'string', 'max:150'],
        ]);

        $tienePrestamoActivo = PrestamoEquipo::where('equipo_id', $equipo->id)
            ->whereIn('estado', ['aprobado', 'activo'])
            ->exists();

        if ($tienePrestamoActivo) {
            return redirect()->route('tecnico.inventario')
                ->with('error', "No se puede cambiar el estado de '{$equipo->nombre}' mientras esté en préstamo activo. Espere a que sea devuelto.");
        }

        $estadoAnterior  = $equipo->estado_fisico;
        $disponibleAntes = $equipo->disponible;

        $nuevoDisponible = !in_array($data['estado_fisico'], ['dañado', 'dado_de_baja']);

        $equipo->update([
            'estado_fisico'            => $data['estado_fisico'],
            'disponible'               => $nuevoDisponible,
            'ubicacion_almacenamiento' => $data['ubicacion_almacenamiento'] ?? $equipo->ubicacion_almacenamiento,
            'descripcion'              => $data['observacion'] ?? $equipo->descripcion,
        ]);

        DB::table('historial_movimientos')->insert([
            'user_id'      => Auth::id(),
            'entidad_tipo' => Equipo::class,
            'entidad_id'   => $equipo->id,
            'tipo_accion'  => 'cambio_estado_equipo',
            'descripcion'  => "TI cambió estado de '{$equipo->nombre}': {$estadoAnterior} → {$data['estado_fisico']}."
                              .($data['observacion'] ? " Obs: {$data['observacion']}" : ''),
            'payload'      => json_encode([
                'estado_anterior'   => $estadoAnterior,
                'estado_nuevo'      => $data['estado_fisico'],
                'disponible_antes'  => $disponibleAntes,
                'disponible_ahora'  => $nuevoDisponible,
                'observacion'       => $data['observacion'] ?? null,
            ]),
            'created_at'   => now(),
        ]);

        return redirect()->route('tecnico.inventario')
                         ->with('success', "Estado de '{$equipo->nombre}' actualizado a '{$data['estado_fisico']}'.");
    }
}
