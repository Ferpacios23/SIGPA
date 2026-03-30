<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_movimientos', function (Blueprint $table) {
            $table->id();

            // Actor de la acción
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('Usuario que ejecutó la acción (null si fue sistema automático)');

            // Polimorfismo: apunta a préstamo de aula o de equipo
            $table->string('entidad_tipo', 80)->comment('App\\Models\\PrestamoAula | App\\Models\\PrestamoEquipo');
            $table->unsignedBigInteger('entidad_id')->comment('ID del registro afectado');

            // Detalle del movimiento
            $table->enum('tipo_accion', [
                'solicitud_aula',
                'aprobacion_aula',
                'cancelacion_aula',
                'confirmacion_asistencia',
                'liberacion_por_tolerancia',
                'finalizacion_aula',
                'prestamo_equipo',
                'devolucion_equipo',
                'registro_novedad',
                'cambio_estado_equipo',
                'cambio_estado_aula',
                'accion_sistema',
            ]);

            $table->string('descripcion', 255)->comment('Descripción legible de la acción realizada');
            $table->json('payload')->nullable()
                  ->comment('Snapshot JSON del estado anterior y posterior del registro');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            // Sin softDeletes: el historial es inmutable
            $table->timestamp('created_at')->useCurrent();

            // Índices
            $table->index(['entidad_tipo', 'entidad_id']);
            $table->index(['user_id', 'tipo_accion']);
            $table->index('tipo_accion');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_movimientos');
    }
};