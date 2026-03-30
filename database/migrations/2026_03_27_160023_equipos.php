<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_inventario', 50)->unique()->comment('Código único de inventario institucional');
            $table->string('nombre', 100)->comment('Ej: Video Beam, Cable HDMI, Micrófono');
            $table->string('marca', 80)->nullable();
            $table->string('modelo', 80)->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->enum('estado_fisico', ['bueno', 'regular', 'dañado', 'dado_de_baja'])->default('bueno');
            $table->boolean('disponible')->default(true)->comment('Disponibilidad para préstamo');
            $table->string('ubicacion_almacenamiento', 150)->nullable()->comment('Lugar físico donde se guarda el equipo');
            $table->date('fecha_adquisicion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('disponible');
            $table->index('estado_fisico');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};