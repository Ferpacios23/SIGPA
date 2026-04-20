<?php

namespace App\Console\Commands;

use App\Models\Aula;
use App\Models\HorarioAcademico;
use App\Models\PrestamoAula;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SincronizarEstadoAulas extends Command
{
    protected $signature   = 'sigpa:sincronizar-aulas';
    protected $description = 'Ocupa aulas al inicio de clases/préstamos activos y las libera al terminar';

    public function handle(): int
    {
        $ahora   = now();
        $hoy     = today()->toDateString();
        $horaStr = $ahora->format('H:i:s');

        $ocupadas  = 0;
        $liberadas = 0;

        // ── 1. HORARIOS ACADÉMICOS ────────────────────────────────
        $dias = ['domingo','lunes','martes','miercoles','jueves','viernes','sabado'];
        $diaHoy = $dias[$ahora->dayOfWeek];

        $horariosActivos = HorarioAcademico::with('aula')
            ->where('dia_semana', $diaHoy)
            ->where('activo', true)
            ->where('fecha_inicio', '<=', $hoy)
            ->where('fecha_fin',    '>=', $hoy)
            ->get();

        foreach ($horariosActivos as $h) {
            if (!$h->aula) continue;

            $inicio = Carbon::parse($hoy . ' ' . $h->hora_inicio);
            $fin    = Carbon::parse($hoy . ' ' . $h->hora_fin);

            // La clase está en curso → marcar aula como ocupada
            if ($ahora->gte($inicio) && $ahora->lt($fin)) {
                if ($h->aula->estado !== 'ocupada') {
                    $h->aula->update(['estado' => 'ocupada']);
                    DB::table('historial_movimientos')->insert([
                        'user_id'      => null,
                        'entidad_tipo' => HorarioAcademico::class,
                        'entidad_id'   => $h->id,
                        'tipo_accion'  => 'cambio_estado_aula',
                        'descripcion'  => "Aula {$h->aula->codigo} ocupada automáticamente por clase '{$h->materia}' ({$h->hora_inicio}–{$h->hora_fin}).",
                        'payload'      => json_encode([
                            'horario_id' => $h->id,
                            'aula_id'    => $h->aula_id,
                            'materia'    => $h->materia,
                            'inicio'     => $h->hora_inicio,
                            'fin'        => $h->hora_fin,
                        ]),
                        'created_at'   => $ahora,
                    ]);
                    $this->line("  [CLASE] Aula {$h->aula->codigo} → ocupada ({$h->materia} {$h->hora_inicio}–{$h->hora_fin})");
                    $ocupadas++;
                }
            }

            // La clase terminó → liberar si no hay otro préstamo activo tomándola
            if ($ahora->gte($fin)) {
                $tienePrestamoActivo = PrestamoAula::where('aula_id', $h->aula_id)
                    ->whereIn('estado', ['aprobado', 'activo'])
                    ->whereDate('fecha_prestamo', $hoy)
                    ->where('hora_inicio', '<', $horaStr)
                    ->where('hora_fin',    '>', $horaStr)
                    ->exists();

                if (!$tienePrestamoActivo && $h->aula->estado === 'ocupada') {
                    // Verificar que no haya otra clase activa en la misma aula
                    $otraClaseActiva = HorarioAcademico::where('aula_id', $h->aula_id)
                        ->where('id', '!=', $h->id)
                        ->where('dia_semana', $diaHoy)
                        ->where('activo', true)
                        ->where('fecha_inicio', '<=', $hoy)
                        ->where('fecha_fin',    '>=', $hoy)
                        ->where('hora_inicio', '<=', $horaStr)
                        ->where('hora_fin',    '>', $horaStr)
                        ->exists();

                    if (!$otraClaseActiva) {
                        $h->aula->update(['estado' => 'disponible']);
                        DB::table('historial_movimientos')->insert([
                            'user_id'      => null,
                            'entidad_tipo' => HorarioAcademico::class,
                            'entidad_id'   => $h->id,
                            'tipo_accion'  => 'cambio_estado_aula',
                            'descripcion'  => "Aula {$h->aula->codigo} liberada automáticamente al finalizar clase '{$h->materia}'.",
                            'payload'      => json_encode([
                                'horario_id' => $h->id,
                                'aula_id'    => $h->aula_id,
                                'materia'    => $h->materia,
                            ]),
                            'created_at'   => $ahora,
                        ]);
                        $this->line("  [CLASE] Aula {$h->aula->codigo} → disponible (finalizó '{$h->materia}')");
                        $liberadas++;
                    }
                }
            }
        }

        // ── 2. PRÉSTAMOS ACTIVOS ──────────────────────────────────
        // Marcar como activo y ocupar aula cuando llega la hora de inicio y ya tiene check-in
        $prestamosAIniciar = PrestamoAula::with('aula')
            ->where('estado', 'aprobado')
            ->where('asistencia_confirmada', true)
            ->whereDate('fecha_prestamo', $hoy)
            ->where('hora_inicio', '<=', $horaStr)
            ->where('hora_fin',    '>',  $horaStr)
            ->get();

        foreach ($prestamosAIniciar as $p) {
            DB::transaction(function () use ($p, $ahora) {
                $p->update(['estado' => 'activo']);
                if ($p->aula && $p->aula->estado !== 'ocupada') {
                    $p->aula->update(['estado' => 'ocupada']);
                }
            });
            $this->line("  [PRÉSTAMO #{$p->id}] Aula {$p->aula?->codigo} → activo/ocupada");
            $ocupadas++;
        }

        // Finalizar préstamos cuya hora_fin ya pasó y siguen activos
        $prestamosAFinalizar = PrestamoAula::with('aula')
            ->whereIn('estado', ['aprobado', 'activo'])
            ->whereDate('fecha_prestamo', $hoy)
            ->where('hora_fin', '<=', $horaStr)
            ->get();

        foreach ($prestamosAFinalizar as $p) {
            DB::transaction(function () use ($p, $ahora) {
                $p->update(['estado' => 'finalizado']);

                if ($p->aula) {
                    // Solo liberar si ningún otro préstamo/clase lo tiene ocupado
                    $otroActivo = PrestamoAula::where('aula_id', $p->aula_id)
                        ->where('id', '!=', $p->id)
                        ->whereIn('estado', ['aprobado', 'activo'])
                        ->whereDate('fecha_prestamo', today())
                        ->where('hora_inicio', '<=', $ahora->format('H:i:s'))
                        ->where('hora_fin',    '>',  $ahora->format('H:i:s'))
                        ->exists();

                    if (!$otroActivo) {
                        $p->aula->update(['estado' => 'disponible']);
                    }
                }

                DB::table('historial_movimientos')->insert([
                    'user_id'      => null,
                    'entidad_tipo' => PrestamoAula::class,
                    'entidad_id'   => $p->id,
                    'tipo_accion'  => 'finalizacion_aula',
                    'descripcion'  => "Préstamo #{$p->id} finalizado automáticamente. Aula {$p->aula?->codigo} liberada.",
                    'payload'      => null,
                    'created_at'   => $ahora,
                ]);
            });
            $this->line("  [PRÉSTAMO #{$p->id}] Aula {$p->aula?->codigo} → finalizado/disponible");
            $liberadas++;
        }

        $this->info("Sincronización completada: {$ocupadas} aula(s) ocupada(s), {$liberadas} liberada(s).");

        return Command::SUCCESS;
    }
}
