<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->restrictOnDelete();
            $table->string('identificacion', 20)->unique()->nullable()->comment('Cédula o documento de identidad');
            $table->string('telefono', 20)->nullable();
            $table->string('dependencia', 100)->nullable()->comment('Facultad, área o departamento');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('user_id');
            $table->index('role_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};