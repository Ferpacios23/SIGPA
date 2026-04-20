<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('prestamos_equipos')) {
            return;
        }

        Schema::create('prestamos_equipos', function (Blueprint $table) {
            $table->id();

            // Relaciones principales
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete()
                  ->comment('Docente o usuario que solicita el equipo');
            $table->foreignId('equipo_id')->constrained('equipos')->restrictOnDelete();
            $table->foreignId('prestamo_aula_id')->nullable()
                  ->constrained('prestamos_aulas')->nullOnDelete()
                  ->comment('Préstamo de aula al que se vincula (opcional)');
            $table->foreignId('aprobado_por')->nullable()
                  ->constrained('users')->nullOnDelete()
                  ->comment('Secretaria o admin que aprueba');

            // Datos del préstamo
            $table->date('fecha_prestamo');
            $table->time('hora_inicio');
            $table->time('hora_fin');

            // Estado
            $table->enum('estado', [
                'pendiente',
                'aprobado',
                'activo',
                'finalizado',
                'cancelado',
            ])->default('pendiente');

            // Confirmación de entrega/devolución
            $table->boolean('entregado')->default(false)
                  ->comment('El equipo fue entregado al solicitante');
            $table->timestamp('entregado_en')->nullable();
            $table->boolean('devuelto')->default(false)
                  ->comment('El equipo fue devuelto al técnico');
            $table->timestamp('devuelto_en')->nullable();
            $table->string('estado_devolucion', 50)->nullable()
                  ->comment('Estado físico al devolver: bueno, regular, dañado');

            // Metadatos
            $table->string('motivo', 255)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamp('cancelado_en')->nullable();
            $table->string('motivo_cancelacion', 255)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['equipo_id', 'fecha_prestamo', 'estado']);
            $table->index(['user_id', 'estado']);
            $table->index(['prestamo_aula_id']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamos_equipos');
    }
};
