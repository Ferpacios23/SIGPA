<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ChangePasswordController extends Controller
{
    public function show()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers(),
            ],
        ], [
            'password.required'  => 'La nueva contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $user = Auth::user();

        $user->update([
            'password'             => $request->password,
            'must_change_password' => false,
        ]);

        $slug = $user->profile?->role?->slug;
        $routes = [
            'admin'      => 'dashboard.admin',
            'secretaria' => 'secretaria.dashboard',
            'tecnico'    => 'tecnico.dashboard',
            'docente'    => 'docente.dashboard',
        ];

        $route = $routes[$slug] ?? 'login';

        return redirect()->route($route)
                         ->with('success', 'Contraseña actualizada correctamente. ¡Bienvenido al sistema!');
    }
}
