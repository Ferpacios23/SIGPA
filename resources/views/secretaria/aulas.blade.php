{{-- resources/views/secretaria/aulas.blade.php --}}
@extends('layouts.dashboard')
@section('title', 'Aulas')
@section('accent-color', 'var(--green)')
@section('role-label', '🏫 Secretaría')
@section('page-title', 'Estado de Aulas')
@section('page-subtitle', 'Vista en tiempo real')
 
@section('sidebar-nav')
  @include('secretaria.partials.sidebar')
@endsection
 

 
@section('content')
@php
  // Estado efectivo por aula
  // 'espera'   → horario académico activo, sin check-in todavía
  // 'ocupada'  → préstamo activo o aula.estado = 'ocupada' tras check-in
  $countEspera        = $aulas->filter(fn($a) =>  $a->horario_activo && !$a->prestamo_activo)->count();
  $countOcupadas      = $aulas->filter(fn($a) => !$a->horario_activo && ($a->prestamo_activo || $a->estado === 'ocupada'))->count();
  $countDisponibles   = $aulas->filter(fn($a) =>  $a->estado === 'disponible' && !$a->horario_activo && !$a->prestamo_activo)->count();
  $countMantenimiento = $aulas->where('estado', 'en_mantenimiento')->count();
@endphp
<div x-data="aulasSecretariaApp()" class="space-y-5">

  {{-- Stats --}}
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
    @foreach([
      ['Disponibles',   $countDisponibles,   '#22c55e','rgba(34,197,94,.08)'],
      ['En espera',     $countEspera,        '#8b5cf6','rgba(139,92,246,.08)'],
      ['Ocupadas',      $countOcupadas,      '#ef4444','rgba(239,68,68,.08)'],
      ['Mantenimiento', $countMantenimiento, '#f59e0b','rgba(245,158,11,.08)'],
      ['Total activas', $aulas->count(),     'var(--blue)','rgba(26,79,214,.08)'],
    ] as [$lbl,$val,$col,$bg])
    <div class="bg-white rounded-2xl p-4 shadow-sm">
      <p style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:{{ $col }}">{{ $val }}</p>
      <p class="text-gray-500 text-sm mt-0.5">{{ $lbl }}</p>
    </div>
    @endforeach
  </div>
 
  {{-- Filtros --}}
  <div class="bg-white rounded-2xl shadow-sm p-4 flex flex-wrap gap-3">
    <div class="relative flex-1 min-w-44">
      <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="14" height="14" fill="none" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      <input type="text" x-model="search" placeholder="Buscar aula..." class="field pl-9"/>
    </div>
    <select x-model="filterEstado" class="field w-auto min-w-40">
      <option value="">Todos los estados</option>
      <option value="disponible">Disponibles</option>
      <option value="espera">En espera (clase)</option>
      <option value="ocupada">Ocupadas</option>
      <option value="en_mantenimiento">En mantenimiento</option>
    </select>
  </div>
 
  {{-- Grid --}}
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
    @foreach($aulas as $aula)
      @php
        // Estado efectivo:
        // 'espera'  → horario académico activo, sin check-in (secretaría debe confirmar)
        // 'ocupada' → préstamo activo o check-in ya realizado
        if ($aula->horario_activo && !$aula->prestamo_activo) {
            $efectivo = 'espera';
        } elseif ($aula->prestamo_activo || $aula->estado === 'ocupada') {
            $efectivo = 'ocupada';
        } else {
            $efectivo = $aula->estado;
        }

        $estilos = [
          'disponible'       => ['bar'=>'#22c55e','badge'=>'bg-green-100 text-green-700',   'label'=>'Disponible'],
          'espera'           => ['bar'=>'#8b5cf6','badge'=>'bg-purple-100 text-purple-700', 'label'=>'En espera'],
          'ocupada'          => ['bar'=>'#ef4444','badge'=>'bg-red-100 text-red-600',       'label'=>'Ocupada'],
          'en_mantenimiento' => ['bar'=>'#f59e0b','badge'=>'bg-yellow-100 text-yellow-700', 'label'=>'Mantenimiento'],
          'inactiva'         => ['bar'=>'#94a3b8','badge'=>'bg-gray-100 text-gray-500',     'label'=>'Inactiva'],
        ];
        $sc = $estilos[$efectivo] ?? $estilos['inactiva'];
      @endphp
      <div class="aula-card"
           x-show="(!search || '{{ strtolower($aula->codigo.' '.($aula->nombre??'')) }}'.includes(search.toLowerCase())) && (!filterEstado || filterEstado==='{{ $efectivo }}')">
        <div class="sbar" style="background:{{ $sc['bar'] }}"></div>

        <div class="flex items-start justify-between mt-1">
          <div>
            <p style="font-family:'Syne',sans-serif;font-weight:800;font-size:1rem;">{{ $aula->codigo }}</p>
            @if($aula->nombre)
              <p class="text-gray-400 text-xs">{{ $aula->nombre }}</p>
            @endif
          </div>
          <span class="badge {{ $sc['badge'] }}">{{ $sc['label'] }}</span>
        </div>

        <div class="mt-3 space-y-1.5 text-xs text-gray-500">
          <div class="flex items-center gap-1.5">
            <svg width="11" height="11" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3" stroke="currentColor" stroke-width="2"/><path d="M5 20c0-3.314 3.134-6 7-6s7 2.686 7 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            {{ $aula->capacidad }} personas
          </div>
          @if($aula->ubicacion)
          <div class="flex items-center gap-1.5">
            <svg width="11" height="11" fill="none" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/></svg>
            {{ $aula->ubicacion }}
          </div>
          @endif
        </div>

        {{-- Pie de tarjeta: préstamo de usuario > horario académico > estado libre --}}
        @if($aula->prestamo_activo)
          @php
            $pa = $aula->prestamo_activo;
          @endphp
          <div class="mt-3 pt-3 border-t border-gray-100">
            <p class="text-xs font-semibold text-gray-700">{{ $pa->user->name ?? '—' }}</p>
            <p class="text-xs text-gray-400">{{ substr($pa->hora_inicio,0,5) }} – {{ substr($pa->hora_fin,0,5) }}</p>
          </div>

        @elseif($aula->horario_activo)
          @php
            $ha = $aula->horario_activo;
          @endphp
          <div class="mt-3 pt-3 border-t border-gray-100">
            <div class="flex items-center gap-1 mb-0.5">
              <svg width="9" height="9" fill="none" viewBox="0 0 24 24" class="flex-shrink-0" style="color:#8b5cf6">
                <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
              <span class="text-xs font-semibold" style="color:#8b5cf6">Clase en curso</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 truncate">{{ $ha->docente->name ?? 'Docente' }}</p>
            <p class="text-xs text-gray-400 truncate">{{ $ha->materia }}{{ $ha->grupo ? ' · Gr. '.$ha->grupo : '' }}</p>
            <p class="text-xs text-gray-400">{{ substr($ha->hora_inicio,0,5) }} – {{ substr($ha->hora_fin,0,5) }}</p>
            <form method="POST"
                  action="{{ route('secretaria.horarios.checkin', $ha->id) }}"
                  class="mt-2">
              @csrf @method('PATCH')
              <button type="submit"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-white px-2.5 py-1 rounded-lg hover:brightness-110 transition-all"
                style="background:#8b5cf6">
                <svg width="10" height="10" fill="none" viewBox="0 0 24 24">
                  <path d="M20 6L9 17l-5-5" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Check-in · Ocupar aula
              </button>
            </form>
          </div>

        @elseif($efectivo === 'disponible')
          <div class="mt-3 pt-3 border-t border-gray-100">
            <p class="text-xs text-green-600 font-medium">✓ Libre para préstamo</p>
          </div>
        @endif
      </div>
    @endforeach
  </div>
 
</div>
@endsection
 