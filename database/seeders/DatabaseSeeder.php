<?php

/* Este seeder es un punto de partida para poblar la base de datos con datos de prueba.
Puedes personalizarlo para crear roles, usuarios con perfiles completos, equipos y préstamos de ejemplo.
Recuerda ejecutar `php artisan db:seed` después de configurar este seeder para llenar tu base de datos. */



namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    
    public function run(): void
    {

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'remember_token'=> '1233444555'
        ]);

        User::factory(10)->create();
    }
}
