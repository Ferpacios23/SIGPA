<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrestamoEquipo extends Model
{
    use SoftDeletes;

    protected $table = 'prestamos_equipos';

    protected $fillable = [
        'user_id',
        'equipo_id',
        'prestamo_aula_id',
        'aprobado_por',
        'fecha_prestamo',
        'hora_inicio',
        'hora_fin',
        'estado',
        'entregado',
        'entregado_en',
        'devuelto',
        'devuelto_en',
        'estado_devolucion',
        'motivo',
        'observaciones',
        'cancelado_en',
        'motivo_cancelacion',
    ];

    protected $casts = [
        'fecha_prestamo' => 'date',
        'entregado'      => 'boolean',
        'entregado_en'   => 'datetime',
        'devuelto'       => 'boolean',
        'devuelto_en'    => 'datetime',
        'cancelado_en'   => 'datetime',
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

    public function equipo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function prestamoAula(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PrestamoAula::class, 'prestamo_aula_id');
    }

    public function aprobadoPor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }
}
