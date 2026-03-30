<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aulas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique()->comment('Ej: A101, B203');
            $table->string('nombre', 100)->nullable()->comment('Nombre descriptivo opcional');
            $table->unsignedSmallInteger('capacidad')->default(0)->comment('Número máximo de personas');
            $table->string('ubicacion', 150)->nullable()->comment('Bloque, piso o edificio');
            $table->string('descripcion', 255)->nullable();
            $table->enum('estado', ['disponible', 'ocupada', 'en_mantenimiento', 'inactiva'])->default('disponible');
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('estado');
            $table->index('activa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aulas');
    }
};