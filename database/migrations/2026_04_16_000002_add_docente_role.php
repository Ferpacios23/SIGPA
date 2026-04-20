<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->insertOrIgnore([
            'nombre'      => 'Docente',
            'slug'        => 'docente',
            'descripcion' => 'Docente con acceso a solicitar aulas',
            'activo'      => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('roles')->where('slug', 'docente')->delete();
    }
};
