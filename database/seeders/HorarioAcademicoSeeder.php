<?php

namespace Database\Seeders;

use App\Models\HorarioAcademico;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HorarioAcademicoSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        HorarioAcademico::truncate();

        // Periodo académico vigente: Feb – Jun 2026
        $inicio = '2026-02-02';
        $fin    = '2026-06-27';

        // IDs obtenidos de la BD: docentes 16 (carlos), 17 (manuel), 18 (carlos2)
        //                          aulas 1 (sala 101), 2 (sala 102)
        $horarios = [
            // ── Carlos (id 16) ─────────────────────────────────────────
            ['docente_id' => 16, 'aula_id' => 1, 'materia' => 'Cálculo I',         'grupo' => '1A', 'dia_semana' => 'lunes',     'hora_inicio' => '07:00', 'hora_fin' => '09:00'],
            ['docente_id' => 16, 'aula_id' => 2, 'materia' => 'Cálculo I',         'grupo' => '1B', 'dia_semana' => 'miercoles', 'hora_inicio' => '07:00', 'hora_fin' => '09:00'],
            ['docente_id' => 16, 'aula_id' => 1, 'materia' => 'Álgebra Lineal',    'grupo' => '2A', 'dia_semana' => 'martes',    'hora_inicio' => '09:00', 'hora_fin' => '11:00'],
            ['docente_id' => 16, 'aula_id' => 2, 'materia' => 'Álgebra Lineal',    'grupo' => '2B', 'dia_semana' => 'jueves',    'hora_inicio' => '09:00', 'hora_fin' => '11:00'],
            ['docente_id' => 16, 'aula_id' => 1, 'materia' => 'Estadística',       'grupo' => '3A', 'dia_semana' => 'viernes',   'hora_inicio' => '14:00', 'hora_fin' => '16:00'],

            // ── Manuel (id 17) ─────────────────────────────────────────
            ['docente_id' => 17, 'aula_id' => 2, 'materia' => 'Programación I',    'grupo' => '1C', 'dia_semana' => 'lunes',     'hora_inicio' => '09:00', 'hora_fin' => '11:00'],
            ['docente_id' => 17, 'aula_id' => 1, 'materia' => 'Programación I',    'grupo' => '1D', 'dia_semana' => 'miercoles', 'hora_inicio' => '09:00', 'hora_fin' => '11:00'],
            ['docente_id' => 17, 'aula_id' => 2, 'materia' => 'Base de Datos',     'grupo' => '3B', 'dia_semana' => 'martes',    'hora_inicio' => '14:00', 'hora_fin' => '16:00'],
            ['docente_id' => 17, 'aula_id' => 1, 'materia' => 'Base de Datos',     'grupo' => '3C', 'dia_semana' => 'jueves',    'hora_inicio' => '14:00', 'hora_fin' => '16:00'],
            ['docente_id' => 17, 'aula_id' => 2, 'materia' => 'Redes I',           'grupo' => '4A', 'dia_semana' => 'sabado',    'hora_inicio' => '07:00', 'hora_fin' => '10:00'],

            // ── Carlos2 (id 18) ────────────────────────────────────────
            ['docente_id' => 18, 'aula_id' => 1, 'materia' => 'Física I',          'grupo' => '1E', 'dia_semana' => 'lunes',     'hora_inicio' => '14:00', 'hora_fin' => '16:00'],
            ['docente_id' => 18, 'aula_id' => 2, 'materia' => 'Física I',          'grupo' => '1F', 'dia_semana' => 'miercoles', 'hora_inicio' => '14:00', 'hora_fin' => '16:00'],
            ['docente_id' => 18, 'aula_id' => 1, 'materia' => 'Termodinámica',     'grupo' => '2C', 'dia_semana' => 'martes',    'hora_inicio' => '07:00', 'hora_fin' => '09:00'],
            ['docente_id' => 18, 'aula_id' => 2, 'materia' => 'Termodinámica',     'grupo' => '2D', 'dia_semana' => 'viernes',   'hora_inicio' => '07:00', 'hora_fin' => '09:00'],
            ['docente_id' => 18, 'aula_id' => 1, 'materia' => 'Lab. Física',       'grupo' => '2E', 'dia_semana' => 'sabado',    'hora_inicio' => '10:00', 'hora_fin' => '13:00'],
        ];

        foreach ($horarios as $h) {
            HorarioAcademico::create($h + [
                'fecha_inicio' => $inicio,
                'fecha_fin'    => $fin,
                'activo'       => true,
            ]);
        }
    }
}
