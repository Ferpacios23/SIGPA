<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones_prestamos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()
                  ->comment('Usuario destinatario de la notificación');

            // Polimorfismo para apuntar a cualquier tipo de préstamo
            $table->string('notificable_tipo', 80)->nullable();
            $table->unsignedBigInteger('notificable_id')->nullable();

            $table->enum('tipo', [
                'aprobacion',
                'rechazo',
                'recordatorio',
                'vencimiento',
                'novedad',
                'liberacion_automatica',
                'devolucion_pendiente',
            ]);

            $table->string('titulo', 150);
            $table->text('mensaje');
            $table->timestamp('leida_en')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'leida_en']);
            $table->index(['notificable_tipo', 'notificable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones_prestamos');
    }
};