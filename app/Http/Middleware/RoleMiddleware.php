<?php
// app/Http/Middleware/RoleMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Cargamos profile → role si no están cargados
        $user->loadMissing('profile.role');

        $slug = $user->profile?->role?->slug;

        if (! $slug) {
            abort(403, 'Tu cuenta no tiene un rol asignado. Contacta al administrador.');
        }

        if (! in_array($slug, $roles)) {
            // Redirige a su propio dashboard en lugar de mostrar 403
            return redirect()->route('dashboard.' . $slug)
                ->with('error', 'No tienes permiso para acceder a esa sección.');
        }

        return $next($request);
    }
}