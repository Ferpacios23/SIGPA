<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipo extends Model
{
    use SoftDeletes;
 
    protected $table = 'equipos';
 
    protected $fillable = [
        'codigo_inventario',
        'nombre',
        'marca',
        'modelo',
        'descripcion',
        'estado_fisico',
        'disponible',
        'ubicacion_almacenamiento',
        'fecha_adquisicion',
        'activo',
    ];
 
    protected $casts = [
        'disponible'       => 'boolean',
        'activo'           => 'boolean',
        'fecha_adquisicion'=> 'date',
    ];
 
    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true)->where('activo', true);
    }

    public function prestamos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PrestamoEquipo::class);
    }

    public function prestamosActivos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PrestamoEquipo::class)
                    ->whereIn('estado', ['aprobado', 'activo']);
    }
}

