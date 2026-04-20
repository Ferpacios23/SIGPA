<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use App\Models\HorarioAcademico;
use App\Models\User;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function index(Request $request)
    {
        $query = HorarioAcademico::with(['docente', 'aula']);

        if ($request->filled('dia')) {
            $query->where('dia_semana', $request->dia);
        }
        if ($request->filled('aula_id')) {
            $query->where('aula_id', $request->aula_id);
        }
        if ($request->filled('docente_id')) {
            $query->where('docente_id', $request->docente_id);
        }

        $horarios = $query->orderByRaw("FIELD(dia_semana,'lunes','martes','miercoles','jueves','viernes','sabado')")
                          ->orderBy('hora_inicio')
                          ->paginate(20)
                          ->withQueryString();

        $docentes = User::whereHas('profile.role', fn($q) => $q->where('slug', 'docente'))
                        ->with('profile')
                        ->orderBy('name')
                        ->get();

        $aulas = Aula::where('activa', true)->orderBy('codigo')->get();

        return view('admin.horarios.index', compact('horarios', 'docentes', 'aulas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'docente_id'   => ['required', 'exists:users,id'],
            'aula_id'      => ['required', 'exists:aulas,id'],
            'materia'      => ['required', 'string', 'max:150'],
            'grupo'        => ['nullable', 'string', 'max:30'],
            'dia_semana'   => ['required', 'in:lunes,martes,miercoles,jueves,viernes,sabado'],
            'hora_inicio'  => ['required', 'date_format:H:i'],
            'hora_fin'     => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'    => ['required', 'date', 'after_or_equal:fecha_inicio'],
        ]);

        if ($this->tieneConflicto($data)) {
            return back()->withInput()
                ->with('error', 'El aula ya tiene un horario académico en ese día y franja horaria durante el periodo indicado.');
        }

        HorarioAcademico::create($data + ['activo' => true]);

        return redirect()->route('admin.horarios.index')
            ->with('success', 'Horario registrado correctamente. El aula quedará bloqueada para préstamos en ese horario.');
    }

    public function update(Request $request, HorarioAcademico $horario)
    {
        $data = $request->validate([
            'docente_id'   => ['required', 'exists:users,id'],
            'aula_id'      => ['required', 'exists:aulas,id'],
            'materia'      => ['required', 'string', 'max:150'],
            'grupo'        => ['nullable', 'string', 'max:30'],
            'dia_semana'   => ['required', 'in:lunes,martes,miercoles,jueves,viernes,sabado'],
            'hora_inicio'  => ['required', 'date_format:H:i'],
            'hora_fin'     => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'    => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'activo'       => ['boolean'],
        ]);

        if ($this->tieneConflicto($data, $horario->id)) {
            return back()->withInput()
                ->with('error', 'El aula ya tiene un horario académico en ese día y franja horaria durante el periodo indicado.');
        }

        $horario->update($data);

        return redirect()->route('admin.horarios.index')
            ->with('success', 'Horario actualizado correctamente.');
    }

    public function destroy(HorarioAcademico $horario)
    {
        $horario->delete();
        return redirect()->route('admin.horarios.index')
            ->with('success', 'Horario eliminado. El aula ya no estará bloqueada en ese horario.');
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function tieneConflicto(array $data, ?int $excludeId = null): bool
    {
        $query = HorarioAcademico::where('aula_id', $data['aula_id'])
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
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
