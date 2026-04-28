{{-- resources/views/admin/equipos/index.blade.php --}}
@extends('layouts.dashboard')
@section('title', 'Gestión de Equipos')
@section('accent-color', 'var(--magenta)')
@section('role-label', '⭐ Administrador')
@section('page-title', 'Equipos Tecnológicos')
@section('page-subtitle', 'Inventario y control de préstamos')

@section('sidebar-nav')
  <p class="nav-section">Principal</p>
  <a href="{{ route('dashboard.admin') }}" class="nav-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1" stroke="currentColor" stroke-width="2"/><rect x="14" y="3" width="7" height="5" rx="1" stroke="currentColor" stroke-width="2"/><rect x="14" y="12" width="7" height="9" rx="1" stroke="currentColor" stroke-width="2"/><rect x="3" y="16" width="7" height="5" rx="1" stroke="currentColor" stroke-width="2"/></svg>Dashboard</a>
  <p class="nav-section mt-2">Gestión</p>
  <a href="{{ route('admin.usuarios.index') }}" class="nav-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/></svg>Usuarios</a>
  <a href="{{ route('admin.roles.index') }}" class="nav-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>Roles</a>
  <a href="{{ route('admin.aulas.index') }}" class="nav-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="2"/></svg>Aulas</a>
  <a href="{{ route('admin.equipos.index') }}" class="nav-item active" style="background:rgba(224,23,108,.15);color:#f472b6;"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 19v2M16 19v2M5 19h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>Equipos</a>
  <p class="nav-section mt-2">Análisis</p>
  <a href="{{ route('admin.reportes.index') }}" class="nav-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M9 17H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2h-2M9 17v4m6-4v4M9 21h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>Reportes</a>
  <a href="{{ route('admin.configuracion') }}" class="nav-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/><path d="M12 1v2M12 21v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M1 12h2M21 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>Configuración</a>
@endsection



