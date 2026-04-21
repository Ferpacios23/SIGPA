{{-- resources/views/admin/usuarios/index.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Gestión de Usuarios')
@section('accent-color', 'var(--blue)')
@section('role-label', '⭐ Administrador')
@section('page-title', 'Usuarios')
@section('page-subtitle', 'Administración de cuentas del sistema')

@section('sidebar-nav')
  <p class="nav-section">Principal</p>
  <a href="{{ route('dashboard.admin') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1" stroke="currentColor" stroke-width="2"/><rect x="14" y="3" width="7" height="5" rx="1" stroke="currentColor" stroke-width="2"/><rect x="14" y="12" width="7" height="9" rx="1" stroke="currentColor" stroke-width="2"/><rect x="3" y="16" width="7" height="5" rx="1" stroke="currentColor" stroke-width="2"/></svg>
    Dashboard
  </a>
  <p class="nav-section mt-2">Gestión</p>
  <a href="{{ route('admin.usuarios.index') }}" class="nav-item active" style="background:rgba(26,79,214,.2);color:#6ea0ff;">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/></svg>
    Usuarios
  </a>
  <a href="{{ route('admin.roles.index') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
    Roles
  </a>
  <a href="{{ route('admin.aulas.index') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="2"/></svg>
    Aulas
  </a>
  <a href="{{ route('admin.equipos.index') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 19v2M16 19v2M5 19h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    Equipos
  </a>
  <p class="nav-section mt-2">Análisis</p>
  <a href="{{ route('admin.reportes.index') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M9 17H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2h-2M9 17v4m6-4v4M9 21h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    Reportes
  </a>
  <a href="{{ route('admin.configuracion') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/><path d="M19.07 4.93l-1.41 1.41M1 12h2M21 12h2M4.93 19.07l1.41-1.41M4.93 4.93l1.41 1.41M19.07 19.07l-1.41-1.41M12 1v2M12 21v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    Configuración
  </a>
@endsection



@section('content')
<div x-data="usuariosApp()">

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
    <div>
      <h2 style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;">Usuarios del sistema</h2>
      <p class="text-gray-500 text-sm mt-0.5">{{ $users->total() }} usuarios registrados</p>
    </div>
    <button @click="openCreate()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-semibold text-sm"
      style="background:var(--blue);box-shadow:0 4px 16px rgba(26,79,214,.3)">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg>
      Nuevo Usuario
    </button>
  </div>

  {{-- Search --}}
  <div class="bg-white rounded-2xl shadow-sm p-4 mb-5">
    <div class="relative">
      <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="15" height="15" fill="none" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      <input type="text" x-model="search" placeholder="Buscar por nombre o correo..." class="field pl-9"/>
    </div>
  </div>

  {{-- Table --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b border-gray-100">
          <tr class="text-gray-400 text-xs uppercase tracking-wider">
            <th class="text-left px-5 py-3 font-medium">Usuario</th>
            <th class="text-left px-5 py-3 font-medium">Identificación</th>
            <th class="text-left px-5 py-3 font-medium">Rol</th>
            <th class="text-left px-5 py-3 font-medium">Dependencia</th>
            <th class="text-left px-5 py-3 font-medium">Estado</th>
            <th class="text-left px-5 py-3 font-medium">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $u)
            @php
              $rSlug  = $u->profile?->role?->slug ?? '';
              $rName  = $u->profile?->role?->nombre ?? '—';
              $activo = $u->profile?->activo ?? true;
              $colors = [
                'admin'      => ['badge'=>'bg-blue-100 text-blue-700',   'av'=>'rgba(26,79,214,.15)',  'avc'=>'#1A4FD6'],
                'secretaria' => ['badge'=>'bg-green-100 text-green-700', 'av'=>'rgba(0,182,122,.15)',  'avc'=>'#00B67A'],
                'tecnico'    => ['badge'=>'bg-pink-100 text-pink-700',   'av'=>'rgba(224,23,108,.15)', 'avc'=>'#E0176C'],
              ];
              $c = $colors[$rSlug] ?? ['badge'=>'bg-gray-100 text-gray-600','av'=>'rgba(100,116,139,.15)','avc'=>'#64748b'];
            @endphp
            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors"
                x-show="!search || '{{ strtolower($u->name.' '.$u->email) }}'.includes(search.toLowerCase())">
              <td class="px-5 py-3.5">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0"
                       style="background:{{ $c['av'] }};color:{{ $c['avc'] }}">
                    {{ strtoupper(substr($u->name,0,1)) }}
                  </div>
                  <div>
                    <p class="font-semibold text-gray-800">{{ $u->name }}</p>
                    <p class="text-xs text-gray-400">{{ $u->email }}</p>
                  </div>
                </div>
              </td>
              <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $u->profile?->identificacion ?? '—' }}</td>
              <td class="px-5 py-3.5"><span class="badge {{ $c['badge'] }}">{{ $rName }}</span></td>
              <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $u->profile?->dependencia ?? '—' }}</td>
              <td class="px-5 py-3.5">
                <span class="badge {{ $activo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                  {{ $activo ? 'Activo' : 'Inactivo' }}
                </span>
              </td>
              <td class="px-5 py-3.5">
                <div class="flex gap-2">
                  <button @click="openEdit({{ $u->id }})" class="text-xs font-semibold text-blue-600 hover:underline">Editar</button>
                  @if($u->id !== auth()->id())
                    <form action="{{ route('admin.usuarios.destroy', $u->id) }}" method="POST"
                          onsubmit="return confirm('¿Eliminar a {{ $u->name }}?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="text-xs font-semibold text-red-500 hover:underline">Eliminar</button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No hay usuarios registrados.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $users->links() }}</div>
  </div>

  {{-- ════ MODAL CREAR/EDITAR ════ --}}
  <div x-show="showModal" class="modal-overlay" @click.self="showModal=false" style="display:none">
    <div class="modal-box">
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;" x-text="editId ? 'Editar Usuario' : 'Nuevo Usuario'"></h3>
        <button @click="showModal=false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
      </div>
      <form :action="editId ? '/admin/usuarios/' + editId : '/admin/usuarios'" method="POST" class="px-6 py-5 space-y-4">
        @csrf
        <input type="hidden" name="_method" x-bind:value="editId ? 'PUT' : 'POST'"/>

        <div class="grid grid-cols-2 gap-4">
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nombre completo *</label>
            <input type="text" name="name" x-model="form.name" class="field" required/>
          </div>
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Correo electrónico *</label>
            <input type="email" name="email" x-model="form.email" class="field" required/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
              <span x-text="editId ? 'Nueva contraseña (opcional)' : 'Contraseña *'"></span>
            </label>
            <input type="password" name="password" x-model="form.password" class="field" :required="!editId" minlength="8"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" class="field" :required="!editId && form.password"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Rol *</label>
            <select name="role_id" x-model="form.role_id" class="field" required>
              <option value="">Seleccionar...</option>
              @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->nombre }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Cédula / Identificación</label>
            <input type="text" name="identificacion" x-model="form.identificacion" class="field" maxlength="20"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Teléfono</label>
            <input type="text" name="telefono" x-model="form.telefono" class="field" maxlength="20"/>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Dependencia / Facultad</label>
            <input type="text" name="dependencia" x-model="form.dependencia" class="field"/>
          </div>
          <div x-show="editId" class="col-span-2 flex items-center gap-2">
            <input type="checkbox" name="activo" value="1" id="activo_u" x-model="form.activo" class="w-4 h-4 rounded" style="accent-color:var(--blue)"/>
            <label for="activo_u" class="text-sm text-gray-700 cursor-pointer">Usuario activo</label>
          </div>
        </div>

        @if($errors->any())
          <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3">
            @foreach($errors->all() as $err)
              <p class="text-red-600 text-xs">• {{ $err }}</p>
            @endforeach
          </div>
        @endif

        <div class="flex gap-3 pt-2">
          <button type="button" @click="showModal=false"
            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50">
            Cancelar
          </button>
          <button type="submit"
            class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm"
            style="background:var(--blue)" x-text="editId ? 'Actualizar' : 'Crear Usuario'">
          </button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>window.SIGPA_PAGE = { usersData: @json($users->getCollection()->keyBy('id')) };</script>
@endsection