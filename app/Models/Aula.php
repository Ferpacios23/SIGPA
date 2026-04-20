<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Aula extends Model
{
        use SoftDeletes;
 
    protected $table = 'aulas';
 
    protected $fillable = [
        'codigo',
        'nombre',
        'capacidad',
        'ubicacion',
        'descripcion',
        'estado',
        'activa',
    ];
 
    protected $casts = [
        'capacidad' => 'integer',
        'activa'    => 'boolean',
    ];
 
    // Scopes útiles
    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'disponible')->where('activa', true);
    }
 
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }
 
    public function prestamos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PrestamoAula::class);
    }
 
    public function prestamosActivos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PrestamoAula::class)->whereIn('estado', ['aprobado', 'activo']);
    }

    public function horariosAcademicos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HorarioAcademico::class);
    }
}
