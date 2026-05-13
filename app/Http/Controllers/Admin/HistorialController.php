<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HistorialMovimiento;
use App\Models\PrestamoAula;
use App\Models\User;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    /** RF31 — Historial completo de préstamos de aulas */
    public function prestamos(Request $request)
    {
        $desde   = $request->get('desde', now()->subMonth()->toDateString());
        $hasta   = $request->get('hasta', now()->toDateString());
        $estado  = $request->get('estado');
        $usuario = $request->get('usuario');

        $query = PrestamoAula::with(['user', 'aula', 'aprobadoPor'])
            ->whereBetween('fecha_prestamo', [$desde, $hasta]);

        if ($estado) {
            $query->where('estado', $estado);
        }

        if ($usuario) {
            $query->whereHas('user', fn($q) =>
                $q->where('name', 'like', "%{$usuario}%")
                  ->orWhere('email', 'like', "%{$usuario}%")
            );
        }

        $prestamos = $query->orderByDesc('fecha_prestamo')->paginate(20)->withQueryString();

        $estados = [
            'pendiente', 'aprobado', 'activo',
            'finalizado', 'cancelado', 'liberado_por_tolerancia',
        ];

        return view('admin.historial.prestamos',
            compact('prestamos', 'desde', 'hasta', 'estado', 'usuario', 'estados')
        );
    }

    /** RF33 — Historial de accesos (login / logout) */
    public function accesos(Request $request)
    {
        $desde = $request->get('desde', now()->subMonth()->toDateString());
        $hasta = $request->get('hasta', now()->toDateString());
        $tipo  = $request->get('tipo'); // 'login' | 'logout'

        $query = HistorialMovimiento::with('user')
            ->whereIn('tipo_accion', ['acceso_login', 'acceso_logout'])
            ->whereBetween('created_at', [
                $desde . ' 00:00:00',
                $hasta . ' 23:59:59',
            ]);

        if (in_array($tipo, ['login', 'logout'])) {
            $query->where('tipo_accion', 'acceso_' . $tipo);
        }

        $accesos = $query->orderByDesc('created_at')->paginate(25)->withQueryString();

        return view('admin.historial.accesos', compact('accesos', 'desde', 'hasta', 'tipo'));
    }

    /** Actividad completa del área TI */
    public function actividadTI(Request $request)
    {
        $desde   = $request->input('desde', now()->subMonth()->toDateString());
        $hasta   = $request->input('hasta', now()->toDateString());
        $accion  = $request->input('accion');
        $usuario = $request->input('usuario');

        // IDs de usuarios con rol 'tecnico'
        $idsTecnicos = User::whereHas('profile.role', fn($q) => $q->where('slug', 'tecnico'))
            ->pluck('id');

        $accionesTI = [
            'creacion_equipo',
            'cambio_estado_equipo',
            'asignacion_ti',
            'devolucion_ti',
        ];

        $query = HistorialMovimiento::with('user')
            ->whereIn('user_id', $idsTecnicos)
            ->whereIn('tipo_accion', $accionesTI)
            ->whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59']);

        if ($accion && in_array($accion, $accionesTI)) {
            $query->where('tipo_accion', $accion);
        }

        if ($usuario) {
            $ids = User::where('name', 'like', "%{$usuario}%")
                ->orWhere('email', 'like', "%{$usuario}%")
                ->pluck('id');
            $query->whereIn('user_id', $ids);
        }

        $registros = $query->orderByDesc('created_at')->paginate(25)->withQueryString();

        $resumen = [
            'creacion_equipo'   => HistorialMovimiento::whereIn('user_id', $idsTecnicos)
                ->where('tipo_accion', 'creacion_equipo')
                ->whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])->count(),
            'cambio_estado'     => HistorialMovimiento::whereIn('user_id', $idsTecnicos)
                ->where('tipo_accion', 'cambio_estado_equipo')
                ->whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])->count(),
            'asignacion'        => HistorialMovimiento::whereIn('user_id', $idsTecnicos)
                ->where('tipo_accion', 'asignacion_ti')
                ->whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])->count(),
            'devolucion'        => HistorialMovimiento::whereIn('user_id', $idsTecnicos)
                ->where('tipo_accion', 'devolucion_ti')
                ->whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])->count(),
        ];

        return view('admin.historial.actividad_ti',
            compact('registros', 'desde', 'hasta', 'accion', 'usuario', 'accionesTI', 'resumen')
        );
    }

    /** RF35 — Cancelaciones por inasistencia */
    public function cancelaciones(Request $request)
    {
        $desde = $request->get('desde', now()->subMonth()->toDateString());
        $hasta = $request->get('hasta', now()->toDateString());

        $cancelaciones = PrestamoAula::with(['user', 'aula'])
            ->whereIn('estado', ['cancelado', 'liberado_por_tolerancia'])
            ->whereBetween('fecha_prestamo', [$desde, $hasta])
            ->orderByDesc('fecha_prestamo')
            ->paginate(20)
            ->withQueryString();

        $totales = [
            'inasistencia' => PrestamoAula::whereBetween('fecha_prestamo', [$desde, $hasta])
                ->where('estado', 'liberado_por_tolerancia')->count(),
            'cancelado'    => PrestamoAula::whereBetween('fecha_prestamo', [$desde, $hasta])
                ->where('estado', 'cancelado')->count(),
        ];

        return view('admin.historial.cancelaciones',
            compact('cancelaciones', 'desde', 'hasta', 'totales')
        );
    }
}
