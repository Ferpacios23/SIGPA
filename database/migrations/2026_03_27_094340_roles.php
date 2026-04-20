<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique();
            $table->string('slug', 50)->unique();
            $table->string('descripcion', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Seed roles iniciales
        DB::table('roles')->insert([
            ['nombre' => 'Administrador', 'slug' => 'admin',      'descripcion' => 'Control total del sistema',               'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Secretaría',    'slug' => 'secretaria', 'descripcion' => 'Gestión de préstamos de aulas',           'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Técnico TI',    'slug' => 'tecnico',    'descripcion' => 'Gestión de equipos tecnológicos',         'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
