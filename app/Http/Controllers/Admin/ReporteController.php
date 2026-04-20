<?php


namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\Aula;
use App\Models\Equipo;
use App\Models\PrestamoAula;
use App\Models\User;
use Illuminate\Http\Request;
 
class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $desde = $request->get('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->get('hasta', now()->toDateString());
 
        $prestamos = PrestamoAula::with(['user', 'aula', 'aprobadoPor'])
            ->whereBetween('fecha_prestamo', [$desde, $hasta])
            ->latest()
            ->paginate(20);
 
        $resumen = [
            'total'      => PrestamoAula::whereBetween('fecha_prestamo', [$desde, $hasta])->count(),
            'aprobados'  => PrestamoAula::whereBetween('fecha_prestamo', [$desde, $hasta])->where('estado', 'aprobado')->count(),
            'cancelados' => PrestamoAula::whereBetween('fecha_prestamo', [$desde, $hasta])->where('estado', 'cancelado')->count(),
            'finalizados'=> PrestamoAula::whereBetween('fecha_prestamo', [$desde, $hasta])->where('estado', 'finalizado')->count(),
        ];
 
        $aulasMasUsadas = Aula::withCount(['prestamos' => function ($q) use ($desde, $hasta) {
            $q->whereBetween('fecha_prestamo', [$desde, $hasta]);
        }])->orderByDesc('prestamos_count')->take(5)->get();
 
        return view('admin.reportes.index', compact('prestamos', 'resumen', 'aulasMasUsadas', 'desde', 'hasta'));
    }
 
    public function export(Request $request)
    {
        // Aquí puedes integrar Laravel Excel o generar CSV simple
        $desde = $request->get('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->get('hasta', now()->toDateString());
 
        $prestamos = PrestamoAula::with(['user', 'aula'])
            ->whereBetween('fecha_prestamo', [$desde, $hasta])
            ->get();
 
        $csv  = "ID,Usuario,Correo,Aula,Fecha,Inicio,Fin,Estado\n";
        foreach ($prestamos as $p) {
            $csv .= implode(',', [
                $p->id,
                '"' . ($p->user->name ?? '') . '"',
                $p->user->email ?? '',
                $p->aula->codigo ?? '',
                $p->fecha_prestamo,
                substr($p->hora_inicio, 0, 5),
                substr($p->hora_fin, 0, 5),
                $p->estado,
            ]) . "\n";
        }
 
        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=reporte_{$desde}_{$hasta}.csv",
        ]);
    }
}
 