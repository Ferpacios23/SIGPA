<?php
// app/Http/Controllers/Auth/AuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ]);
        }

        $request->session()->regenerate();

        return $this->redirectByRole(Auth::user());
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    protected function redirectByRole($user)
    {
        // Cargamos profile → role en una sola consulta
        $user->loadMissing('profile.role');

        $slug = $user->profile?->role?->slug;

        if (! $slug) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Tu cuenta no tiene un rol asignado. Contacta al administrador.');
        }

        // Mapa slug → nombre de ruta exacto definido en web.php
        $routes = [
            'admin'      => 'dashboard.admin',       // del grupo admin
            'secretaria' => 'secretaria.dashboard',  // del grupo secretaria
            'tecnico'    => 'tecnico.dashboard',      // del grupo tecnico
            'docente'    => 'docente.dashboard',      // del grupo docente
        ];
 
        $route = $routes[$slug] ?? null;
 
        if (! $route || ! \Illuminate\Support\Facades\Route::has($route)) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', "Rol '{$slug}' no tiene una vista asignada.");
        }
 
        return redirect()->route($route);



    }
}