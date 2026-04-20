<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PrestamoAula extends Model
{
        use SoftDeletes;
 
    protected $table = 'prestamos_aulas';
 
    protected $fillable = [
        'user_id',
        'aula_id',
        'aprobado_por',
        'fecha_prestamo',
        'hora_inicio',
        'hora_fin',
        'tolerancia_minutos',
        'estado',
        'asistencia_confirmada',
        'asistencia_confirmada_en',
        'motivo',
        'observaciones',
        'cancelado_en',
        'motivo_cancelacion',
    ];
 
    protected $casts = [
        'fecha_prestamo'           => 'date',
        'asistencia_confirmada'    => 'boolean',
        'asistencia_confirmada_en' => 'datetime',
        'cancelado_en'             => 'datetime',
    ];
 
    // Scopes
    public function scopeActivos($query)
    {
        return $query->whereIn('estado', ['aprobado', 'activo']);
    }
 
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }
 
    public function scopeDeHoy($query)
    {
        return $query->whereDate('fecha_prestamo', today());
    }
 
    // Relaciones
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
 
    public function aula(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Aula::class);
    }
 
    public function aprobadoPor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function prestamosEquipos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PrestamoEquipo::class, 'prestamo_aula_id');
    }
}
