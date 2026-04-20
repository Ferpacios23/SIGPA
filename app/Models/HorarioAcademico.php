<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioAcademico extends Model
{
    protected $table = 'horarios_academicos';

    protected $fillable = [
        'docente_id',
        'aula_id',
        'materia',
        'grupo',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'fecha_inicio',
        'fecha_fin',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'activo'       => 'boolean',
    ];

    public function docente(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    public function aula(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Aula::class);
    }

    /**
     * Verifica si el horario aplica a una fecha y franja horaria dadas.
     */
    public static function aulaOcupadaEn(int $aulaId, string $fecha, string $horaInicio, string $horaFin): bool
    {
        $dias = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        $diaSemana = $dias[\Carbon\Carbon::parse($fecha)->dayOfWeek];

        return static::where('aula_id', $aulaId)
            ->where('dia_semana', $diaSemana)
            ->where('activo', true)
            ->where('fecha_inicio', '<=', $fecha)
            ->where('fecha_fin', '>=', $fecha)
            ->where(function ($q) use ($horaInicio, $horaFin) {
                $q->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                  ->orWhereBetween('hora_fin',  [$horaInicio, $horaFin])
                  ->orWhere(function ($q2) use ($horaInicio, $horaFin) {
                      $q2->where('hora_inicio', '<=', $horaInicio)
                         ->where('hora_fin',    '>=', $horaFin);
                  });
            })->exists();
    }
}
