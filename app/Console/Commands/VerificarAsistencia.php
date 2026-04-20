<?php

namespace App\Console\Commands;

use App\Models\PrestamoAula;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerificarAsistencia extends Command
{
    protected $signature   = 'sigpa:verificar-asistencia';
    protected $description = 'Cancela automáticamente préstamos sin check-in pasado el tiempo de tolerancia y libera el aula';

    public function handle(): int
    {
        $ahora     = now();
        $cancelados = 0;

        // Préstamos aprobados de hoy sin asistencia confirmada
        $prestamos = PrestamoAula::with('aula')
            ->where('estado', 'aprobado')
            ->where('asistencia_confirmada', false)
            ->whereDate('fecha_prestamo', today())
            ->get()
            ->filter(function (PrestamoAula $prestamo) use ($ahora) {
                // Límite = hora_inicio + tolerancia_minutos
                $limite = Carbon::parse(
                    $prestamo->fecha_prestamo->format('Y-m-d') . ' ' . $prestamo->hora_inicio
                )->addMinutes($prestamo->tolerancia_minutos);

                return $ahora->greaterThan($limite);
            });

        foreach ($prestamos as $prestamo) {
            DB::transaction(function () use ($prestamo, $ahora) {

                // 1. Marcar el préstamo como liberado por inasistencia
                $prestamo->update([
                    'estado'             => 'liberado_por_tolerancia',
                    'cancelado_en'       => $ahora,
                    'motivo_cancelacion' => "Inasistencia automática: no se registró check-in dentro de "
                                         . "{$prestamo->tolerancia_minutos} min desde el inicio del préstamo.",
                ]);

                // 2. Liberar el aula
                if ($prestamo->aula) {
                    $prestamo->aula->update(['estado' => 'disponible']);
                }

                // 3. Registrar en historial
                DB::table('historial_movimientos')->insert([
                    'user_id'      => null, // acción del sistema
                    'entidad_tipo' => PrestamoAula::class,
                    'entidad_id'   => $prestamo->id,
                    'tipo_accion'  => 'liberacion_por_tolerancia',
                    'descripcion'  => "Aula {$prestamo->aula?->codigo} liberada automáticamente por inasistencia "
                                    . "(tolerancia: {$prestamo->tolerancia_minutos} min).",
                    'payload'      => json_encode([
                        'prestamo_id'        => $prestamo->id,
                        'aula_id'            => $prestamo->aula_id,
                        'user_id'            => $prestamo->user_id,
                        'hora_inicio'        => $prestamo->hora_inicio,
                        'tolerancia_minutos' => $prestamo->tolerancia_minutos,
                        'ejecutado_en'       => $ahora->toIso8601String(),
                    ]),
                    'created_at'   => $ahora,
                ]);
            });

            $this->line("  Préstamo #{$prestamo->id} — Aula {$prestamo->aula?->codigo} liberada por inasistencia.");
            $cancelados++;
        }

        $this->info("Verificación completada: {$cancelados} préstamo(s) cancelado(s) por inasistencia.");

        return Command::SUCCESS;
    }
}
