<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios_academicos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('docente_id')
                  ->constrained('users')
                  ->cascadeOnDelete()
                  ->comment('Docente al que pertenece el horario');

            $table->foreignId('aula_id')
                  ->constrained('aulas')
                  ->restrictOnDelete();

            $table->string('materia', 150)->comment('Nombre de la asignatura');
            $table->string('grupo', 30)->nullable()->comment('Ej: 2A, 3B');

            $table->enum('dia_semana', ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado']);
            $table->time('hora_inicio');
            $table->time('hora_fin');

            $table->date('fecha_inicio')->comment('Inicio del periodo académico');
            $table->date('fecha_fin')->comment('Fin del periodo académico');

            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['aula_id', 'dia_semana', 'activo']);
            $table->index(['docente_id', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios_academicos');
    }
};
