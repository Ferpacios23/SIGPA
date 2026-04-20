<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Esta migración fue duplicada por error; se omite para no recrear la tabla existente.
        if (Schema::hasTable('prestamos_aulas')) {
            return;
        }
        Schema::create('prestamos_aulas', function (Blueprint $table) {
            $table->id();

            // Relaciones principales
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete()
                  ->comment('Usuario que solicita el equipo');
            $table->foreignId('aula_id')->constrained('aulas')->restrictOnDelete();
            $table->foreignId('aprobado_por')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('Secretaria o admin que aprueba el préstamo');

            // Datos del préstamo
            $table->date('fecha_prestamo')->comment('Fecha en que se realizará el préstamo');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->unsignedSmallInteger('tolerancia_minutos')->default(15)
                  ->comment('Minutos de gracia antes de liberar el aula automáticamente');

            // Estado y confirmación
            $table->enum('estado', ['pendiente', 'aprobado', 'activo', 'finalizado', 'cancelado', 'liberado_por_tolerancia'])
                  ->default('pendiente');
            $table->boolean('asistencia_confirmada')->default(false)
                  ->comment('El usuario confirmó presencia dentro del tiempo de tolerancia');
            $table->timestamp('asistencia_confirmada_en')->nullable()
                  ->comment('Timestamp exacto de la confirmación de asistencia');

            // Metadatos
            $table->string('motivo', 255)->nullable()->comment('Motivo o descripción de uso del aula');
            $table->text('observaciones')->nullable();
            $table->timestamp('cancelado_en')->nullable();
            $table->string('motivo_cancelacion', 255)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['aula_id', 'fecha_prestamo', 'estado']);
            $table->index(['user_id', 'estado']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamos_aulas');
    }
};