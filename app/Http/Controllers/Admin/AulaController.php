<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use App\Models\HorarioAcademico;
use App\Models\User;
use Illuminate\Http\Request;

class AulaController extends Controller
{
    public function index()
    {
        $aulas = Aula::withCount(['horariosAcademicos as horarios_activos_count' => function ($q) {
                        $q->where('activo', true);
                    }])
                    ->latest()
                    ->paginate(20);

        return view('admin.aulas.index', compact('aulas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo'      => ['required', 'string', 'max:20', 'unique:aulas'],
            'nombre'      => ['nullable', 'string', 'max:100'],
            'capacidad'   => ['required', 'integer', 'min:1'],
            'ubicacion'   => ['nullable', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'estado'      => ['required', 'in:disponible,ocupada,en_mantenimiento,inactiva'],
        ]);
        Aula::create($data + ['activa' => true]);
        return redirect()->route('admin.aulas.index')->with('success', 'Aula creada correctamente.');
    }

    public function update(Request $request, Aula $aula)
    {
        $data = $request->validate([
            'codigo'      => ['required', 'string', 'max:20', "unique:aulas,codigo,{$aula->id}"],
            'nombre'      => ['nullable', 'string', 'max:100'],
            'capacidad'   => ['required', 'integer', 'min:1'],
            'ubicacion'   => ['nullable', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'estado'      => ['required', 'in:disponible,ocupada,en_mantenimiento,inactiva'],
            'activa'      => ['boolean'],
        ]);
        $aula->update($data);
        return redirect()->route('admin.aulas.index')->with('success', 'Aula actualizada.');
    }

    public function destroy(Aula $aula)
    {
        $aula->delete();
        return redirect()->route('admin.aulas.index')->with('success', 'Aula eliminada.');
    }

    // ── Gestión de horario académico de un aula ───────────────────
    public function horarios(Aula $aula)
    {
        $horarios = HorarioAcademico::with('docente')
            ->where('aula_id', $aula->id)
            ->orderByRaw("FIELD(dia_semana,'lunes','martes','miercoles','jueves','viernes','sabado')")
            ->orderBy('hora_inicio')
            ->get();

        $docentes = User::whereHas('profile.role', fn($q) => $q->where('slug', 'docente'))
                        ->orderBy('name')
                        ->get();

        // Agrupar por día para la grilla visual
        $porDia = $horarios->groupBy('dia_semana');

        return view('admin.aulas.horarios', compact('aula', 'horarios', 'docentes', 'porDia'));
    }

    // Store horario desde la vista de aula (redirige de vuelta al aula)
    public function storeHorario(Request $request, Aula $aula)
    {
        $data = $request->validate([
            'docente_id'   => ['required', 'exists:users,id'],
            'materia'      => ['required', 'string', 'max:150'],
            'grupo'        => ['nullable', 'string', 'max:30'],
            'dia_semana'   => ['required', 'in:lunes,martes,miercoles,jueves,viernes,sabado'],
            'hora_inicio'  => ['required', 'date_format:H:i'],
            'hora_fin'     => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'    => ['required', 'date', 'after_or_equal:fecha_inicio'],
        ]);

        $conflicto = HorarioAcademico::where('aula_id', $aula->id)
            ->where('dia_semana', $data['dia_semana'])
            ->where('activo', true)
            ->where('fecha_inicio', '<=', $data['fecha_fin'])
            ->where('fecha_fin',    '>=', $data['fecha_inicio'])
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
                ->with('error', 'Ya existe una clase programada en ese día y franja horaria para esta aula.');
        }

        HorarioAcademico::create($data + ['aula_id' => $aula->id, 'activo' => true]);

        return redirect()->route('admin.aulas.horarios', $aula)
            ->with('success', 'Clase registrada. El aula queda bloqueada en ese horario.');
    }

    // Eliminar horario desde la vista de aula
    public function destroyHorario(Aula $aula, HorarioAcademico $horario)
    {
        $horario->delete();
        return redirect()->route('admin.aulas.horarios', $aula)
            ->with('success', 'Bloque de horario eliminado. El aula queda disponible en esa franja.');
    }

    // Activar / desactivar horario
    public function toggleHorario(Aula $aula, HorarioAcademico $horario)
    {
        $horario->update(['activo' => !$horario->activo]);
        $estado = $horario->activo ? 'activado' : 'desactivado';
        return back()->with('success', "Horario {$estado} correctamente.");
    }
}