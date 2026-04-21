{{-- resources/views/secretaria/prestamos.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Gestión de Préstamos')
@section('accent-color', 'var(--green)')
@section('role-label', '🏫 Secretaría')
@section('page-title', 'Préstamos')
@section('page-subtitle', 'Gestión completa de solicitudes')

@section('sidebar-nav')
  @include('secretaria.partials.sidebar')
@endsection



@section('content')
<div x-data="prestamosApp()" class="space-y-5">

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-4">
    <h2 style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;">Préstamos de aulas</h2>
    <button @click="showCreate=true"
      class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-semibold text-sm"
      style="background:var(--green);box-shadow:0 4px 16px rgba(0,182,122,.3)">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg>
      Nuevo Préstamo
    </button>
  </div>

  {{-- Tabs de estado --}}
  <div class="bg-white rounded-2xl shadow-sm px-4 py-3 flex flex-wrap gap-2">
    @php
      $estados = [''=>'Todos','pendiente'=>'Pendientes','aprobado'=>'Aprobados','activo'=>'Activos','finalizado'=>'Finalizados','cancelado'=>'Cancelados'];
      $current = request('estado','');
    @endphp
    @foreach($estados as $val=>$lbl)
      <a href="{{ route('secretaria.prestamos', array_merge(request()->query(), ['estado'=>$val])) }}"
         class="tab-pill {{ $current===$val ? 'on' : '' }}">
        {{ $lbl }}
        @if($val==='pendiente' && $prestamosPendientesCount > 0)
          <span class="ml-1 px-1.5 py-0.5 rounded-full text-xs" style="background:rgba(255,255,255,.3)">
            {{ $prestamosPendientesCount }}
          </span>
        @endif
      </a>
    @endforeach
  </div>

  {{-- Filtros --}}
  <form method="GET" action="{{ route('secretaria.prestamos') }}" class="bg-white rounded-2xl shadow-sm p-4 flex flex-wrap gap-3 items-end">
    <input type="hidden" name="estado" value="{{ request('estado') }}"/>
    <div class="relative flex-1 min-w-48">
      <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="15" height="15" fill="none" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Docente o código de aula..." class="field pl-9"/>
    </div>
    <div>
      <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha</label>
      <input type="date" name="fecha" value="{{ request('fecha') }}" class="field w-auto"/>
    </div>
    <button type="submit" class="px-4 py-2.5 rounded-xl text-white font-semibold text-sm" style="background:var(--green)">Buscar</button>
    @if(request()->hasAny(['search','fecha']))
      <a href="{{ route('secretaria.prestamos',['estado'=>request('estado')]) }}" class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50">Limpiar</a>
    @endif
  </form>

  {{-- Tabla --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b border-gray-100">
          <tr class="text-gray-400 text-xs uppercase tracking-wider">
            <th class="text-left px-5 py-3 font-medium">Docente</th>
            <th class="text-left px-5 py-3 font-medium">Aula</th>
            <th class="text-left px-5 py-3 font-medium">Fecha</th>
            <th class="text-left px-5 py-3 font-medium">Horario</th>
            <th class="text-left px-5 py-3 font-medium">Motivo</th>
            <th class="text-left px-5 py-3 font-medium">Estado</th>
            <th class="text-left px-5 py-3 font-medium">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($prestamos as $p)
            @php
              $badge = [
                'pendiente'              => 'bg-yellow-100 text-yellow-700',
                'aprobado'               => 'bg-blue-100 text-blue-700',
                'activo'                 => 'bg-green-100 text-green-700',
                'finalizado'             => 'bg-gray-100 text-gray-500',
                'cancelado'              => 'bg-red-100 text-red-600',
                'liberado_por_tolerancia'=> 'bg-orange-100 text-orange-600',
              ][$p->estado] ?? 'bg-gray-100 text-gray-500';
            @endphp
            <tr class="border-b border-gray-50 hover:bg-gray-50/70 transition-colors">
              <td class="px-5 py-3.5">
                <p class="font-semibold text-gray-800 leading-tight">{{ $p->user->name ?? '—' }}</p>
                <p class="text-xs text-gray-400">{{ $p->user->profile?->identificacion ?? $p->user->email ?? '' }}</p>
              </td>
              <td class="px-5 py-3.5">
                <p class="font-bold text-gray-800">{{ $p->aula->codigo ?? '—' }}</p>
                <p class="text-xs text-gray-400">Cap. {{ $p->aula->capacidad ?? '—' }}</p>
              </td>
              <td class="px-5 py-3.5 text-gray-600 text-xs font-medium">
                {{ \Carbon\Carbon::parse($p->fecha_prestamo)->format('d M Y') }}
              </td>
              <td class="px-5 py-3.5 text-gray-600 text-xs">
                {{ substr($p->hora_inicio,0,5) }} – {{ substr($p->hora_fin,0,5) }}
                <p class="text-gray-400">{{ $p->tolerancia_minutos }}min tolerancia</p>
              </td>
              <td class="px-5 py-3.5 text-gray-500 text-xs max-w-[140px]">
                {{ Str::limit($p->motivo ?? '—', 40) }}
              </td>
              <td class="px-5 py-3.5">
                <span class="badge {{ $badge }}">{{ ucfirst(str_replace('_',' ',$p->estado)) }}</span>
                @if($p->aprobadoPor)
                  <p class="text-xs text-gray-400 mt-1">por {{ $p->aprobadoPor->name }}</p>
                @endif
              </td>
              <td class="px-5 py-3.5">
                <div class="flex flex-col gap-1.5">
                  @if($p->estado === 'pendiente')
                    <form method="POST" action="{{ route('secretaria.prestamos.aprobar', $p->id) }}">
                      @csrf @method('PATCH')
                      <button type="submit" class="text-xs font-semibold text-green-600 hover:underline">✓ Aprobar</button>
                    </form>
                    <button @click="openCancel({{ $p->id }})" class="text-xs font-semibold text-red-500 hover:underline text-left">✕ Rechazar</button>
                  @elseif(in_array($p->estado, ['aprobado','activo']))
                    <form method="POST" action="{{ route('secretaria.prestamos.finalizar', $p->id) }}">
                      @csrf @method('PATCH')
                      <button type="submit" class="text-xs font-semibold text-blue-600 hover:underline">Finalizar</button>
                    </form>
                    <button @click="openAsignarEquipo({{ $p->id }})"
                            class="text-xs font-semibold hover:underline text-left"
                            style="color:var(--green)">
                      + Equipo
                    </button>
                    <button @click="openCancel({{ $p->id }})" class="text-xs font-semibold text-red-500 hover:underline text-left">Cancelar</button>
                  @else
                    <span class="text-xs text-gray-400">—</span>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                <svg class="mx-auto mb-3 opacity-40" width="40" height="40" fill="none" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                <p class="text-sm font-medium">Sin préstamos que mostrar</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $prestamos->links() }}</div>
  </div>

  {{-- ════ MODAL CREAR ════ --}}
  <div x-show="showCreate" class="modal-overlay" @click.self="showCreate=false" style="display:none">
    <div class="modal-box">
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <div>
          <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;">Registrar Préstamo</h3>
          <p class="text-gray-400 text-xs mt-0.5">Queda aprobado automáticamente</p>
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
            <input type="date" name="fecha_prestamo" class="field" min="{{ today()->toDateString() }}" required/>
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
            <input type="text" name="motivo" class="field" placeholder="Clase, reunión..."/>
          </div>
        </div>
        <div class="flex gap-3 pt-2">
          <button type="button" @click="showCreate=false"
            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50">
            Cancelar
          </button>
          <button type="submit" class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm hover:brightness-110" style="background:var(--green)">
            Registrar
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- ════ MODAL ASIGNAR EQUIPO (HU-09) ════ --}}
  <div x-show="showAsignarEquipo" class="modal-overlay" @click.self="showAsignarEquipo=false" style="display:none">
    <div class="modal-box" style="max-width:460px">
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <div>
          <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.05rem;">Asignar Equipo al Préstamo</h3>
          <p class="text-gray-400 text-xs mt-0.5">El equipo quedará marcado como no disponible</p>
        </div>
        <button @click="showAsignarEquipo=false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
      </div>
      <form :action="'/secretaria/prestamos/' + asignarPrestamoId + '/equipos'" method="POST" class="px-6 py-5 space-y-4">
        @csrf
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Equipo disponible *</label>
          <select name="equipo_id" class="field" required>
            <option value="">Seleccionar equipo...</option>
            @foreach($equiposDisponibles as $eq)
              <option value="{{ $eq->id }}">
                {{ $eq->nombre }} — {{ $eq->codigo_inventario }}
                @if($eq->marca) · {{ $eq->marca }} @endif
              </option>
            @endforeach
          </select>
          @if($equiposDisponibles->isEmpty())
            <p class="text-xs text-orange-500 mt-1">No hay equipos disponibles en este momento.</p>
          @endif
        </div>
        <div class="flex gap-3 pt-1">
          <button type="button" @click="showAsignarEquipo=false"
            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50">
            Cancelar
          </button>
          <button type="submit" class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm hover:brightness-110"
                  style="background:var(--green)" :disabled="{{ $equiposDisponibles->isEmpty() ? 'true' : 'false' }}">
            Asignar equipo
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- ════ MODAL CANCELAR/RECHAZAR ════ --}}
  <div x-show="showCancel" class="modal-overlay" @click.self="showCancel=false" style="display:none">
    <div class="modal-box" style="max-width:420px">
      <div class="px-6 py-6">
        <div class="w-12 h-12 rounded-full mx-auto mb-4 flex items-center justify-center" style="background:rgba(239,68,68,.1)">
          <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke="#ef4444" stroke-width="2" stroke-linecap="round"/></svg>
        </div>
        <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.05rem;text-align:center;margin-bottom:4px;">Cancelar préstamo</h3>
        <p class="text-gray-500 text-sm text-center mb-5">Indica el motivo para el docente</p>
        <form :action="'/secretaria/prestamos/' + cancelId + '/cancelar'" method="POST">
          @csrf @method('PATCH')
          <textarea name="motivo" class="field" rows="3" placeholder="Ej: Aula requerida para evento institucional..."></textarea>
          <div class="flex gap-3 mt-4">
            <button type="button" @click="showCancel=false"
              class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50">
              Volver
            </button>
            <button type="submit" class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm" style="background:#ef4444">
              Confirmar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>window.SIGPA_PAGE = { pendientesCount: {{ \App\Models\PrestamoAula::where('estado','pendiente')->count() }} };</script>
@endsection