{{-- resources/views/docente/dashboard.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Mi Dashboard')
@section('accent-color', 'var(--orange)')
@section('role-label', '🎓 Docente')
@section('page-title', 'Mi Espacio')
@section('page-subtitle', 'Horario semanal y solicitudes de salón')

@section('sidebar-nav')
  @include('docente.partials.sidebar')
@endsection

@section('content')

  {{-- Flash --}}
  @if(session('success'))
    <div class="flex items-center gap-3 p-4 rounded-xl text-sm font-medium mb-5"
         style="background:rgba(247,107,28,.1);color:#c2410c;border:1px solid rgba(247,107,28,.2)">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
      {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="flex items-center gap-3 p-4 rounded-xl text-sm font-medium mb-5"
         style="background:rgba(239,68,68,.08);color:#dc2626;border:1px solid rgba(239,68,68,.15)">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      {{ session('error') }}
    </div>
  @endif

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
    <div>
      <h2 style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;">
        Hola, {{ explode(' ', auth()->user()->name)[0] }} 👋
      </h2>
      <p class="text-gray-500 text-sm mt-0.5">{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
    </div>
    <a href="{{ route('docente.solicitudes.index') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-semibold text-sm"
       style="background:var(--orange);box-shadow:0 4px 16px rgba(247,107,28,.3)">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg>
      Nueva Solicitud
    </a>
  </div>

  {{-- Stats --}}
  <div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm p-4">
      <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Clases esta semana</p>
      <p class="text-2xl font-black" style="color:var(--orange)">{{ $totalClases }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-4">
      <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Solicitudes pendientes</p>
      <p class="text-2xl font-black" style="color:var(--magenta)">{{ $pendientes }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-4">
      <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Solicitudes aprobadas</p>
      <p class="text-2xl font-black" style="color:var(--green)">{{ $aprobadas }}</p>
    </div>
  </div>

  {{-- Horario semanal --}}
  <div class="bg-white rounded-2xl shadow-sm p-5 mb-6">
    <h3 class="font-bold text-gray-800 mb-4" style="font-family:'Syne',sans-serif;">
      Mi horario esta semana
    </h3>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
      @foreach($nombresD as $dia)
        @php
          $fecha    = $diasConFecha[$dia];
          $clases   = $horariosSemana[$dia];
          $esHoy    = $fecha->isToday();
        @endphp
        <div class="rounded-xl border p-3 flex flex-col gap-2
          {{ $esHoy ? 'border-orange-300' : 'border-gray-100' }}"
          style="{{ $esHoy ? 'background:rgba(247,107,28,.06)' : '' }}">
          <div class="flex items-center justify-between">
            <p class="text-xs font-bold uppercase tracking-wider
              {{ $esHoy ? 'text-orange-500' : 'text-gray-400' }}">
              {{ ucfirst($dia) }}
            </p>
            @if($esHoy)
              <span class="text-xs font-bold px-1.5 py-0.5 rounded-full text-white" style="background:var(--orange);font-size:.55rem">HOY</span>
            @endif
          </div>
          <p class="text-xs text-gray-400">{{ $fecha->format('d/m') }}</p>

          @if($clases->isEmpty())
            <p class="text-xs text-gray-300 italic mt-1">Sin clases</p>
          @else
            <div class="flex flex-col gap-1.5">
              @foreach($clases as $h)
                <div class="rounded-lg p-2" style="background:rgba(247,107,28,.1)">
                  <p class="text-xs font-semibold text-orange-700 leading-tight">{{ $h->materia }}</p>
                  <p class="text-xs text-orange-500 mt-0.5">{{ substr($h->hora_inicio,0,5) }}–{{ substr($h->hora_fin,0,5) }}</p>
                  <p class="text-xs text-gray-500">{{ $h->aula?->nombre ?? $h->aula?->codigo ?? '—' }}</p>
                  @if($h->grupo)
                    <p class="text-xs text-gray-400">{{ $h->grupo }}</p>
                  @endif
                </div>
              @endforeach
            </div>
          @endif
        </div>
      @endforeach
    </div>
  </div>

  {{-- Solicitudes recientes --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
      <h3 class="font-bold text-gray-800" style="font-family:'Syne',sans-serif;">Mis solicitudes recientes</h3>
      <a href="{{ route('docente.solicitudes.index') }}" class="text-xs font-semibold" style="color:var(--orange)">Ver todas →</a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b border-gray-100">
          <tr class="text-gray-400 text-xs uppercase tracking-wider">
            <th class="text-left px-5 py-3 font-medium">Salón</th>
            <th class="text-left px-5 py-3 font-medium">Fecha</th>
            <th class="text-left px-5 py-3 font-medium">Horario</th>
            <th class="text-left px-5 py-3 font-medium">Estado</th>
          </tr>
        </thead>
        <tbody>
          @forelse($solicitudesRecientes as $s)
            @php
              $badge = match($s->estado) {
                'pendiente'  => 'bg-yellow-100 text-yellow-700',
                'aprobado'   => 'bg-blue-100 text-blue-700',
                'activo'     => 'bg-green-100 text-green-700',
                'finalizado' => 'bg-gray-100 text-gray-500',
                'cancelado'  => 'bg-red-100 text-red-500',
                default      => 'bg-gray-100 text-gray-500',
              };
              $label = match($s->estado) {
                'pendiente'  => 'Pendiente',
                'aprobado'   => 'Aprobada',
                'activo'     => 'En uso',
                'finalizado' => 'Finalizada',
                'cancelado'  => 'Cancelada',
                default      => $s->estado,
              };
            @endphp
            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
              <td class="px-5 py-3.5 font-medium text-gray-800">
                {{ $s->aula?->nombre ?? $s->aula?->codigo ?? '—' }}
              </td>
              <td class="px-5 py-3.5 text-gray-500 text-xs">
                {{ \Carbon\Carbon::parse($s->fecha_prestamo)->format('d/m/Y') }}
              </td>
              <td class="px-5 py-3.5 text-gray-500 text-xs">
                {{ substr($s->hora_inicio,0,5) }} – {{ substr($s->hora_fin,0,5) }}
              </td>
              <td class="px-5 py-3.5">
                <span class="badge {{ $badge }}">{{ $label }}</span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="px-5 py-10 text-center text-gray-400 text-sm">
                No tienes solicitudes aún.
                <a href="{{ route('docente.solicitudes.index') }}" class="font-semibold ml-1" style="color:var(--orange)">Crear una</a>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection
