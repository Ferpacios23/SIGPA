{{-- resources/views/docente/solicitudes.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Mis Solicitudes')
@section('accent-color', 'var(--orange)')
@section('role-label', '🎓 Docente')
@section('page-title', 'Solicitudes de Salón')
@section('page-subtitle', 'Gestiona tus solicitudes de préstamo')

@section('sidebar-nav')
  @include('docente.partials.sidebar')
@endsection

@section('content')
<div x-data="docenteSolicitudesApp()">

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
      <h2 style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;">Mis Solicitudes</h2>
      <p class="text-gray-500 text-sm mt-0.5">{{ $solicitudes->total() }} solicitudes en total</p>
    </div>
    <button @click="showCreate=true"
      class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-semibold text-sm"
      style="background:var(--orange);box-shadow:0 4px 16px rgba(247,107,28,.3)">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg>
      Nueva Solicitud
    </button>
  </div>

  {{-- Filtros --}}
  <form method="GET" action="{{ route('docente.solicitudes.index') }}"
        class="bg-white rounded-2xl shadow-sm p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div class="min-w-[160px]">
      <select name="estado" class="field">
        <option value="">Todos los estados</option>
        <option value="pendiente"  {{ request('estado')==='pendiente'  ? 'selected' : '' }}>Pendiente</option>
        <option value="aprobado"   {{ request('estado')==='aprobado'   ? 'selected' : '' }}>Aprobada</option>
        <option value="activo"     {{ request('estado')==='activo'     ? 'selected' : '' }}>En uso</option>
        <option value="finalizado" {{ request('estado')==='finalizado' ? 'selected' : '' }}>Finalizada</option>
        <option value="cancelado"  {{ request('estado')==='cancelado'  ? 'selected' : '' }}>Cancelada</option>
      </select>
    </div>
    <div class="min-w-[160px]">
      <input type="date" name="fecha" value="{{ request('fecha') }}" class="field"/>
    </div>
    <button type="submit" class="px-4 py-2 rounded-xl text-white font-semibold text-sm" style="background:var(--orange)">
      Filtrar
    </button>
    @if(request()->hasAny(['estado','fecha']))
      <a href="{{ route('docente.solicitudes.index') }}"
         class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50">
        Limpiar
      </a>
    @endif
  </form>

  {{-- Tabla --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b border-gray-100">
          <tr class="text-gray-400 text-xs uppercase tracking-wider">
            <th class="text-left px-5 py-3 font-medium">Salón</th>
            <th class="text-left px-5 py-3 font-medium">Fecha</th>
            <th class="text-left px-5 py-3 font-medium">Horario</th>
            <th class="text-left px-5 py-3 font-medium">Motivo</th>
            <th class="text-left px-5 py-3 font-medium">Estado</th>
            <th class="text-left px-5 py-3 font-medium">Acción</th>
          </tr>
        </thead>
        <tbody>
          @forelse($solicitudes as $s)
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
              <td class="px-5 py-3.5">
                <p class="font-semibold text-gray-800">{{ $s->aula?->nombre ?? $s->aula?->codigo ?? '—' }}</p>
                @if($s->aula?->ubicacion)
                  <p class="text-xs text-gray-400 mt-0.5">{{ $s->aula->ubicacion }}</p>
                @endif
              </td>
              <td class="px-5 py-3.5 text-gray-600 text-xs">
                {{ \Carbon\Carbon::parse($s->fecha_prestamo)->format('d/m/Y') }}
              </td>
              <td class="px-5 py-3.5 text-gray-600 text-xs">
                {{ substr($s->hora_inicio,0,5) }} – {{ substr($s->hora_fin,0,5) }}
              </td>
              <td class="px-5 py-3.5 text-gray-500 text-xs max-w-[180px] truncate">
                {{ $s->motivo ?? '—' }}
              </td>
              <td class="px-5 py-3.5">
                <span class="badge {{ $badge }}">{{ $label }}</span>
                @if($s->motivo_cancelacion && $s->estado === 'cancelado')
                  <p class="text-xs text-gray-400 mt-0.5 max-w-[140px] truncate">{{ $s->motivo_cancelacion }}</p>
                @endif
              </td>
              <td class="px-5 py-3.5">
                @if($s->estado === 'pendiente')
                  <button @click="confirmCancel({{ $s->id }})"
                    class="text-xs font-semibold text-red-500 hover:underline">
                    Cancelar
                  </button>
                @else
                  <span class="text-xs text-gray-300">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                <svg class="mx-auto mb-3 opacity-40" width="40" height="40" fill="none" viewBox="0 0 24 24">
                  <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <p class="text-sm font-medium">No tienes solicitudes</p>
                <p class="text-xs mt-1">Crea tu primera solicitud de salón con el botón superior</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $solicitudes->links() }}</div>
  </div>

  {{-- ════ MODAL NUEVA SOLICITUD ════ --}}
  <div x-show="showCreate" class="modal-overlay" @click.self="showCreate=false" style="display:none">
    <div class="modal-box">
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <div>
          <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;">Nueva Solicitud de Salón</h3>
          <p class="text-gray-400 text-xs mt-0.5">La secretaría revisará y aprobará tu solicitud</p>
        </div>
        <button @click="showCreate=false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
      </div>
      <form method="POST" action="{{ route('docente.solicitudes.store') }}" class="px-6 py-5 space-y-4">
        @csrf

        @if($errors->any())
          <div class="p-3 rounded-xl text-xs" style="background:rgba(239,68,68,.08);color:#dc2626;border:1px solid rgba(239,68,68,.15)">
            @foreach($errors->all() as $err)
              <p>• {{ $err }}</p>
            @endforeach
          </div>
        @endif

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Salón *</label>
          <select name="aula_id" class="field" required>
            <option value="">Seleccionar salón...</option>
            @foreach($aulas as $aula)
              <option value="{{ $aula->id }}" {{ old('aula_id') == $aula->id ? 'selected' : '' }}>
                {{ $aula->nombre ?? $aula->codigo }}
                @if($aula->capacidad) (Cap. {{ $aula->capacidad }}) @endif
                @if($aula->ubicacion) — {{ $aula->ubicacion }} @endif
              </option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Fecha *</label>
          <input type="date" name="fecha_prestamo" value="{{ old('fecha_prestamo', now()->format('Y-m-d')) }}"
                 min="{{ now()->format('Y-m-d') }}" class="field" required/>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Hora de inicio *</label>
            <input type="time" name="hora_inicio" value="{{ old('hora_inicio') }}" class="field" required/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Hora de fin *</label>
            <input type="time" name="hora_fin" value="{{ old('hora_fin') }}" class="field" required/>
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Motivo / Descripción</label>
          <input type="text" name="motivo" value="{{ old('motivo') }}" class="field"
                 placeholder="Ej: Clase de recuperación, examen parcial..."/>
        </div>

        <div class="flex gap-3 pt-1">
          <button type="button" @click="showCreate=false"
            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50">
            Cancelar
          </button>
          <button type="submit"
            class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm"
            style="background:var(--orange)">
            Enviar Solicitud
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- ════ MODAL CONFIRMAR CANCELACIÓN ════ --}}
  <div x-show="showCancel" class="modal-overlay" @click.self="showCancel=false" style="display:none">
    <div class="modal-box max-w-sm">
      <div class="px-6 pt-6 pb-4 border-b border-gray-100">
        <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;">¿Cancelar solicitud?</h3>
        <p class="text-gray-500 text-sm mt-1">Esta acción no se puede deshacer.</p>
      </div>
      <div class="px-6 py-5 flex gap-3">
        <button @click="showCancel=false"
          class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50">
          Volver
        </button>
        <form :action="'/docente/solicitudes/' + cancelId + '/cancelar'" method="POST" class="flex-1">
          @csrf @method('PATCH')
          <button type="submit"
            class="w-full py-2.5 rounded-xl text-white font-semibold text-sm"
            style="background:var(--magenta)">
            Sí, cancelar
          </button>
        </form>
      </div>
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>window.SIGPA_PAGE = { hasErrors: {{ $errors->any() ? 'true' : 'false' }} };</script>
@endsection
