<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\HorarioAcademico;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HorarioDocenteController extends Controller
{
    public function index(Request $request)
    {
        $docente = Auth::user();

        $offset = max(-52, min(52, (int) $request->get('semana', 0)));
        $lunes  = Carbon::now()->startOfWeek(Carbon::MONDAY)->addWeeks($offset);

        $nombresD    = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        $diasConFecha = [];
        for ($i = 0; $i < 6; $i++) {
            $diasConFecha[$nombresD[$i]] = $lunes->copy()->addDays($i);
        }

        $horariosSemana = [];
        foreach ($nombresD as $dia) {
            $fechaDia = $diasConFecha[$dia]->toDateString();

            $horariosSemana[$dia] = HorarioAcademico::with('aula')
                ->where('docente_id', $docente->id)
                ->where('dia_semana', $dia)
                ->where('activo', true)
                ->where('fecha_inicio', '<=', $fechaDia)
                ->where('fecha_fin',    '>=', $fechaDia)
                ->orderBy('hora_inicio')
                ->get();
        }

        $totalClases    = collect($horariosSemana)->flatten()->count();
        $esSemanaActual = $offset === 0;

        return view('docente.horario', compact(
            'horariosSemana', 'diasConFecha', 'nombresD',
            'totalClases', 'offset', 'esSemanaActual', 'lunes'
        ));
    }
}
