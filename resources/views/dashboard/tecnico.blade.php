@extends('layouts.dashboard')

@section('title', 'Panel Técnico TI')
@section('accent-color', 'var(--magenta)')
@section('role-label', '💻 Técnico TI')
@section('page-title', 'Panel TI')
@section('page-subtitle')Hoy, {{ now()->isoFormat('dddd D [de] MMMM') }}@endsection

@section('sidebar-nav')
  @include('tecnico.partials.sidebar')
@endsection



@section('content')
<div x-data="tecnicoApp()" class="space-y-6">

  {{-- Flash --}}
  @if(session('success'))
    <div class="flex items-center gap-3 p-4 rounded-xl text-sm font-medium"
         style="background:rgba(0,182,122,.1);color:#00b67a;border:1px solid rgba(0,182,122,.2)">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
      {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="flex items-center gap-3 p-4 rounded-xl text-sm font-medium"
         style="background:rgba(239,68,68,.08);color:#dc2626;border:1px solid rgba(239,68,68,.15)">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      {{ session('error') }}
    </div>
  @endif

  {{-- Banner stats --}}
  <div class="rounded-2xl p-6 text-white" style="background:linear-gradient(135deg,#E0176C 0%,#b91258 100%)">
    <div class="flex flex-wrap items-center justify-between gap-6">
      <div>
        <p class="text-sm font-medium mb-0.5" style="color:rgba(255,255,255,.7)">Bienvenido,</p>
        <h2 style="font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:800;">{{ auth()->user()->name }}</h2>
        <p class="text-sm mt-1" style="color:rgba(255,255,255,.7)">Gestiona los equipos tecnológicos del campus</p>
      </div>
      <div class="flex gap-8">
        <div class="text-center">
          <p style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;">{{ $equiposDisponibles }}</p>
          <p class="text-xs" style="color:rgba(255,255,255,.7)">Disponibles</p>
        </div>
        <div class="text-center">
          <p style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;">{{ $equiposPrestados }}</p>
          <p class="text-xs" style="color:rgba(255,255,255,.7)">Prestados</p>
        </div>
        <div class="text-center">
          <p style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;">{{ $equiposDañados }}</p>
          <p class="text-xs" style="color:rgba(255,255,255,.7)">Dañados</p>
        </div>
        <div class="text-center">
          <p style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;">{{ $totalEquipos }}</p>
          <p class="text-xs" style="color:rgba(255,255,255,.7)">Total</p>
        </div>
      </div>
    </div>
  </div>

  {{-- Cuerpo principal: 2 columnas --}}
  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Columna izquierda (2/3): préstamos activos de hoy --}}
    <div class="xl:col-span-2 space-y-4">

      <div class="flex items-center justify-between">
        <h3 style="font-family:'Syne',sans-serif;font-size:1rem;font-weight:800;">
          Préstamos de hoy
          <span class="ml-2 text-sm font-normal text-gray-500">({{ $prestamosActivos->count() }} activos)</span>
        </h3>
        <a href="{{ route('tecnico.asignaciones') }}"
           class="text-xs font-semibold px-3 py-1.5 rounded-lg"
           style="background:rgba(224,23,108,.1);color:#E0176C">Ver todos →</a>
      </div>

      @forelse($prestamosActivos as $p)
        @php
          $equiposDelPrestamo = $p->prestamosEquipos->whereIn('estado', ['aprobado','activo']);
          $horaInicio  = \Carbon\Carbon::parse(today()->toDateString().' '.substr($p->hora_inicio, 0, 5));
          $horaFin     = \Carbon\Carbon::parse(today()->toDateString().' '.substr($p->hora_fin, 0, 5));
          $enCurso     = now()->between($horaInicio, $horaFin);
        @endphp
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden"
             style="border-left:4px solid {{ $enCurso ? '#E0176C' : '#e2e8f0' }}">
          <div class="px-5 py-4">
            {{-- Header del préstamo --}}
            <div class="flex items-start justify-between gap-4 mb-3">
              <div>
                <div class="flex items-center gap-2 mb-1">
                  @if($enCurso)
                    <span class="flex items-center gap-1.5 text-xs font-bold px-2 py-0.5 rounded-full"
                          style="background:rgba(224,23,108,.1);color:#E0176C">
                      <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse inline-block"></span>
                      En curso
                    </span>
                  @else
                    <span class="badge bg-blue-100 text-blue-700">Aprobado</span>
                  @endif
                  <span class="text-xs text-gray-400">{{ substr($p->hora_inicio,0,5) }} – {{ substr($p->hora_fin,0,5) }}</span>
                </div>
                <p class="font-bold text-gray-800 text-sm">
                  Aula <span style="color:#E0176C">{{ $p->aula?->codigo }}</span>
                  · {{ $p->user?->name }}
                </p>
                @if($p->motivo)
                  <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($p->motivo, 60) }}</p>
                @endif
              </div>
              <button @click="openAsignar({{ $p->id }}, '{{ addslashes($p->aula?->codigo) }}')"
                      class="shrink-0 inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg text-white"
                      style="background:#E0176C">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg>
                Asignar equipo
              </button>
            </div>

            {{-- Equipos asignados --}}
            @if($equiposDelPrestamo->isNotEmpty())
              <div class="border-t border-gray-100 pt-3 mt-1 space-y-2">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Equipos asignados</p>
                @foreach($equiposDelPrestamo as $pe)
                  <div class="equipo-row">
                    <div class="flex items-center gap-3">
                      <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0"
                           style="background:rgba(224,23,108,.1)">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" stroke="#E0176C" stroke-width="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" stroke="#E0176C" stroke-width="2"/></svg>
                      </div>
                      <div>
                        <p class="text-sm font-semibold text-gray-800 leading-tight">{{ $pe->equipo?->nombre }}</p>
                        <p class="text-xs text-gray-400">{{ $pe->equipo?->codigo_inventario }}</p>
                      </div>
                    </div>
                    <button @click="openDevolver({{ $pe->id }}, '{{ addslashes($pe->equipo?->nombre) }}')"
                            class="text-xs font-semibold px-3 py-1 rounded-lg"
                            style="background:rgba(139,92,246,.1);color:#7c3aed">
                      ↩ Devolver
                    </button>
                  </div>
                @endforeach
              </div>
            @else
              <p class="text-xs text-gray-400 border-t border-gray-100 pt-3 mt-1">Sin equipos asignados</p>
            @endif
          </div>
        </div>
      @empty
        <div class="bg-white rounded-2xl shadow-sm px-6 py-12 text-center text-gray-400">
          <svg class="mx-auto mb-3 opacity-40" width="40" height="40" fill="none" viewBox="0 0 24 24">
            <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/>
            <path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="1.5"/>
          </svg>
          <p class="text-sm font-medium">No hay préstamos activos hoy</p>
          <p class="text-xs mt-1">Los préstamos aprobados y activos aparecerán aquí</p>
        </div>
      @endforelse
    </div>

    {{-- Columna derecha (1/3) --}}
    <div class="space-y-4">

      {{-- Devoluciones del día --}}
      <div class="bg-white rounded-2xl shadow-sm p-5">
        <h4 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
          <svg width="15" height="15" fill="none" viewBox="0 0 24 24"><path d="M9 14L4 9l5-5M4 9h11a6 6 0 010 12h-3" stroke="#E0176C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Devoluciones de hoy
        </h4>
        @forelse($devolucionesHoy as $dev)
          <div class="flex items-center gap-3 py-2.5 border-b border-gray-50 last:border-0">
            <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 bg-green-100">
              <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round"/></svg>
            </div>
            <div class="min-w-0">
              <p class="text-sm font-semibold text-gray-800 truncate">{{ $dev->equipo?->nombre }}</p>
              <p class="text-xs text-gray-400">
                {{ $dev->user?->name }} · {{ $dev->devuelto_en?->format('H:i') }}
                @if($dev->estado_devolucion && $dev->estado_devolucion !== 'bueno')
                  · <span class="text-orange-500">{{ $dev->estado_devolucion }}</span>
                @endif
              </p>
            </div>
          </div>
        @empty
          <p class="text-xs text-gray-400 text-center py-3">Sin devoluciones registradas hoy</p>
        @endforelse
      </div>

      {{-- Acceso rápido --}}
      <div class="bg-white rounded-2xl shadow-sm p-5">
        <h4 class="font-bold text-gray-800 text-sm mb-4">Accesos rápidos</h4>
        <div class="space-y-2">
          <a href="{{ route('tecnico.asignaciones') }}"
             class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                 style="background:rgba(224,23,108,.1)">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" stroke="#E0176C" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-800">Asignaciones</p>
              <p class="text-xs text-gray-400">Gestionar equipos por préstamo</p>
            </div>
          </a>
          <a href="{{ route('tecnico.inventario') }}"
             class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                 style="background:rgba(0,182,122,.1)">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" stroke="#00b67a" stroke-width="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" stroke="#00b67a" stroke-width="2"/></svg>
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-800">Inventario</p>
              <p class="text-xs text-gray-400">Ver todos los equipos</p>
            </div>
          </a>
        </div>
      </div>

    </div>
  </div>

  {{-- ════ MODAL ASIGNAR EQUIPO ════ --}}
  <div x-show="showAsignar" class="modal-overlay" @click.self="showAsignar=false" style="display:none">
    <div class="modal-box">
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <div>
          <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.05rem;">Asignar Equipo</h3>
          <p class="text-gray-400 text-xs mt-0.5">Aula: <strong x-text="asignarAula"></strong></p>
        </div>
        <button @click="showAsignar=false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
      </div>
      <form :action="'/tecnico/prestamos/' + asignarId + '/asignar'" method="POST" class="px-6 py-5 space-y-4">
        @csrf
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-2">Seleccionar equipo disponible *</label>
          @if($disponibles->isEmpty())
            <div class="p-3 rounded-xl text-xs font-medium" style="background:rgba(234,179,8,.08);color:#854d0e;border:1px solid rgba(234,179,8,.2)">
              No hay equipos disponibles en este momento.
            </div>
          @else
            <select name="equipo_id" class="w-full border border-gray-200 rounded-xl p-3 text-sm bg-gray-50 focus:outline-none focus:border-pink-400" required>
              <option value="">Seleccionar equipo...</option>
              @foreach($disponibles as $eq)
                <option value="{{ $eq->id }}">
                  {{ $eq->nombre }} — {{ $eq->codigo_inventario }}
                  @if($eq->marca) · {{ $eq->marca }} @endif
                </option>
              @endforeach
            </select>
          @endif
        </div>
        <div class="p-3 rounded-xl text-xs flex items-start gap-2"
             style="background:rgba(224,23,108,.06);color:#9f1239;border:1px solid rgba(224,23,108,.15)">
          <svg width="13" height="13" fill="none" viewBox="0 0 24 24" class="shrink-0 mt-0.5"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          El equipo quedará marcado como <strong>no disponible</strong> hasta que sea devuelto.
        </div>
        <div class="flex gap-3 pt-1">
          <button type="button" @click="showAsignar=false"
            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50">
            Cancelar
          </button>
          <button type="submit" class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm"
                  style="background:#E0176C" {{ $disponibles->isEmpty() ? 'disabled' : '' }}>
            Asignar equipo
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- ════ MODAL DEVOLVER ════ --}}
  <div x-show="showDevolver" class="modal-overlay" @click.self="showDevolver=false" style="display:none">
    <div class="modal-box" style="max-width:400px">
      <div class="px-6 py-6">
        <div class="w-12 h-12 rounded-full mx-auto mb-4 flex items-center justify-center"
             style="background:rgba(139,92,246,.1)">
          <svg width="22" height="22" fill="none" viewBox="0 0 24 24">
            <path d="M9 14L4 9l5-5M4 9h11a6 6 0 010 12h-3" stroke="#7c3aed" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.05rem;text-align:center;margin-bottom:4px;">Registrar devolución</h3>
        <p class="text-gray-500 text-sm text-center mb-1" x-text="'Equipo: ' + devolverNombre"></p>
        <p class="text-gray-400 text-xs text-center mb-5">¿En qué estado regresa?</p>
        <form :action="'/tecnico/asignaciones/' + devolverIdVal + '/devolver'" method="POST">
          @csrf @method('PATCH')
          <div class="space-y-2 mb-5">
            @foreach(['bueno'=>'Bueno — sin daños','regular'=>'Regular — desgaste normal','dañado'=>'Dañado — requiere revisión'] as $val=>$lbl)
              <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                <input type="radio" name="estado_devolucion" value="{{ $val }}" {{ $val==='bueno'?'checked':'' }}
                       class="accent-purple-600 w-4 h-4"/>
                <span class="text-sm font-medium text-gray-700">{{ $lbl }}</span>
              </label>
            @endforeach
          </div>
          <div class="flex gap-3">
            <button type="button" @click="showDevolver=false"
              class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50">
              Cancelar
            </button>
            <button type="submit" class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm"
                    style="background:#7c3aed">
              Confirmar devolución
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>
@endsection


