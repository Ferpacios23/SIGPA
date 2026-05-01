{{-- resources/views/admin/aulas/index.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Gestión de Aulas')
@section('accent-color', 'var(--green)')
@section('role-label', '⭐ Administrador')
@section('page-title', 'Aulas')
@section('page-subtitle', 'Gestión completa de salones')

@section('sidebar-nav')
  @include('admin.partials.sidebar')
@endsection



@section('content')
<div x-data="aulasApp()" x-init="init()">

  {{-- ── HEADER ── --}}
  <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
    <div>
      <h2 style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;">Aulas registradas</h2>
      <p class="text-gray-500 text-sm mt-0.5">{{ $aulas->total() }} aulas en el sistema</p>
    </div>
    <button @click="openCreate()" class="btn-primary" style="background:var(--green);box-shadow:0 4px 16px rgba(0,182,122,.3)">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg>
      Nueva Aula
    </button>
  </div>

  {{-- ── STATS ── --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    @php
      $total     = $aulas->total();
      $disp      = $aulas->getCollection()->where('estado','disponible')->count();
      $ocup      = $aulas->getCollection()->where('estado','ocupada')->count();
      $mant      = $aulas->getCollection()->where('estado','en_mantenimiento')->count();
    @endphp
    @foreach([
      ['Total',$total,'#1A4FD6','rgba(26,79,214,.08)'],
      ['Disponibles',DB::table('aulas')->where('estado','disponible')->count(),'#00B67A','rgba(0,182,122,.08)'],
      ['Ocupadas',DB::table('aulas')->where('estado','ocupada')->count(),'#E0176C','rgba(224,23,108,.08)'],
      ['Mantenimiento',DB::table('aulas')->where('estado','en_mantenimiento')->count(),'#F76B1C','rgba(247,107,28,.08)'],
    ] as [$lbl,$val,$col,$bg])
    <div class="bg-white rounded-2xl p-4 shadow-sm">
      <div class="w-8 h-8 rounded-xl mb-3 flex items-center justify-center" style="background:{{ $bg }}">
        <div class="dot" style="background:{{ $col }};box-shadow:0 0 0 3px {{ $bg }}"></div>
      </div>
      <p style="font-family:'Syne',sans-serif;font-size:1.6rem;font-weight:800;color:{{ $col }}">{{ $val }}</p>
      <p class="text-gray-500 text-xs mt-0.5">{{ $lbl }}</p>
    </div>
    @endforeach
  </div>

  {{-- ── FILTROS ── --}}
  <div class="bg-white rounded-2xl shadow-sm p-4 mb-5 flex items-center gap-3">
    <div class="relative flex-1 min-w-48">
      <input type="text" x-model="search" placeholder="Buscar por código o nombre..." class="field pl-9"/>
    </div>
    <select x-model="filterEstado" class="field w-auto min-w-40">
      <option value="">Todos los estados</option>
      <option value="disponible">Disponible</option>
      <option value="ocupada">Ocupada</option>
      <option value="en_mantenimiento">En mantenimiento</option>
      <option value="inactiva">Inactiva</option>
    </select>
    <select x-model="viewMode" class="field w-auto">
      <option value="grid">Vista tarjetas</option>
      <option value="table">Vista tabla</option>
    </select>
  </div>

  {{-- ── GRID VIEW ── --}}
  <div x-show="viewMode==='grid'" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
    @forelse($aulas as $aula)
      @php
        $sc = [
          'disponible'       => ['bar'=>'#22c55e','dot'=>'dot-green', 'badge'=>'bg-green-100 text-green-700', 'label'=>'Disponible'],
          'ocupada'          => ['bar'=>'#ef4444','dot'=>'dot-red',   'badge'=>'bg-red-100 text-red-600',     'label'=>'Ocupada'],
          'en_mantenimiento' => ['bar'=>'#f59e0b','dot'=>'dot-yellow','badge'=>'bg-yellow-100 text-yellow-700','label'=>'Mantenimiento'],
          'inactiva'         => ['bar'=>'#94a3b8','dot'=>'dot-gray',  'badge'=>'bg-gray-100 text-gray-500',   'label'=>'Inactiva'],
        ][$aula->estado] ?? ['bar'=>'#94a3b8','dot'=>'dot-gray','badge'=>'bg-gray-100 text-gray-500','label'=>'—'];
      @endphp
      <div class="aula-card"
           x-show="matchFilter('{{ strtolower($aula->codigo.' '.($aula->nombre ?? '')) }}','{{ $aula->estado }}')"
           @click="openEdit({{ $aula->id }})">
        <div class="status-bar" style="background:{{ $sc['bar'] }}"></div>
        <div class="flex items-start justify-between mt-1 mb-3">
          <div>
            <p style="font-family:'Syne',sans-serif;font-weight:800;font-size:.95rem;color:#0d1117;">{{ $aula->codigo }}</p>
            @if($aula->nombre)
              <p class="text-gray-400 text-xs mt-0.5 truncate max-w-[90px]">{{ $aula->nombre }}</p>
            @endif
          </div>
          <span class="dot {{ $sc['dot'] }} mt-1"></span>
        </div>
        <div class="space-y-1.5">
          <div class="flex items-center gap-1.5 text-gray-500 text-xs">
            <svg width="11" height="11" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3" stroke="currentColor" stroke-width="2"/><path d="M5 20c0-3.314 3.134-6 7-6s7 2.686 7 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            {{ $aula->capacidad }} personas
          </div>
          @if($aula->ubicacion)
          <div class="flex items-center gap-1.5 text-gray-500 text-xs">
            <svg width="11" height="11" fill="none" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/></svg>
            <span class="truncate">{{ $aula->ubicacion }}</span>
          </div>
          @endif
        </div>
        <div class="mt-3 flex items-center justify-between">
          <span class="badge {{ $sc['badge'] }}">{{ $sc['label'] }}</span>
          <div class="flex gap-1" @click.stop>
            {{-- Botón horario --}}
            <a href="{{ route('admin.aulas.horarios', $aula->id) }}" @click.stop
              class="w-7 h-7 rounded-lg flex items-center justify-center text-indigo-500 hover:bg-indigo-50 transition-colors"
              title="Gestionar horario académico">
              <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </a>
            <button @click.stop="openEdit({{ $aula->id }})"
              class="w-7 h-7 rounded-lg flex items-center justify-center text-blue-500 hover:bg-blue-50 transition-colors">
              <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
            <button @click.stop="confirmDelete({{ $aula->id }}, '{{ $aula->codigo }}')"
              class="w-7 h-7 rounded-lg flex items-center justify-center text-red-400 hover:bg-red-50 transition-colors">
              <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6M10 11v6M14 11v6M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
          </div>
        </div>
        {{-- Badge de clases programadas --}}
        @if($aula->horarios_activos_count > 0)
        <div class="mt-2 flex items-center gap-1.5 text-xs text-indigo-600 font-semibold">
          <svg width="10" height="10" fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2.5"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
          {{ $aula->horarios_activos_count }} {{ $aula->horarios_activos_count === 1 ? 'clase' : 'clases' }} programada{{ $aula->horarios_activos_count === 1 ? '' : 's' }}
        </div>
        @endif
      </div>
    @empty
      <div class="col-span-full py-16 text-center text-gray-400">
        <svg class="mx-auto mb-3 opacity-40" width="48" height="48" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="1.5"/></svg>
        <p class="font-medium">No hay aulas registradas</p>
        <button @click="openCreate()" class="mt-3 text-sm font-semibold" style="color:var(--green)">+ Agregar primera aula</button>
      </div>
    @endforelse
  </div>

  {{-- ── TABLE VIEW ── --}}
  <div x-show="viewMode==='table'" class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b border-gray-100">
          <tr class="text-gray-400 text-xs uppercase tracking-wider">
            <th class="text-left px-5 py-3 font-medium">Código</th>
            <th class="text-left px-5 py-3 font-medium">Nombre</th>
            <th class="text-left px-5 py-3 font-medium">Capacidad</th>
            <th class="text-left px-5 py-3 font-medium">Ubicación</th>
            <th class="text-left px-5 py-3 font-medium">Estado</th>
            <th class="text-left px-5 py-3 font-medium">Activa</th>
            <th class="text-left px-5 py-3 font-medium">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($aulas as $aula)
            @php
              $sc = [
                'disponible'       => 'bg-green-100 text-green-700',
                'ocupada'          => 'bg-red-100 text-red-600',
                'en_mantenimiento' => 'bg-yellow-100 text-yellow-700',
                'inactiva'         => 'bg-gray-100 text-gray-500',
              ][$aula->estado] ?? 'bg-gray-100 text-gray-500';
            @endphp
            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors"
                x-show="matchFilter('{{ strtolower($aula->codigo.' '.($aula->nombre ?? '')) }}','{{ $aula->estado }}')">
              <td class="px-5 py-3.5 font-bold text-gray-800">{{ $aula->codigo }}</td>
              <td class="px-5 py-3.5 text-gray-600">{{ $aula->nombre ?? '—' }}</td>
              <td class="px-5 py-3.5 text-gray-500">{{ $aula->capacidad }}</td>
              <td class="px-5 py-3.5 text-gray-500 max-w-[160px] truncate">{{ $aula->ubicacion ?? '—' }}</td>
              <td class="px-5 py-3.5"><span class="badge {{ $sc }}">{{ ucfirst(str_replace('_',' ',$aula->estado)) }}</span></td>
              <td class="px-5 py-3.5">
                <span class="badge {{ $aula->activa ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                  {{ $aula->activa ? 'Sí' : 'No' }}
                </span>
              </td>
              <td class="px-5 py-3.5">
                <div class="flex gap-2 items-center">
                  <a href="{{ route('admin.aulas.horarios', $aula->id) }}" class="text-xs font-semibold text-indigo-600 hover:underline flex items-center gap-1">
                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Horario
                    @if($aula->horarios_activos_count > 0)
                      <span class="ml-0.5 bg-indigo-100 text-indigo-600 rounded-full px-1.5 py-0.5 text-[10px]">{{ $aula->horarios_activos_count }}</span>
                    @endif
                  </a>
                  <button @click="openEdit({{ $aula->id }})" class="text-xs font-semibold text-blue-600 hover:underline">Editar</button>
                  <button @click="confirmDelete({{ $aula->id }}, '{{ $aula->codigo }}')" class="text-xs font-semibold text-red-500 hover:underline">Eliminar</button>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400">No hay aulas.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    {{-- Paginación --}}
    <div class="px-5 py-3 border-t border-gray-100">
      {{ $aulas->links() }}
    </div>
  </div>

  {{-- ════ MODAL CREAR / EDITAR ════ --}}
  <div x-show="showModal" class="modal-overlay" @click.self="showModal=false" style="display:none">
    <div class="modal-box">
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <div>
          <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;" x-text="editId ? 'Editar Aula' : 'Nueva Aula'"></h3>
          <p class="text-gray-400 text-xs mt-0.5" x-text="editId ? 'Actualiza los datos del salón' : 'Registra un nuevo salón en el sistema'"></p>
        </div>
        <button @click="showModal=false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
      </div>

      <form :action="editId ? '/admin/aulas/' + editId : '/admin/aulas'" method="POST" class="px-6 py-5 space-y-4">
        @csrf
        <input type="hidden" name="_method" x-bind:value="editId ? 'PUT' : 'POST'"/>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Código *</label>
            <input type="text" name="codigo" x-model="form.codigo" class="field" placeholder="Ej: A101" required/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nombre</label>
            <input type="text" name="nombre" x-model="form.nombre" class="field" placeholder="Nombre descriptivo"/>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Capacidad *</label>
            <input type="number" name="capacidad" x-model="form.capacidad" class="field" placeholder="0" min="1" required/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Estado *</label>
            <select name="estado" x-model="form.estado" class="field" required>
              <option value="disponible">Disponible</option>
              <option value="ocupada">Ocupada</option>
              <option value="en_mantenimiento">En mantenimiento</option>
              <option value="inactiva">Inactiva</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Ubicación</label>
          <input type="text" name="ubicacion" x-model="form.ubicacion" class="field" placeholder="Bloque A, Piso 2"/>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Descripción</label>
          <textarea name="descripcion" x-model="form.descripcion" class="field" rows="2" placeholder="Detalles adicionales del aula"></textarea>
        </div>

        <div x-show="editId" class="flex items-center gap-2">
          <input type="checkbox" name="activa" value="1" id="activa" x-model="form.activa" class="w-4 h-4 rounded" style="accent-color:var(--green)"/>
          <label for="activa" class="text-sm text-gray-700 cursor-pointer">Aula activa</label>
        </div>

        <div class="flex gap-3 pt-2">
          <button type="button" @click="showModal=false"
            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50 transition-colors">
            Cancelar
          </button>
          <button type="submit"
            class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm transition-all hover:brightness-110"
            style="background:var(--green)" x-text="editId ? 'Actualizar' : 'Crear Aula'">
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- ════ MODAL CONFIRMAR ELIMINAR ════ --}}
  <div x-show="showDelete" class="modal-overlay" @click.self="showDelete=false" style="display:none">
    <div class="modal-box" style="max-width:400px">
      <div class="px-6 py-6 text-center">
        <div class="w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center" style="background:rgba(239,68,68,.1)">
          <svg width="24" height="24" fill="none" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6" stroke="#ef4444" stroke-width="2" stroke-linecap="round"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6M10 11v6M14 11v6M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2" stroke="#ef4444" stroke-width="2" stroke-linecap="round"/></svg>
        </div>
        <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;margin-bottom:8px;">¿Eliminar aula?</h3>
        <p class="text-gray-500 text-sm">El aula <strong x-text="deleteCode"></strong> será eliminada del sistema. Esta acción no se puede deshacer.</p>
        <div class="flex gap-3 mt-6">
          <button @click="showDelete=false" class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50">
            Cancelar
          </button>
          <form :action="'/admin/aulas/' + deleteId" method="POST" class="flex-1">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full py-2.5 rounded-xl text-white font-semibold text-sm" style="background:#ef4444">
              Sí, eliminar
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>window.SIGPA_PAGE = { aulasData: @json($aulas->getCollection()->keyBy('id')) };</script>
@endsection