@section('content')
<div x-data="equiposAdminApp()">

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
    <div>
      <h2 style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;">Inventario de equipos</h2>
      <p class="text-gray-500 text-sm mt-0.5">{{ $equipos->total() }} equipos registrados</p>
    </div>
    <button @click="openCreate()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-semibold text-sm"
      style="background:var(--magenta);box-shadow:0 4px 16px rgba(224,23,108,.3)">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg>
      Nuevo Equipo
    </button>
  </div>

  {{-- Stats --}}
  <div class="grid grid-cols-3 gap-4 mb-5">
    @foreach([
      ['Disponibles', $equiposDisponibles, 'var(--green)',   'rgba(0,182,122,.08)'],
      ['Prestados',   $equiposPrestados,   'var(--magenta)', 'rgba(224,23,108,.08)'],
      ['Total',       $totalEquipos,       'var(--blue)',     'rgba(26,79,214,.08)'],
    ] as [$lbl,$val,$col,$bg])
    <div class="bg-white rounded-2xl p-4 shadow-sm text-center">
      <p style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:{{ $col }}">{{ $val }}</p>
      <p class="text-gray-500 text-xs mt-1">{{ $lbl }}</p>
    </div>
    @endforeach
  </div>

  {{-- Filtros --}}
  <div class="bg-white rounded-2xl shadow-sm p-4 mb-5 flex flex-wrap gap-3">
    <div class="relative flex-1 min-w-48">
      <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="15" height="15" fill="none" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      <input type="text" x-model="search" placeholder="Buscar equipo o código..." class="field pl-9"/>
    </div>
    <select x-model="filterDisp" class="field w-auto min-w-40">
      <option value="">Todos</option>
      <option value="1">Disponibles</option>
      <option value="prestado">En préstamo</option>
      <option value="deshabilitado">Deshabilitados</option>
    </select>
    <select x-model="filterEstado" class="field w-auto min-w-40">
      <option value="">Todos los estados</option>
      <option value="bueno">Bueno</option>
      <option value="regular">Regular</option>
      <option value="dañado">Dañado</option>
      <option value="dado_de_baja">Dado de baja</option>
    </select>
  </div>

  {{-- Tabla --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b border-gray-100">
          <tr class="text-gray-400 text-xs uppercase tracking-wider">
            <th class="text-left px-5 py-3 font-medium">Equipo</th>
            <th class="text-left px-5 py-3 font-medium">Código inventario</th>
            <th class="text-left px-5 py-3 font-medium">Marca / Modelo</th>
            <th class="text-left px-5 py-3 font-medium">Estado físico</th>
            <th class="text-left px-5 py-3 font-medium">Disponibilidad</th>
            <th class="text-left px-5 py-3 font-medium">Ubicación</th>
            <th class="text-left px-5 py-3 font-medium">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($equipos as $eq)
            @php
              $fueraServicio = in_array($eq->estado_fisico, ['dañado', 'dado_de_baja']);
              if ($eq->disponible) {
                  $dispBadge = 'bg-green-100 text-green-700';
                  $dispLabel = 'Disponible';
              } elseif ($fueraServicio) {
                  $dispBadge = 'bg-gray-100 text-gray-500';
                  $dispLabel = 'Deshabilitado';
              } else {
                  $dispBadge = 'bg-pink-100 text-pink-700';
                  $dispLabel = 'En préstamo';
              }
              $fisicoBadge = ['bueno'=>'bg-green-100 text-green-700','regular'=>'bg-yellow-100 text-yellow-700','dañado'=>'bg-red-100 text-red-600','dado_de_baja'=>'bg-gray-100 text-gray-500'][$eq->estado_fisico] ?? 'bg-gray-100 text-gray-500';
            @endphp
            <tr class="eq-row border-b border-gray-50 transition-colors"
                x-show="matchFilter('{{ strtolower($eq->nombre.' '.$eq->codigo_inventario) }}','{{ $eq->disponible ? '1' : ($fueraServicio ? 'deshabilitado' : 'prestado') }}','{{ $eq->estado_fisico }}')">
              <td class="px-5 py-3.5">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
                       style="background:{{ $eq->disponible ? 'rgba(0,182,122,.1)' : ($fueraServicio ? 'rgba(148,163,184,.12)' : 'rgba(224,23,108,.1)') }}">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24"
                         style="stroke:{{ $eq->disponible ? 'var(--green)' : ($fueraServicio ? '#94a3b8' : 'var(--magenta)') }}">
                      <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="2"/>
                      <path d="M8 19v2M16 19v2M5 19h14" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                  </div>
                  <p class="font-semibold text-gray-800">{{ $eq->nombre }}</p>
                </div>
              </td>
              <td class="px-5 py-3.5 text-gray-500 font-mono text-xs">{{ $eq->codigo_inventario }}</td>
              <td class="px-5 py-3.5 text-gray-500 text-xs">{{ trim(($eq->marca ?? '').' '.($eq->modelo ?? '')) ?: '—' }}</td>
              <td class="px-5 py-3.5"><span class="badge {{ $fisicoBadge }}">{{ ucfirst($eq->estado_fisico) }}</span></td>
              <td class="px-5 py-3.5"><span class="badge {{ $dispBadge }}">{{ $dispLabel }}</span></td>
              <td class="px-5 py-3.5 text-gray-500 text-xs max-w-[140px] truncate">{{ $eq->ubicacion_almacenamiento ?? '—' }}</td>
              <td class="px-5 py-3.5">
                <div class="flex gap-2">
                  <button @click="openEdit({{ $eq->id }})" class="text-xs font-semibold text-blue-600 hover:underline">Editar</button>
                  <form action="{{ route('admin.equipos.destroy', $eq->id) }}" method="POST"
                        onsubmit="return confirm('¿Eliminar {{ $eq->nombre }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs font-semibold text-red-500 hover:underline">Eliminar</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400">No hay equipos registrados.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $equipos->links() }}</div>
  </div>

  {{-- Modal --}}
  <div x-show="showModal" class="modal-overlay" @click.self="showModal=false" style="display:none">
    <div class="modal-box">
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;" x-text="editId ? 'Editar Equipo' : 'Nuevo Equipo'"></h3>
        <button @click="showModal=false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
      </div>
      <form :action="editId ? '/admin/equipos/' + editId : '/admin/equipos'" method="POST" class="px-6 py-5 space-y-4">
        @csrf
        <input type="hidden" name="_method" x-bind:value="editId ? 'PUT' : 'POST'"/>

        {{-- Errores de validación --}}
        @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 space-y-0.5">
          @foreach($errors->all() as $error)
            <p>• {{ $error }}</p>
          @endforeach
        </div>
        @endif

        <div class="grid grid-cols-2 gap-4">
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nombre del equipo *</label>
            <input type="text" name="nombre" x-model="form.nombre" class="field" placeholder="Ej: Video Beam Epson" required/>
            @error('nombre')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Código de inventario *</label>
            <input type="text" name="codigo_inventario" x-model="form.codigo_inventario"
                   @input.debounce.500ms="checkCodigo()"
                   :class="codigoError ? 'field border-red-400 focus:border-red-500 focus:shadow-[0_0_0_3px_rgba(239,68,68,.1)]' : 'field'"
                   placeholder="EQ-001" required/>
            <p x-show="codigoError" x-text="codigoError" class="text-red-500 text-xs mt-1" style="display:none"></p>
            <p x-show="codigoOk" class="text-green-600 text-xs mt-1" style="display:none">Código disponible</p>
            @error('codigo_inventario')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Estado físico *</label>
            <select name="estado_fisico" x-model="form.estado_fisico" class="field" required>
              <option value="bueno">Bueno</option>
              <option value="regular">Regular</option>
              <option value="dañado">Dañado</option>
              <option value="dado_de_baja">Dado de baja</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Marca</label>
            <input type="text" name="marca" x-model="form.marca" class="field" placeholder="Epson, Dell..."/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Modelo</label>
            <input type="text" name="modelo" x-model="form.modelo" class="field"/>
          </div>
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Ubicación de almacenamiento</label>
            <select name="ubicacion_almacenamiento" x-model="form.ubicacion_almacenamiento" class="field">
              <option value="">Sin ubicación</option>
              <option value="Sala TI">Sala TI</option>
              <option value="Secretaria">Secretaria</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Fecha adquisición</label>
            <input type="date" name="fecha_adquisicion" x-model="form.fecha_adquisicion" class="field"/>
          </div>
          <div class="flex items-end gap-4">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" name="disponible" value="1" x-model="form.disponible" class="w-4 h-4 rounded" style="accent-color:var(--green)"/>
              <span class="text-sm text-gray-700">Disponible</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" name="activo" value="1" x-model="form.activo" class="w-4 h-4 rounded" style="accent-color:var(--blue)"/>
              <span class="text-sm text-gray-700">Activo</span>
            </label>
          </div>
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Descripción</label>
            <textarea name="descripcion" x-model="form.descripcion" class="field" rows="2"></textarea>
          </div>
        </div>
        <div class="flex gap-3 pt-2">
          <button type="button" @click="showModal=false" class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50">Cancelar</button>
          <button type="submit" :disabled="!!codigoError"
                  :class="codigoError ? 'opacity-50 cursor-not-allowed' : ''"
                  class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm" style="background:var(--magenta)"
                  x-text="editId ? 'Actualizar' : 'Registrar Equipo'"></button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection

@section('scripts')
@php
  $pageData = [
    'equiposData'   => $equipos->getCollection()->keyBy('id'),
    'checkCodigoUrl'=> route('admin.equipos.check-codigo'),
    'hasErrors'     => $errors->any(),
    'editId'        => $errors->any() && old('_method') === 'PUT' ? request()->route('equipo') : null,
    'codigoError'   => $errors->first('codigo_inventario'),
    'oldForm'       => $errors->any() ? [
      'nombre'                   => old('nombre'),
      'codigo_inventario'        => old('codigo_inventario'),
      'estado_fisico'            => old('estado_fisico', 'bueno'),
      'marca'                    => old('marca'),
      'modelo'                   => old('modelo'),
      'ubicacion_almacenamiento' => old('ubicacion_almacenamiento'),
      'fecha_adquisicion'        => old('fecha_adquisicion'),
      'disponible'               => true,
      'activo'                   => true,
      'descripcion'              => old('descripcion'),
    ] : null,
  ];
@endphp
<script>window.SIGPA_PAGE = @json($pageData);</script>
@endsection