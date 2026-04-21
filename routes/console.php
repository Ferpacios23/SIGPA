<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Para activar en producción, configurar el cron:
//   * * * * * cd /ruta/proyecto && php artisan schedule:run >> /dev/null 2>&1

// Ocupa/libera aulas según horarios académicos y préstamos activos
Schedule::command('sigpa:sincronizar-aulas')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/sincronizar-aulas.log'));

// Cancela préstamos sin check-in pasado el tiempo de tolerancia
Schedule::command('sigpa:verificar-asistencia')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/verificar-asistencia.log'));
