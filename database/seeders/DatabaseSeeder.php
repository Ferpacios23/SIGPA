<?php

/* Este seeder es un punto de partida para poblar la base de datos con datos de prueba.
Puedes personalizarlo para crear roles, usuarios con perfiles completos, equipos y préstamos de ejemplo.
Recuerda ejecutar `php artisan db:seed` después de configurar este seeder para llenar tu base de datos. */


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\UserProfile;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    // Arreglo con los datos de los usuarios
    $usuarios = [
        [
            'name'  => 'Tecnico Principal',
            'email' => 'tecnico3@sigpa.edu.co',
            'role'  => 'tecnico',
            'identificacion' => '1776341015',
            'telefono' => '1234567890',
            'dependencia' => 'Departamento de Tecnología'
        ],
        [
            'name'  => 'docente',
            'email' => 'docente4@sigpa.edu.co',
            'role'  => 'docente',
            'identificacion' => '1077631016',
            'telefono' => '0987654321',
            'dependencia' => 'Facultad de Ingeniería'
        ],
        [
            'name'  => 'Secretaria',
            'email' => 'secretaria2@sigpa.edu.co',
            'role'  => 'secretaria',
            'identificacion' => '1077631017',
            'telefono' => '1122334455',
            'dependencia' => 'Facultad de Ciencias'
        ],
        [
            'name'  => 'administrador',
            'email' => 'admin2@sigpa.edu.co',
            'role'  => 'admin',
            'identificacion' => '1077631018',
            'telefono' => '5566778899',
            'dependencia' => 'Administración Central'
        ],
        
    ];

    foreach ($usuarios as $u) {
        $user = User::create([
            'name'     => $u['name'],
            'email'    => $u['email'],
            'password' => Hash::make('password123'),
        ]);

        $role = Role::where('slug', $u['role'])->first();

        if ($role) {
            UserProfile::create([
                'user_id'        => $user->id,
                'role_id'        => $role->id,
                'identificacion' => $u['identificacion'],
                'telefono'       => $u['telefono'] ?? null,
                'dependencia'    => $u['role'] ?? null,
                'activo'         => true,
            ]);
        }
    }
}
}   