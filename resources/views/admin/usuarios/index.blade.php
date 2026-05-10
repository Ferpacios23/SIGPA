{{-- resources/views/admin/usuarios/index.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Gestión de Usuarios')
@section('accent-color', 'var(--blue)')
@section('role-label', '⭐ Administrador')
@section('page-title', 'Usuarios')
@section('page-subtitle', 'Administración de cuentas del sistema')

@section('sidebar-nav')
  @include('admin.partials.sidebar')
@endsection

@section('content')
<div>

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
    <div>
      <h2 class="page-heading">Usuarios del sistema</h2>
      <p class="text-gray-500 text-sm mt-0.5">{{ $users->total() }} usuarios registrados</p>
    </div>
    <a href="{{ route('admin.usuarios.create') }}"
       class="btn-blue inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-semibold text-sm">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
        <path d="M12 5v14M5 12h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
      </svg>
      Nuevo Usuario
    </a>
  </div>

  {{-- Filtros --}}
  <form method="GET" action="{{ route('admin.usuarios.index') }}" class="bg-white rounded-2xl shadow-sm p-4 mb-5">
    <div class="flex items-center gap-3">
      <div class="relative flex-1 min-w-[180px]">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="15" height="15" fill="none" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
          <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <input type="text" name="search" value="{{ $search }}" placeholder="  Buscar por nombre o correo..." class="field pl-9"/>
      </div>
      <div class="min-w-[150px]">
        <select name="rol" class="field">
          <option value="">Todos los roles</option>
          @foreach($roles as $role)
            <option value="{{ $role->id }}" {{ $rolId == $role->id ? 'selected' : '' }}>{{ $role->nombre }}</option>
          @endforeach
        </select>
      </div>
      <div class="min-w-[140px]">
        <select name="estado" class="field">
          <option value="">Todos los estados</option>
          <option value="1" {{ $estado === '1' ? 'selected' : '' }}>Activo</option>
          <option value="0" {{ $estado === '0' ? 'selected' : '' }}>Inactivo</option>
        </select>
      </div>
      <button type="submit" class="btn-blue px-4 py-2 rounded-xl text-white font-semibold text-sm">Buscar</button>
      @if($search || $rolId || $estado !== null && $estado !== '')
        <a href="{{ route('admin.usuarios.index') }}" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50">Limpiar</a>
      @endif
    </div>
  </form>

  {{-- Tabla --}}
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
            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
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
                  <a href="{{ route('admin.usuarios.edit', $u->id) }}" class="text-xs font-semibold text-blue-600 hover:underline">Editar</a>
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

</div>
@endsection
