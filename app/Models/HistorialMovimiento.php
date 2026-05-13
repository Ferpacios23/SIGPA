<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class HistorialMovimiento extends Model
{
    public $timestamps = false;

    protected $table = 'historial_movimientos';

    protected $fillable = [
        'user_id', 'entidad_tipo', 'entidad_id', 'tipo_accion',
        'descripcion', 'payload', 'ip_address', 'user_agent', 'created_at',
    ];

    protected $casts = [
        'payload'    => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Inserta un registro de auditoría. El parámetro $entidad puede ser cualquier
     * Model de Eloquent; para eventos de acceso (login/logout) se pasa el User.
     */
    public static function registrar(
        string $tipoAccion,
        string $descripcion,
        Model $entidad,
        ?array $payload = null,
        ?int $userId = null
    ): void {
        self::create([
            'user_id'      => $userId ?? auth()->id(),
            'entidad_tipo' => get_class($entidad),
            'entidad_id'   => $entidad->getKey(),
            'tipo_accion'  => $tipoAccion,
            'descripcion'  => $descripcion,
            'payload'      => $payload ? json_encode($payload) : null,
            'ip_address'   => substr(Request::ip() ?? '', 0, 45),
            'user_agent'   => substr(Request::userAgent() ?? '', 0, 255),
            'created_at'   => now(),
        ]);
    }
}
