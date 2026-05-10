<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'                  => \App\Http\Middleware\RoleMiddleware::class,
            'nocache'               => \App\Http\Middleware\NoCacheHeaders::class,
            'check.password.change' => \App\Http\Middleware\CheckPasswordChange::class,
        ]);

        // Aplica no-cache a todas las rutas autenticadas y de invitado
        $middleware->appendToGroup('auth',  \App\Http\Middleware\NoCacheHeaders::class);
        $middleware->appendToGroup('guest', \App\Http\Middleware\NoCacheHeaders::class);

        // Fuerza cambio de contraseña en primer login (aplica a todas las rutas web)
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckPasswordChange::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();