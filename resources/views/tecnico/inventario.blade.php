@extends('layouts.dashboard')

@section('title', 'Inventario de Equipos')
@section('accent-color', 'var(--magenta)')
@section('role-label', '💻 Técnico TI')
@section('page-title', 'Inventario')
@section('page-subtitle', 'Registro y control de implementos tecnológicos')

@section('sidebar-nav')
  @include('tecnico.partials.sidebar')
@endsection



@section('content')
<div x-data="inventarioApp()" class="space-y-5">

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

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-4">
    <div>
      <h2 style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;">Inventario de Equipos</h2>
      <p class="text-sm text-gray-500 mt-0.5">{{ $equipos->total() }} equipos registrados</p>
    </div>
    <button @click="showRegistrar=true"
      class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-semibold text-sm"
      style="background:#E0176C;box-shadow:0 4px 16px rgba(224,23,108,.3)">
      <svg width="15" height="15" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg>
      Registrar equipo
    </button>
  </div>

  {{-- Tabs --}}
  <div class="bg-white rounded-2xl shadow-sm px-4 py-3 flex flex-wrap gap-2">
    @php
      $tabs = ['' => 'Todos', 'disponible' => 'Disponibles', 'prestado' => 'Prestados', 'dañado' => 'Fuera de servicio'];
      $current = request('estado', '');
    @endphp
    @foreach($tabs as $val => $lbl)
      <a href="{{ route('tecnico.inventario', array_merge(request()->query(), ['estado'=>$val])) }}"
         class="tab-pill {{ $current === $val ? 'on' : '' }}">{{ $lbl }}</a>
    @endforeach
  </div>

  {{-- Filtro --}}
  <form method="GET" action="{{ route('tecnico.inventario') }}"
        class="bg-white rounded-2xl shadow-sm p-4 flex flex-wrap gap-3 items-end">
    <input type="hidden" name="estado" value="{{ request('estado') }}"/>
    <div class="relative flex-1 min-w-48">
      <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="15" height="15" fill="none" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, código o marca..." class="field pl-9"/>
    </div>
    <button type="submit" class="px-4 py-2.5 rounded-xl text-white font-semibold text-sm" style="background:#E0176C">Buscar</button>
    @if(request('search'))
      <a href="{{ route('tecnico.inventario', ['estado'=>request('estado')]) }}"
         class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50">Limpiar</a>
    @endif
  </form>

  {{-- Tabla --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b border-gray-100">
          <tr class="text-gray-400 text-xs uppercase tracking-wider">
            <th class="text-left px-5 py-3 font-medium">Equipo</th>
            <th class="text-left px-5 py-3 font-medium">Código</th>
            <th class="text-left px-5 py-3 font-medium">Estado físico</th>
            <th class="text-left px-5 py-3 font-medium">Disponibilidad</th>
            <th class="text-left px-5 py-3 font-medium">Asignado a</th>
            <th class="text-left px-5 py-3 font-medium">Ubicación</th>
            <th class="text-left px-5 py-3 font-medium">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($equipos as $eq)
            @php
              $estadoBadge = [
                'bueno'        => 'bg-green-100 text-green-700',
                'regular'      => 'bg-yellow-100 text-yellow-700',
                'dañado'       => 'bg-red-100 text-red-600',
                'dado_de_baja' => 'bg-gray-200 text-gray-500',
              ][$eq->estado_fisico] ?? 'bg-gray-100 text-gray-500';

              $tieneActivo = !$eq->disponible && isset($eq->asignacion_activa);
            @endphp
            <tr class="border-b border-gray-50 hover:bg-gray-50/70 transition-colors">
              <td class="px-5 py-3.5">
                <p class="font-semibold text-gray-800">{{ $eq->nombre }}</p>
                @if($eq->marca || $eq->modelo)
                  <p class="text-xs text-gray-400">{{ $eq->marca }} {{ $eq->modelo }}</p>
                @endif
              </td>
              <td class="px-5 py-3.5">
                <span class="font-mono text-xs font-semibold text-gray-700 bg-gray-100 px-2 py-1 rounded-lg">
                  {{ $eq->codigo_inventario }}
                </span>
              </td>
              <td class="px-5 py-3.5">
                <span class="badge {{ $estadoBadge }}">
                  {{ ['bueno'=>'Bueno','regular'=>'Regular','dañado'=>'Dañado','dado_de_baja'=>'Dado de baja'][$eq->estado_fisico] ?? $eq->estado_fisico }}
                </span>
              </td>
              <td class="px-5 py-3.5">
                @if($eq->disponible)
                  <span class="flex items-center gap-1.5 text-xs font-semibold text-green-700">
                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                    Disponible
                  </span>
                @elseif(in_array($eq->estado_fisico, ['dañado','dado_de_baja']))
                  <span class="flex items-center gap-1.5 text-xs font-semibold text-red-600">
                    <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>
                    Fuera de servicio
                  </span>
                @else
                  <span class="flex items-center gap-1.5 text-xs font-semibold" style="color:#E0176C">
                    <span class="w-2 h-2 rounded-full inline-block" style="background:#E0176C"></span>
                    En préstamo
                  </span>
                @endif
              </td>
              <td class="px-5 py-3.5">
                @if($tieneActivo)
                  @php $asig = $eq->asignacion_activa; @endphp
                  <p class="text-xs font-semibold text-gray-700">{{ $asig->prestamoAula?->aula?->codigo ?? '—' }}</p>
                  <p class="text-xs text-gray-400">{{ $asig->user?->name ?? '—' }}</p>
                  <p class="text-xs text-gray-400">{{ substr($asig->hora_inicio??'',0,5) }}–{{ substr($asig->hora_fin??'',0,5) }}</p>
                @else
                  <span class="text-xs text-gray-400">—</span>
                @endif
              </td>
              <td class="px-5 py-3.5 text-xs text-gray-500">
                {{ $eq->ubicacion_almacenamiento ?? '—' }}
              </td>
              <td class="px-5 py-3.5">
                <div class="flex items-center gap-2">
                  {{-- Ver detalle --}}
                  <button @click="openVer({{ json_encode([
                      'nombre'       => $eq->nombre,
                      'codigo'       => $eq->codigo_inventario,
                      'marca'        => $eq->marca ?? '',
                      'modelo'       => $eq->modelo ?? '',
                      'descripcion'  => $eq->descripcion ?? '',
                      'estado_fisico'=> $eq->estado_fisico,
                      'ubicacion'    => $eq->ubicacion_almacenamiento ?? '',
                      'fecha_adq'    => $eq->fecha_adquisicion ? \Carbon\Carbon::parse($eq->fecha_adquisicion)->format('d/m/Y') : '',
                      'disponible'   => $eq->disponible,
                      'en_prestamo'  => $tieneActivo,
                      'aula'         => $tieneActivo ? ($eq->asignacion_activa->prestamoAula?->aula?->codigo ?? '') : '',
                      'docente'      => $tieneActivo ? ($eq->asignacion_activa->user?->name ?? '') : '',
                      'horario'      => $tieneActivo ? (substr($eq->asignacion_activa->hora_inicio??'',0,5).'–'.substr($eq->asignacion_activa->hora_fin??'',0,5)) : '',
                      'registrado'   => $eq->created_at->format('d/m/Y'),
                    ]) }})"
                    class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg border transition-colors hover:bg-blue-50"
                    style="border-color:#bfdbfe;color:#2563eb">
                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg>
                    Ver
                  </button>
                  {{-- Cambiar estado (bloqueado si está en préstamo) --}}
                  @if($tieneActivo)
                    <span title="Devuelva el equipo antes de cambiar su estado"
                      class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg border cursor-not-allowed opacity-40"
                      style="border-color:#e2e8f0;color:#64748b">
                      <svg width="11" height="11" fill="none" viewBox="0 0 24 24"><path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="2"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" stroke="currentColor" stroke-width="2"/></svg>
                      Estado
                    </span>
                  @else
                    <button @click="openEstado({{ json_encode([
                        'id'           => $eq->id,
                        'nombre'       => $eq->nombre,
                        'estado_fisico'=> $eq->estado_fisico,
                        'ubicacion'    => $eq->ubicacion_almacenamiento ?? '',
                        'en_prestamo'  => false,
                      ]) }})"
                      class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg border transition-colors hover:bg-gray-50"
                      style="border-color:#e2e8f0;color:#64748b">
                      <svg width="11" height="11" fill="none" viewBox="0 0 24 24"><path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="2"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" stroke="currentColor" stroke-width="2"/></svg>
                      Estado
                    </button>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                <svg class="mx-auto mb-3 opacity-40" width="40" height="40" fill="none" viewBox="0 0 24 24">
                  <rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="1.5"/>
                  <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                <p class="text-sm font-medium">Sin equipos que mostrar</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $equipos->links() }}</div>
  </div>

  {{-- ════ MODAL VER EQUIPO ════ --}}
  <div x-show="showVer" class="modal-overlay" @click.self="showVer=false" style="display:none">
    <div class="modal-box" style="max-width:480px">

      {{-- Cabecera --}}
      <div class="flex items-start justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-xl flex items-center justify-center text-white shrink-0"
               style="background:linear-gradient(135deg,#E0176C,#b5105a)">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" stroke="currentColor" stroke-width="2"/></svg>
          </div>
          <div>
            <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.05rem;" x-text="verData.nombre"></h3>
            <span class="font-mono text-xs font-semibold text-gray-400" x-text="verData.codigo"></span>
          </div>
        </div>
        <button @click="showVer=false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center shrink-0">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
      </div>

      <div class="px-6 py-5 space-y-5">

        {{-- Badge estado + disponibilidad --}}
        <div class="flex flex-wrap gap-2">
          <template x-if="verData.estado_fisico === 'bueno'">
            <span class="badge bg-green-100 text-green-700">Bueno</span>
          </template>
          <template x-if="verData.estado_fisico === 'regular'">
            <span class="badge bg-yellow-100 text-yellow-700">Regular</span>
          </template>
          <template x-if="verData.estado_fisico === 'dañado'">
            <span class="badge bg-red-100 text-red-600">Dañado</span>
          </template>
          <template x-if="verData.estado_fisico === 'dado_de_baja'">
            <span class="badge bg-gray-200 text-gray-500">Dado de baja</span>
          </template>

          <template x-if="verData.disponible">
            <span class="badge" style="background:rgba(22,163,74,.1);color:#16a34a">
              <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Disponible
            </span>
          </template>
          <template x-if="!verData.disponible && verData.en_prestamo">
            <span class="badge" style="background:rgba(224,23,108,.08);color:#E0176C">
              <span class="w-1.5 h-1.5 rounded-full" style="background:#E0176C"></span> En préstamo
            </span>
          </template>
          <template x-if="!verData.disponible && !verData.en_prestamo">
            <span class="badge bg-red-100 text-red-600">
              <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Fuera de servicio
            </span>
          </template>
        </div>

        {{-- Ficha técnica --}}
        <div class="rounded-xl border border-gray-100 overflow-hidden">
          <div class="px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider" style="background:#f8fafc">
            Ficha técnica
          </div>
          <div class="divide-y divide-gray-50">
            <div class="flex items-center px-4 py-2.5 gap-3">
              <span class="text-xs text-gray-400 w-28 shrink-0">Marca</span>
              <span class="text-sm font-semibold text-gray-700" x-text="verData.marca || '—'"></span>
            </div>
            <div class="flex items-center px-4 py-2.5 gap-3">
              <span class="text-xs text-gray-400 w-28 shrink-0">Modelo</span>
              <span class="text-sm font-semibold text-gray-700" x-text="verData.modelo || '—'"></span>
            </div>
            <div class="flex items-start px-4 py-2.5 gap-3">
              <span class="text-xs text-gray-400 w-28 shrink-0 mt-0.5">Descripción</span>
              <span class="text-sm text-gray-700" x-text="verData.descripcion || '—'"></span>
            </div>
            <div class="flex items-center px-4 py-2.5 gap-3">
              <span class="text-xs text-gray-400 w-28 shrink-0">Ubicación</span>
              <span class="text-sm text-gray-700" x-text="verData.ubicacion || '—'"></span>
            </div>
            <div class="flex items-center px-4 py-2.5 gap-3">
              <span class="text-xs text-gray-400 w-28 shrink-0">Adquisición</span>
              <span class="text-sm text-gray-700" x-text="verData.fecha_adq || '—'"></span>
            </div>
            <div class="flex items-center px-4 py-2.5 gap-3">
              <span class="text-xs text-gray-400 w-28 shrink-0">Registrado</span>
              <span class="text-sm text-gray-700" x-text="verData.registrado"></span>
            </div>
          </div>
        </div>

        {{-- Asignación activa --}}
        <template x-if="verData.en_prestamo">
          <div class="rounded-xl p-4 space-y-2" style="background:rgba(224,23,108,.05);border:1px solid rgba(224,23,108,.15)">
            <p class="text-xs font-bold uppercase tracking-wider" style="color:#E0176C">Asignación activa</p>
            <div class="grid grid-cols-3 gap-3 text-center">
              <div>
                <p class="text-xs text-gray-400">Aula</p>
                <p class="font-bold text-gray-800 text-sm" x-text="verData.aula || '—'"></p>
              </div>
              <div>
                <p class="text-xs text-gray-400">Horario</p>
                <p class="font-bold text-gray-800 text-sm" x-text="verData.horario || '—'"></p>
              </div>
              <div>
                <p class="text-xs text-gray-400">Docente</p>
                <p class="font-semibold text-gray-700 text-xs leading-tight" x-text="verData.docente || '—'"></p>
              </div>
            </div>
          </div>
        </template>

        <button @click="showVer=false"
          class="w-full py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50">
          Cerrar
        </button>
      </div>
    </div>
  </div>

  {{-- ════ MODAL REGISTRAR EQUIPO ════ --}}
  <div x-show="showRegistrar" class="modal-overlay" @click.self="showRegistrar=false" style="display:none">
    <div class="modal-box">
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <div>
          <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;">Registrar Implemento</h3>
          <p class="text-gray-400 text-xs mt-0.5">Nuevo equipo al inventario TI</p>
        </div>
        <button @click="showRegistrar=false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
      </div>
      <form method="POST" action="{{ route('tecnico.inventario.store') }}" class="px-6 py-5 space-y-4">
        @csrf

        @if($errors->any())
          <div class="p-3 rounded-xl text-xs font-medium" style="background:rgba(239,68,68,.08);color:#dc2626;border:1px solid rgba(239,68,68,.15)">
            {{ $errors->first() }}
          </div>
        @endif

        <div class="grid grid-cols-2 gap-4">
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nombre del equipo *</label>
            <input type="text" name="nombre" value="{{ old('nombre') }}" class="field"
                   placeholder="Ej: Video Beam, Laptop, Micrófono..." required/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Código de inventario *</label>
            <div class="relative">
              <input type="text" name="codigo_inventario" value="{{ old('codigo_inventario') }}"
                     class="field pr-9"
                     placeholder="Ej: EQ-001" required
                     @input="checkCodigo($event.target.value)"
                     :class="codigoStatus === 'taken' ? 'border-red-400 focus:ring-red-300' : codigoStatus === 'free' ? 'border-green-400 focus:ring-green-300' : ''"/>
              {{-- Icono de estado --}}
              <span class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                <template x-if="codigoStatus === 'checking'">
                  <svg class="animate-spin text-gray-400" width="14" height="14" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="60" stroke-dashoffset="20"/>
                  </svg>
                </template>
                <template x-if="codigoStatus === 'taken'">
                  <svg class="text-red-500" width="14" height="14" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    <path d="M15 9l-6 6M9 9l6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  </svg>
                </template>
                <template x-if="codigoStatus === 'free'">
                  <svg class="text-green-500" width="14" height="14" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    <path d="M8 12l3 3 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  </svg>
                </template>
              </span>
            </div>
            <p x-show="codigoStatus === 'taken'" class="mt-1 text-xs font-semibold text-red-500">
              Este código ya está en uso.
            </p>
            <p x-show="codigoStatus === 'free'" class="mt-1 text-xs font-semibold text-green-600">
              Código disponible.
            </p>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Estado físico *</label>
            <select name="estado_fisico" class="field" required>
              <option value="bueno"        {{ old('estado_fisico','bueno')==='bueno'        ? 'selected':'' }}>Bueno</option>
              <option value="regular"      {{ old('estado_fisico')==='regular'              ? 'selected':'' }}>Regular</option>
              <option value="dañado"       {{ old('estado_fisico')==='dañado'               ? 'selected':'' }}>Dañado</option>
              <option value="dado_de_baja" {{ old('estado_fisico')==='dado_de_baja'         ? 'selected':'' }}>Dado de baja</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Marca</label>
            <input type="text" name="marca" value="{{ old('marca') }}" class="field" placeholder="Ej: HP, Dell, Sony"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Modelo</label>
            <input type="text" name="modelo" value="{{ old('modelo') }}" class="field" placeholder="Ej: ProBook 450"/>
          </div>
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Descripción</label>
            <input type="text" name="descripcion" value="{{ old('descripcion') }}" class="field"
                   placeholder="Características relevantes..."/>
          </div>
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Ubicación de almacenamiento</label>
            <select name="ubicacion_almacenamiento" value="{{ old('ubicacion_almacenamiento') }}" class="field">
              <option value="">Sin ubicación</option>
              <option value="Sala TI" {{ old('ubicacion_almacenamiento') === 'Sala TI' ? 'selected' : '' }}>Sala TI</option>
              <option value="Secretaria" {{ old('ubicacion_almacenamiento') === 'Secretaria' ? 'selected' : '' }}>Secretaria</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Fecha de adquisición</label>
            <input type="date" name="fecha_adquisicion" value="{{ old('fecha_adquisicion') }}" class="field"/>
          </div>
        </div>

        <div class="flex gap-3 pt-2">
          <button type="button" @click="showRegistrar=false"
            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50">
            Cancelar
          </button>
          <button type="submit" class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm"
                  style="background:#E0176C">
            Registrar
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- ════ MODAL CAMBIAR ESTADO ════ --}}
  <div x-show="showEstado" class="modal-overlay" @click.self="showEstado=false" style="display:none">
    <div class="modal-box" style="max-width:440px">
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <div>
          <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.05rem;">Cambiar estado del equipo</h3>
          <p class="text-gray-500 text-xs mt-0.5 font-semibold" x-text="estadoData.nombre"></p>
        </div>
        <button @click="showEstado=false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
      </div>
      <form :action="'/tecnico/inventario/' + estadoData.id + '/estado'" method="POST" class="px-6 py-5 space-y-5">
        @csrf @method('PATCH')

        {{-- Selector de estado físico --}}
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-3">Estado físico *</label>
          <div class="grid grid-cols-2 gap-2">
            @foreach([
              'bueno'        => ['label'=>'Bueno',        'sub'=>'Operativo sin daños',        'color'=>'#16a34a', 'icon'=>'✓'],
              'regular'      => ['label'=>'Regular',      'sub'=>'Con desgaste normal',         'color'=>'#d97706', 'icon'=>'~'],
              'dañado'       => ['label'=>'Dañado',       'sub'=>'Requiere reparación',         'color'=>'#dc2626', 'icon'=>'✕'],
              'dado_de_baja' => ['label'=>'Dado de baja', 'sub'=>'Fuera de servicio definitivo','color'=>'#6b7280', 'icon'=>'⊘'],
            ] as $val => $info)
              <label class="flex items-start gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all hover:bg-gray-50"
                     :style="estadoData.estado_fisico === '{{ $val }}' ? 'border-color:{{ $info['color'] }};background:{{ $info['color'] }}11' : 'border-color:#e2e8f0'"
                     @click="estadoData.estado_fisico = '{{ $val }}'">
                <input type="radio" name="estado_fisico" value="{{ $val }}"
                       :checked="estadoData.estado_fisico === '{{ $val }}'" class="sr-only"/>
                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                      style="background:{{ $info['color'] }}">{{ $info['icon'] }}</span>
                <div>
                  <p class="text-sm font-semibold text-gray-800">{{ $info['label'] }}</p>
                  <p class="text-xs text-gray-400">{{ $info['sub'] }}</p>
                </div>
              </label>
            @endforeach
          </div>
        </div>

        {{-- Observación --}}
        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="text-xs font-semibold text-gray-700">Observación / Motivo</label>
            <span class="text-xs font-semibold tabular-nums"
                  :class="obsLen > 180 ? (obsLen >= 200 ? 'text-red-500' : 'text-yellow-500') : 'text-gray-400'"
                  x-text="obsLen + ' / 200'"></span>
          </div>
          <textarea name="observacion" rows="3" maxlength="200"
                    x-model="obsText"
                    @input="obsLen = obsText.length"
                    class="field resize-none"
                    placeholder="Ej: Pantalla rota, no enciende, mantenimiento preventivo..."></textarea>
          <div class="mt-1 h-1 rounded-full bg-gray-100 overflow-hidden">
            <div class="h-full rounded-full transition-all"
                 :style="'width:' + (obsLen/200*100) + '%;background:' + (obsLen >= 200 ? '#ef4444' : obsLen > 180 ? '#f59e0b' : '#E0176C')"></div>
          </div>
        </div>

        {{-- Ubicación --}}
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Ubicación de almacenamiento</label>
          <select name="ubicacion_almacenamiento" class="field"
                  x-init="$el.value = estadoData.ubicacion">
            <option value="">Sin ubicación</option>
            <option value="Sala TI">Sala TI</option>
            <option value="Secretaria">Secretaria</option>
          </select>
        </div>

        <div class="flex gap-3 pt-1">
          <button type="button" @click="showEstado=false"
            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50">
            Cancelar
          </button>
          <button type="submit" class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm"
                  style="background:#E0176C">
            Guardar cambio
          </button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>window.SIGPA_PAGE = { hasErrors: {{ $errors->any() ? 'true' : 'false' }} };</script>
@endsection
