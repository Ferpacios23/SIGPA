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
    <div class="flash-ok flex items-center gap-3 p-4 rounded-xl text-sm font-medium mb-5">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
      {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="flash-err flex items-center gap-3 p-4 rounded-xl text-sm font-medium mb-5">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      {{ session('error') }}
    </div>
  @endif

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
    <div>
      <h2 class="page-heading">Hola, {{ explode(' ', auth()->user()->name)[0] }} 👋</h2>
      <p class="text-gray-500 text-sm mt-0.5">{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
    </div>
    <a href="{{ route('docente.solicitudes.index') }}"
       class="btn-orange inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-semibold text-sm">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg>
      Nueva Solicitud
    </a>
  </div>

  {{-- Stats --}}
  <div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm p-4">
      <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Clases esta semana</p>
      <p class="text-orange text-2xl font-black">{{ $totalClases }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-4">
      <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Solicitudes pendientes</p>
      <p class="text-danger text-2xl font-black">{{ $pendientes }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-4">
      <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Solicitudes aprobadas</p>
      <p class="text-green text-2xl font-black">{{ $aprobadas }}</p>
    </div>
  </div>

  {{-- Horario semanal --}}
  <div class="bg-white rounded-2xl shadow-sm p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-bold text-gray-800">Mi horario esta semana</h3>
      <a href="{{ route('docente.horario') }}" class="text-orange text-xs font-semibold">Ver horario completo →</a>
    </div>
    @include('docente.partials.horario-semana')
  </div>

  {{-- Solicitudes recientes --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
      <h3 class="font-bold text-gray-800">Mis solicitudes recientes</h3>
      <a href="{{ route('docente.solicitudes.index') }}" class="text-orange text-xs font-semibold">Ver todas →</a>
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
                <a href="{{ route('docente.solicitudes.index') }}" class="text-orange font-semibold ml-1">Crear una</a>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection
