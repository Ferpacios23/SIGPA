{{-- resources/views/admin/docentes/index.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Docentes')
@section('accent-color', 'var(--green)')
@section('role-label', '⭐ Administrador')
@section('page-title', 'Docentes')
@section('page-subtitle', 'Registro de docentes sin acceso al sistema')

@section('sidebar-nav')
  <p class="nav-section">Principal</p>
  <a href="{{ route('dashboard.admin') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1" stroke="currentColor" stroke-width="2"/><rect x="14" y="3" width="7" height="5" rx="1" stroke="currentColor" stroke-width="2"/><rect x="14" y="12" width="7" height="9" rx="1" stroke="currentColor" stroke-width="2"/><rect x="3" y="16" width="7" height="5" rx="1" stroke="currentColor" stroke-width="2"/></svg>
    Dashboard
  </a>
  <p class="nav-section mt-2">Gestión</p>
  <a href="{{ route('admin.usuarios.index') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/></svg>
    Usuarios
  </a>
  <a href="{{ route('admin.docentes.index') }}" class="nav-item active" style="background:rgba(0,182,122,.2);color:#4ade9a;">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/></svg>
    Docentes
  </a>
  <a href="{{ route('admin.aulas.index') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="2"/></svg>
    Aulas
  </a>
  <a href="{{ route('admin.equipos.index') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" stroke="currentColor" stroke-width="2"/></svg>
    Equipos
  </a>
  <a href="{{ route('admin.horarios.index') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    Horarios
  </a>
  <p class="nav-section mt-2">Reportes</p>
  <a href="{{ route('admin.reportes.index') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    Reportes
  </a>
@endsection



@section('content')
<div x-data="docentesApp()" class="space-y-5">

  {{-- Mensajes flash --}}
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
      <h2 style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;">Docentes</h2>
      <p class="text-sm text-gray-500 mt-0.5">Estos registros no tienen acceso de login al sistema</p>
    </div>
    <button @click="showCreate=true"
      class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-semibold text-sm"
      style="background:var(--green);box-shadow:0 4px 16px rgba(0,182,122,.3)">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg>
      Nuevo Docente
    </button>
  </div>

  {{-- Filtro búsqueda --}}
  <form method="GET" action="{{ route('admin.docentes.index') }}"
        class="bg-white rounded-2xl shadow-sm p-4 flex flex-wrap gap-3 items-end">
    <div class="relative flex-1 min-w-48">
      <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="15" height="15" fill="none" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, cédula o dependencia..." class="field pl-9"/>
    </div>
    <div>
      <label class="block text-xs font-semibold text-gray-600 mb-1">Estado</label>
      <select name="activo" class="field w-auto">
        <option value="">Todos</option>
        <option value="1" {{ request('activo')==='1' ? 'selected' : '' }}>Activos</option>
        <option value="0" {{ request('activo')==='0' ? 'selected' : '' }}>Inactivos</option>
      </select>
    </div>
    <button type="submit" class="px-4 py-2.5 rounded-xl text-white font-semibold text-sm" style="background:var(--green)">Buscar</button>
    @if(request()->hasAny(['search','activo']))
      <a href="{{ route('admin.docentes.index') }}" class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50">Limpiar</a>
    @endif
  </form>

  {{-- Tabla --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b border-gray-100">
          <tr class="text-gray-400 text-xs uppercase tracking-wider">
            <th class="text-left px-5 py-3 font-medium">Nombre</th>
            <th class="text-left px-5 py-3 font-medium">Identificación</th>
            <th class="text-left px-5 py-3 font-medium">Dependencia</th>
            <th class="text-left px-5 py-3 font-medium">Contacto</th>
            <th class="text-left px-5 py-3 font-medium">Estado</th>
            <th class="text-left px-5 py-3 font-medium">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($docentes as $d)
            <tr class="border-b border-gray-50 hover:bg-gray-50/70 transition-colors">
              <td class="px-5 py-3.5">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white"
                       style="background:var(--green)">
                    {{ strtoupper(substr($d->name,0,1)) }}
                  </div>
                  <div>
                    <p class="font-semibold text-gray-800">{{ $d->name }}</p>
                    <p class="text-xs text-gray-400">{{ $d->email }}</p>
                  </div>
                </div>
              </td>
              <td class="px-5 py-3.5 text-gray-600 text-sm font-medium">
                {{ $d->profile?->identificacion ?? '—' }}
              </td>
              <td class="px-5 py-3.5 text-gray-600 text-sm">
                {{ $d->profile?->dependencia ?? '—' }}
              </td>
              <td class="px-5 py-3.5 text-gray-500 text-xs">
                {{ $d->profile?->telefono ?? '—' }}
              </td>
              <td class="px-5 py-3.5">
                @if($d->profile?->activo)
                  <span class="badge bg-green-100 text-green-700">Activo</span>
                @else
                  <span class="badge bg-gray-100 text-gray-500">Inactivo</span>
                @endif
              </td>
              <td class="px-5 py-3.5">
                <div class="flex gap-3">
                  <button @click="openEdit({{ json_encode([
                    'id'             => $d->id,
                    'name'           => $d->name,
                    'email'          => $d->email,
                    'identificacion' => $d->profile?->identificacion ?? '',
                    'telefono'       => $d->profile?->telefono ?? '',
                    'dependencia'    => $d->profile?->dependencia ?? '',
                    'activo'         => $d->profile?->activo ? 1 : 0,
                  ]) }})"
                  class="text-xs font-semibold text-blue-600 hover:underline">
                    Editar
                  </button>
                  <form method="POST" action="{{ route('admin.docentes.destroy', $d->id) }}"
                        onsubmit="return confirm('¿Eliminar al docente {{ addslashes($d->name) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs font-semibold text-red-500 hover:underline">Eliminar</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                <svg class="mx-auto mb-3 opacity-40" width="40" height="40" fill="none" viewBox="0 0 24 24">
                  <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                  <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="1.5"/>
                  <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <p class="text-sm font-medium">No hay docentes registrados</p>
                <p class="text-xs mt-1">Registra el primer docente con el botón superior</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $docentes->links() }}</div>
  </div>

  {{-- ════ MODAL CREAR DOCENTE ════ --}}
  <div x-show="showCreate" class="modal-overlay" @click.self="showCreate=false" style="display:none">
    <div class="modal-box">
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <div>
          <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;">Registrar Docente</h3>
          <p class="text-gray-400 text-xs mt-0.5">Sin acceso al sistema (RF39)</p>
        </div>
        <button @click="showCreate=false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
      </div>
      <form method="POST" action="{{ route('admin.docentes.store') }}" class="px-6 py-5 space-y-4">
        @csrf
        @if($errors->any())
          <div class="p-3 rounded-xl text-xs font-medium" style="background:rgba(239,68,68,.08);color:#dc2626;border:1px solid rgba(239,68,68,.15)">
            {{ $errors->first() }}
          </div>
        @endif

        <div class="grid grid-cols-2 gap-4">
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nombre completo *</label>
            <input type="text" name="name" value="{{ old('name') }}" class="field" placeholder="Ej: Carlos Alberto Rojas" required/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Correo electrónico *</label>
            <input type="email" name="email" value="{{ old('email') }}" class="field" placeholder="docente@uniclaretiana.edu.co" required/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">No. Identificación</label>
            <input type="text" name="identificacion" value="{{ old('identificacion') }}" class="field" placeholder="Cédula..."/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Departamento / Facultad</label>
            <input type="text" name="dependencia" value="{{ old('dependencia') }}" class="field" placeholder="Ej: Ingeniería de Sistemas"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Teléfono</label>
            <input type="text" name="telefono" value="{{ old('telefono') }}" class="field" placeholder="3001234567"/>
          </div>
        </div>

        <div class="p-3 rounded-xl flex items-start gap-2 text-xs"
             style="background:rgba(234,179,8,.08);color:#854d0e;border:1px solid rgba(234,179,8,.2)">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" class="shrink-0 mt-0.5"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          El docente <strong>no podrá iniciar sesión</strong>. Solo es un registro de referencia para asignar préstamos.
        </div>

        <div class="flex gap-3 pt-1">
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

  {{-- ════ MODAL EDITAR DOCENTE ════ --}}
  <div x-show="showEdit" class="modal-overlay" @click.self="showEdit=false; editData={}" style="display:none">
    <div class="modal-box">
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <div>
          <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;">Editar Docente</h3>
          <p class="text-gray-400 text-xs mt-0.5" x-text="editData.name"></p>
        </div>
        <button @click="showEdit=false; editData={}" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
      </div>
      <form :action="'/admin/docentes/' + editData.id" method="POST" class="px-6 py-5 space-y-4">
        @csrf @method('PUT')

        <div class="grid grid-cols-2 gap-4">
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nombre completo *</label>
            <input type="text" name="name" :value="editData.name" class="field" required/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Correo electrónico *</label>
            <input type="email" name="email" :value="editData.email" class="field" required/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">No. Identificación</label>
            <input type="text" name="identificacion" :value="editData.identificacion" class="field"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Departamento / Facultad</label>
            <input type="text" name="dependencia" :value="editData.dependencia" class="field"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Teléfono</label>
            <input type="text" name="telefono" :value="editData.telefono" class="field"/>
          </div>
        </div>

        <div>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="hidden" name="activo" value="0"/>
            <input type="checkbox" name="activo" value="1" :checked="editData.activo == 1"
                   class="rounded accent-green-500 w-4 h-4"/>
            <span class="text-sm font-medium text-gray-700">Docente activo</span>
          </label>
        </div>

        <div class="flex gap-3 pt-1">
          <button type="button" @click="showEdit=false; editData={}"
            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50">
            Cancelar
          </button>
          <button type="submit" class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm hover:brightness-110" style="background:var(--green)">
            Guardar cambios
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
