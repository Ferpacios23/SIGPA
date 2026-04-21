{{-- resources/views/dashboard/secretaria.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Panel Secretaría')
@section('accent-color', 'var(--green)')
@section('accent-var', '#00B67A')
@section('role-label', '🏫 Secretaría')
@section('page-title', 'Panel de Secretaría')
@section('page-subtitle')Gestión de préstamos de aulas · {{ now()->format('d M Y') }}@endsection

@section('sidebar-nav')
  @include('secretaria.partials.sidebar')
@endsection



@section('content')
@php
  use Carbon\Carbon;

  $ahora        = now();
  $horaAhora    = $ahora->hour + $ahora->minute / 60;

  // Construir todos los eventos del día: préstamos + horarios académicos
  $eventos = collect();

  foreach ($prestamosDeHoy as $p) {
      $inicio   = Carbon::parse($p->fecha_prestamo->format('Y-m-d') . ' ' . $p->hora_inicio);
      $fin      = Carbon::parse($p->fecha_prestamo->format('Y-m-d') . ' ' . $p->hora_fin);
      $limite   = $inicio->copy()->addMinutes($p->tolerancia_minutos);

      // Cuánto falta para liberar
      if ($p->estado === 'aprobado' && !$p->asistencia_confirmada) {
          $segs = $ahora->lt($limite) ? $ahora->diffInSeconds($limite, false) : -1;
          $tipo = 'tolerancia';
      } elseif ($p->estado === 'activo') {
          $segs = $ahora->lt($fin) ? $ahora->diffInSeconds($fin, false) : -1;
          $tipo = 'uso';
      } else {
          $segs = null;
          $tipo = null;
      }

      $eventos->push([
          'tipo'        => 'prestamo',
          'estado'      => $p->estado,
          'aula'        => $p->aula?->codigo ?? '—',
          'nombre'      => $p->user?->name ?? '—',
          'motivo'      => $p->motivo,
          'hora_inicio' => $p->hora_inicio,
          'hora_fin'    => $p->hora_fin,
          'h_inicio'    => Carbon::parse($p->hora_inicio)->hour + Carbon::parse($p->hora_inicio)->minute / 60,
          'h_fin'       => Carbon::parse($p->hora_fin)->hour + Carbon::parse($p->hora_fin)->minute / 60,
          'segs'        => $segs,
          'tipo_cuenta' => $tipo,
          'tolerancia'  => $p->tolerancia_minutos,
          'id'          => $p->id,
          'prestamo'    => $p,
      ]);
  }

  foreach ($horariosHoy as $h) {
      $eventos->push([
          'tipo'        => 'horario',
          'estado'      => 'horario_academico',
          'aula'        => $h->aula?->codigo ?? '—',
          'nombre'      => $h->docente?->name ?? 'Docente',
          'motivo'      => $h->materia . ($h->grupo ? ' · Gr. '.$h->grupo : ''),
          'hora_inicio' => $h->hora_inicio,
          'hora_fin'    => $h->hora_fin,
          'h_inicio'    => Carbon::parse($h->hora_inicio)->hour + Carbon::parse($h->hora_inicio)->minute / 60,
          'h_fin'       => Carbon::parse($h->hora_fin)->hour + Carbon::parse($h->hora_fin)->minute / 60,
          'segs'        => null,
          'tipo_cuenta' => 'horario',
          'tolerancia'  => 0,
          'id'          => 'h'.$h->id,
          'prestamo'    => null,
      ]);
  }

  $eventos = $eventos->sortBy('h_inicio')->values();

  // Rango de horas a mostrar
  $horaMin = $eventos->isNotEmpty() ? max(6, (int) floor($eventos->min('h_inicio')) ) : 6;
  $horaMax = $eventos->isNotEmpty() ? min(22, (int) ceil($eventos->max('h_fin')) + 1)  : 22;
  $horaMin = min($horaMin, (int) $horaAhora);
  $horaMax = max($horaMax, (int) $horaAhora + 1);

  // Agrupar eventos por hora de inicio
  $eventosPorHora = [];
  foreach ($eventos as $ev) {
      $h = (int) floor($ev['h_inicio']);
      $eventosPorHora[$h][] = $ev;
  }
@endphp

<div x-data="secretariaApp()" class="space-y-4">

  {{-- ── BANNER ── --}}
  <div class="rounded-2xl p-4 text-white relative overflow-hidden" style="background:linear-gradient(135deg,#00B67A 0%,#008f62 100%)">
    <div class="absolute inset-0 opacity-10" style="background-image:linear-gradient(rgba(255,255,255,.15) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.15) 1px,transparent 1px);background-size:28px 28px"></div>
    <div class="relative flex items-center justify-between flex-wrap gap-4">
      <div>
        <p class="text-green-200 text-xs font-semibold uppercase tracking-wider mb-0.5">Secretaría SIGPA</p>
        <h2 style="font-size:1.2rem;font-weight:800;">{{ auth()->user()->name }}</h2>
        <p class="text-green-200 text-sm">{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
      </div>
      <div class="flex gap-6">
        @foreach([
          ['Aulas libres',    $aulasLibres,          '#fff'],
          ['Préstamos hoy',   $prestamosHoy,         '#fff'],
          ['Pendientes',      $prestamosPendientes,  '#fde68a'],
          ['En curso',        $prestamosActivos,     '#fff'],
        ] as [$lbl, $val, $c])
        <div class="text-center">
          <p style="font-family:'Syne',sans-serif;font-size:1.7rem;font-weight:800;color:{{ $c }}">{{ $val }}</p>
          <p class="text-green-200 text-xs">{{ $lbl }}</p>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- ── LAYOUT PRINCIPAL: CALENDARIO | LATERAL ── --}}
  <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

    {{-- ════ CALENDARIO DEL DÍA (2/3) ════ --}}
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background:rgba(26,79,214,.08)">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--blue)" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke-linecap="round"/></svg>
          </div>
          <div>
            <h3 style="font-weight:800;font-size:.95rem;">Calendario del día</h3>
            <p class="text-gray-400 text-xs">Actualizado · <span id="reloj">{{ now()->format('H:i:s') }}</span></p>
          </div>
        </div>
        <div class="flex items-center gap-3 text-xs">
          <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm inline-block" style="background:#fef9c3;border-left:3px solid #f59e0b"></span>Pendiente</span>
          <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm inline-block" style="background:#f0fdf4;border-left:3px solid #22c55e"></span>Aprobado</span>
          <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm inline-block" style="background:#eff6ff;border-left:3px solid #3b82f6"></span>Activo</span>
          <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm inline-block" style="background:#f5f3ff;border-left:3px solid #8b5cf6"></span>Clase</span>
        </div>
      </div>

      <div class="px-4 py-3 overflow-y-auto" style="max-height:calc(100vh - 280px)">
        @if($eventos->isEmpty())
          <div class="py-20 text-center text-gray-400">
            <svg class="mx-auto mb-3 opacity-30" width="48" height="48" fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            <p class="font-semibold text-sm">Sin reservas para hoy</p>
            <button @click="openCreate()" class="mt-3 text-sm font-semibold hover:underline" style="color:var(--green)">+ Registrar préstamo</button>
          </div>
        @else

          {{-- ── GRILLA DE HORAS ── --}}
          @for($h = $horaMin; $h <= $horaMax; $h++)
            @php $esAhora = (int)$horaAhora === $h; @endphp
            <div class="cal-row {{ $esAhora ? 'is-now' : '' }}" id="hora-{{ $h }}">

              {{-- Etiqueta de hora --}}
              <div class="cal-hour pt-1">{{ str_pad($h,2,'0',STR_PAD_LEFT) }}:00</div>

              {{-- Línea + eventos --}}
              <div class="cal-line {{ $esAhora ? 'current-hour' : '' }} pb-1">
                @if($esAhora)
                  <div class="flex items-center gap-2 mb-1" style="margin-top:-8px">
                    <div class="dot-now"></div>
                    <span style="font-size:.65rem;font-weight:800;color:#ef4444">AHORA · {{ now()->format('H:i') }}</span>
                  </div>
                @endif

                @if(isset($eventosPorHora[$h]))
                  @foreach($eventosPorHora[$h] as $ev)
                    @php
                      $estadoClass = $ev['estado'];
                      $horaInicio  = substr($ev['hora_inicio'],0,5);
                      $horaFin     = substr($ev['hora_fin'],0,5);
                      $durMin      = ($ev['h_fin'] - $ev['h_inicio']) * 60;
                    @endphp

                    <div class="event-card {{ $estadoClass }}">
                      <div class="flex items-start justify-between gap-2 flex-wrap">

                        {{-- Info principal --}}
                        <div class="flex items-start gap-2 min-w-0">
                          {{-- Ícono de tipo --}}
                          @if($ev['tipo'] === 'horario')
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5" style="background:rgba(139,92,246,.12)">
                              <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="#8b5cf6" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke-linecap="round"/></svg>
                            </div>
                          @else
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"
                                 style="background:{{ $estadoClass==='activo' ? 'rgba(59,130,246,.12)' : ($estadoClass==='aprobado' ? 'rgba(34,197,94,.12)' : 'rgba(245,158,11,.12)') }}">
                              <svg width="12" height="12" fill="none" viewBox="0 0 24 24"
                                   stroke="{{ $estadoClass==='activo' ? '#3b82f6' : ($estadoClass==='aprobado' ? '#22c55e' : '#f59e0b') }}" stroke-width="2.5">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke-linecap="round"/><circle cx="9" cy="7" r="4"/>
                              </svg>
                            </div>
                          @endif

                          <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                              {{-- Aula badge --}}
                              <span class="font-mono font-black text-sm" style="color:{{ $ev['tipo']==='horario' ? '#7c3aed' : 'var(--blue)' }}">{{ $ev['aula'] }}</span>
                              {{-- Horario --}}
                              <span class="text-xs font-semibold text-gray-500">{{ $horaInicio }} – {{ $horaFin }}</span>
                              <span class="text-gray-300 text-xs">·</span>
                              <span class="text-xs text-gray-400">{{ $durMin }} min</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-800 leading-tight mt-0.5">{{ $ev['nombre'] }}</p>
                            @if($ev['motivo'])
                              <p class="text-xs text-gray-400 mt-0.5 leading-tight">{{ Str::limit($ev['motivo'], 70) }}</p>
                            @endif
                          </div>
                        </div>

                        {{-- Estado + countdown + acciones --}}
                        <div class="flex flex-col items-end gap-1.5 flex-shrink-0">

                          {{-- Badge de estado --}}
                          @if($ev['tipo'] === 'horario')
                            <span class="badge" style="background:rgba(139,92,246,.1);color:#7c3aed">
                              <svg width="9" height="9" fill="none" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="2.5"/><path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                              Bloqueado · Clase
                            </span>
                          @elseif($ev['estado'] === 'pendiente')
                            <span class="badge bg-yellow-100 text-yellow-700">Pendiente aprobación</span>
                          @elseif($ev['estado'] === 'aprobado')
                            <span class="badge bg-green-100 text-green-700">
                              <svg width="9" height="9" fill="none" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                              Aprobado
                            </span>
                          @elseif($ev['estado'] === 'activo')
                            <span class="badge bg-blue-100 text-blue-700">
                              <svg width="8" height="8" viewBox="0 0 8 8" fill="#3b82f6"><circle cx="4" cy="4" r="4"/></svg>
                              En uso
                            </span>
                          @elseif($ev['estado'] === 'finalizado')
                            <span class="badge bg-gray-100 text-gray-500">Finalizado</span>
                          @elseif(in_array($ev['estado'], ['cancelado','liberado_por_tolerancia']))
                            <span class="badge bg-red-100 text-red-500">{{ $ev['estado'] === 'cancelado' ? 'Cancelado' : 'Liberado · Inasistencia' }}</span>
                          @endif

                          {{-- Cuenta regresiva --}}
                          @if($ev['segs'] !== null && $ev['segs'] >= 0)
                            @php
                              $mins = (int)($ev['segs'] / 60);
                              $urgente = $mins < 10;
                            @endphp
                            <span class="countdown {{ $urgente ? 'urgente' : ($ev['tipo_cuenta']==='uso' ? 'info' : 'normal') }}"
                                  data-segs="{{ (int)$ev['segs'] }}"
                                  data-tipo="{{ $ev['tipo_cuenta'] }}">
                              <svg width="10" height="10" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                              <span class="countdown-txt">
                                {{ $ev['tipo_cuenta'] === 'tolerancia' ? 'Libera en ' : 'En uso · libera en ' }}{{ $mins }}m {{ ($ev['segs'] % 60) }}s
                              </span>
                            </span>
                          @elseif($ev['segs'] !== null && $ev['segs'] < 0)
                            <span class="countdown done">
                              <svg width="10" height="10" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                              Por liberar...
                            </span>
                          @elseif($ev['tipo'] === 'horario')
                            @php
                              $finHorario = Carbon::parse($ev['hora_fin']);
                              $segsHorario = $ahora->lt(Carbon::parse(today()->format('Y-m-d').' '.$ev['hora_fin']))
                                ? $ahora->diffInSeconds(Carbon::parse(today()->format('Y-m-d').' '.$ev['hora_fin']), false)
                                : -1;
                            @endphp
                            @if($segsHorario > 0 && $horaAhora >= $ev['h_inicio'])
                              <span class="countdown info"
                                    data-segs="{{ (int)$segsHorario }}"
                                    data-tipo="horario">
                                <svg width="10" height="10" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                <span class="countdown-txt">Se desbloquea en {{ (int)($segsHorario/60) }}m</span>
                              </span>
                            @endif
                          @endif

                          {{-- Acciones rápidas --}}
                          @if($ev['prestamo'])
                            <div class="flex gap-1.5 mt-0.5">
                              @if($ev['estado'] === 'pendiente')
                                <form method="POST" action="{{ route('secretaria.prestamos.aprobar', $ev['id']) }}">
                                  @csrf @method('PATCH')
                                  <button type="submit" class="action-btn text-white text-[.68rem] py-1 px-2.5" style="background:var(--green)">Aprobar</button>
                                </form>
                                <button @click="openCancel({{ $ev['id'] }})"
                                        class="action-btn border border-red-200 text-red-500 bg-white text-[.68rem] py-1 px-2.5">Rechazar</button>
                              @elseif($ev['estado'] === 'aprobado')
                                <form method="POST" action="{{ route('secretaria.prestamos.checkin', $ev['id']) }}">
                                  @csrf @method('PATCH')
                                  <button type="submit" class="action-btn text-white text-[.68rem] py-1 px-2.5" style="background:var(--blue)">Check-in</button>
                                </form>
                                <button @click="openCancel({{ $ev['id'] }})"
                                        class="action-btn border border-red-200 text-red-500 bg-white text-[.68rem] py-1 px-2.5">Cancelar</button>
                              @elseif($ev['estado'] === 'activo')
                                <form method="POST" action="{{ route('secretaria.prestamos.finalizar', $ev['id']) }}">
                                  @csrf @method('PATCH')
                                  <button type="submit" class="action-btn text-white text-[.68rem] py-1 px-2.5" style="background:var(--blue)">Finalizar</button>
                                </form>
                              @endif
                            </div>
                          @endif

                        </div>
                      </div>
                    </div>
                  @endforeach
                @endif
              </div>
            </div>
          @endfor
        @endif
      </div>

      {{-- Footer: botón crear --}}
      <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
        <button @click="openCreate()"
          class="w-full py-2.5 rounded-xl text-sm font-semibold text-white hover:brightness-110 transition-all"
          style="background:var(--green)">
          + Registrar nuevo préstamo
        </button>
      </div>
    </div>

    {{-- ════ LATERAL (1/3) ════ --}}
    <div class="space-y-4">

      {{-- Solicitudes pendientes --}}
      <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <h3 style="font-weight:700;font-size:.88rem;">Solicitudes pendientes</h3>
            @if($prestamosPendientes > 0)
              <span class="px-2 py-0.5 rounded-full text-xs font-bold text-white" style="background:var(--magenta)">{{ $prestamosPendientes }}</span>
            @endif
          </div>
          <a href="{{ route('secretaria.prestamos') }}" class="text-xs font-semibold" style="color:var(--green)">Ver todos →</a>
        </div>
        <div class="divide-y divide-gray-50 max-h-64 overflow-y-auto">
          @forelse($pendientes as $p)
            <div class="px-4 py-3 hover:bg-gray-50/60 transition-colors">
              <div class="flex items-start gap-2.5">
                <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0"
                     style="background:rgba(247,107,28,.12);color:var(--orange)">
                  {{ strtoupper(substr($p->user->name ?? '?', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-semibold text-gray-800 text-xs leading-tight truncate">{{ $p->user->name ?? '—' }}</p>
                  <p class="text-xs text-gray-400 mt-0.5">
                    <span class="font-semibold text-gray-600">{{ $p->aula->codigo ?? '—' }}</span>
                    · {{ \Carbon\Carbon::parse($p->fecha_prestamo)->format('d M') }}
                    · {{ substr($p->hora_inicio,0,5) }}–{{ substr($p->hora_fin,0,5) }}
                  </p>
                  <div class="flex gap-1.5 mt-1.5">
                    <form method="POST" action="{{ route('secretaria.prestamos.aprobar', $p->id) }}">
                      @csrf @method('PATCH')
                      <button type="submit" class="action-btn text-white py-0.5 px-2 text-[.68rem]" style="background:var(--green)">
                        <svg width="10" height="10" fill="none" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg>
                        Aprobar
                      </button>
                    </form>
                    <button @click="openCancel({{ $p->id }})"
                            class="action-btn border border-red-200 text-red-500 bg-white py-0.5 px-2 text-[.68rem]">Rechazar</button>
                  </div>
                </div>
              </div>
            </div>
          @empty
            <div class="py-8 text-center text-gray-400">
              <svg class="mx-auto mb-2 opacity-40" width="28" height="28" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/><path d="M8 12l3 3 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
              <p class="text-xs font-medium">Sin pendientes · Todo al día ✓</p>
            </div>
          @endforelse
        </div>
      </div>

      {{-- Estado de aulas --}}
      <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
          <h3 style="font-weight:700;font-size:.88rem;">Estado de aulas</h3>
          <a href="{{ route('secretaria.aulas') }}" class="text-xs font-semibold" style="color:var(--green)">Ver más →</a>
        </div>
        <div class="p-3 grid grid-cols-3 gap-2">
          @foreach($aulas as $aula)
            @php
              $sc = [
                'disponible'       => ['bar'=>'#22c55e','badge'=>'bg-green-100 text-green-700'],
                'ocupada'          => ['bar'=>'#ef4444','badge'=>'bg-red-100 text-red-600'],
                'en_mantenimiento' => ['bar'=>'#f59e0b','badge'=>'bg-yellow-100 text-yellow-700'],
                'inactiva'         => ['bar'=>'#94a3b8','badge'=>'bg-gray-100 text-gray-500'],
              ][$aula->estado] ?? ['bar'=>'#94a3b8','badge'=>'bg-gray-100 text-gray-500'];
            @endphp
            <div class="aula-mini">
              <div class="sbar" style="background:{{ $sc['bar'] }}"></div>
              <p style="font-family:'Syne',sans-serif;font-weight:800;font-size:.82rem;color:#0d1117;margin-top:3px;">{{ $aula->codigo }}</p>
              <p class="text-gray-400 text-[.65rem] mt-0.5">{{ $aula->capacidad }}p</p>
              @if($aula->prestamo_activo)
                <p class="text-[.62rem] font-semibold mt-1 text-red-500 truncate">{{ Str::limit($aula->prestamo_activo->user?->name ?? '', 10) }}</p>
              @endif
            </div>
          @endforeach
        </div>
      </div>

      {{-- Resumen del día --}}
      <div class="bg-white rounded-2xl shadow-sm p-4">
        <h3 style="font-weight:700;font-size:.88rem;margin-bottom:10px;">Resumen del día</h3>
        <div class="space-y-2">
          @foreach([
            ['Total de reservas hoy', $prestamosHoy, 'var(--blue)'],
            ['Aulas disponibles', $aulasLibres, 'var(--green)'],
            ['En uso ahora', $prestamosActivos, 'var(--magenta)'],
            ['Clases programadas', $horariosHoy->count(), '#8b5cf6'],
          ] as [$lbl, $val, $col])
          <div class="flex items-center justify-between py-1.5 border-b border-gray-50 last:border-0">
            <span class="text-xs text-gray-500">{{ $lbl }}</span>
            <span class="font-black text-sm" style="color:{{ $col }}">{{ $val }}</span>
          </div>
          @endforeach
        </div>
      </div>

    </div>
  </div>

  {{-- ════ MODAL CREAR PRÉSTAMO ════ --}}
  <div x-show="showCreate" class="modal-overlay" @click.self="showCreate=false" style="display:none">
    <div class="modal-box">
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <div>
          <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;">Registrar Préstamo</h3>
          <p class="text-gray-400 text-xs mt-0.5">El préstamo quedará aprobado directamente</p>
        </div>
        <button @click="showCreate=false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
      </div>
      <form method="POST" action="{{ route('secretaria.prestamos.store') }}" class="px-6 py-5 space-y-4">
        @csrf
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Docente *</label>
          <select name="user_id" class="field" required>
            <option value="">Seleccionar docente...</option>
            @php
              $docentes = \App\Models\User::whereHas('profile.role', fn($q)=>$q->where('slug','docente'))
                          ->with('profile')->orderBy('name')->get();
            @endphp
            @foreach($docentes as $d)
              <option value="{{ $d->id }}">{{ $d->name }}{{ $d->profile?->identificacion ? ' – '.$d->profile->identificacion : '' }}</option>
            @endforeach
          </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Aula *</label>
            <select name="aula_id" class="field" required>
              <option value="">Seleccionar...</option>
              @foreach($aulas->where('estado','disponible') as $a)
                <option value="{{ $a->id }}">{{ $a->codigo }} ({{ $a->capacidad }}p)</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Fecha *</label>
            <input type="date" name="fecha_prestamo" class="field" min="{{ today()->toDateString() }}" value="{{ today()->toDateString() }}" required/>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Hora inicio *</label>
            <input type="time" name="hora_inicio" class="field" required/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Hora fin *</label>
            <input type="time" name="hora_fin" class="field" required/>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tolerancia (min)</label>
            <input type="number" name="tolerancia_minutos" value="15" min="5" max="60" class="field"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Motivo</label>
            <input type="text" name="motivo" class="field" placeholder="Ej: Clase magistral"/>
          </div>
        </div>
        <div class="flex gap-3 pt-2">
          <button type="button" @click="showCreate=false"
            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50">Cancelar</button>
          <button type="submit"
            class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm hover:brightness-110 transition-all"
            style="background:var(--green)">Registrar Préstamo</button>
        </div>
      </form>
    </div>
  </div>

  {{-- ════ MODAL CANCELAR ════ --}}
  <div x-show="showCancel" class="modal-overlay" @click.self="showCancel=false" style="display:none">
    <div class="modal-box" style="max-width:420px">
      <div class="px-6 py-6">
        <div class="w-12 h-12 rounded-full mx-auto mb-4 flex items-center justify-center" style="background:rgba(239,68,68,.1)">
          <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke="#ef4444" stroke-width="2" stroke-linecap="round"/></svg>
        </div>
        <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.05rem;text-align:center;margin-bottom:8px;">Rechazar solicitud</h3>
        <form :action="'/secretaria/prestamos/' + cancelId + '/cancelar'" method="POST">
          @csrf @method('PATCH')
          <div class="mt-4">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Motivo de rechazo</label>
            <textarea name="motivo" class="field" rows="3" placeholder="Explica el motivo del rechazo..."></textarea>
          </div>
          <div class="flex gap-3 mt-5">
            <button type="button" @click="showCancel=false"
              class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50">Volver</button>
            <button type="submit" class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm" style="background:#ef4444">Confirmar rechazo</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>
@endsection


