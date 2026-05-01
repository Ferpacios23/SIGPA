{{-- resources/views/admin/usuarios/edit.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Editar Usuario')
@section('accent-color', 'var(--blue)')
@section('role-label', '⭐ Administrador')
@section('page-title', 'Editar Usuario')
@section('page-subtitle', 'Modificar datos de la cuenta')

@section('sidebar-nav')
  @include('admin.partials.sidebar')
@endsection

@section('content')
@php
  $esSelf      = $usuario->id === auth()->id();
  $activoActual = old('activo', $usuario->profile?->activo ?? true);
@endphp

<div class="max-w-2xl mx-auto space-y-4">

  {{-- Breadcrumb --}}
  <div class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('admin.usuarios.index') }}" class="hover:text-blue-600 transition-colors">Usuarios</a>
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    <span class="text-gray-800 font-medium truncate">{{ $usuario->name }}</span>
  </div>

  {{-- ── TARJETA PRINCIPAL ── --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

    {{-- Header del usuario --}}
    <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex items-center gap-4">
      <div class="usuario-avatar w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg flex-shrink-0">
        {{ strtoupper(substr($usuario->name, 0, 1)) }}
      </div>
      <div class="flex-1 min-w-0">
        <h3 class="usuario-nombre truncate">{{ $usuario->name }}</h3>
        <p class="text-gray-400 text-xs mt-0.5">{{ $usuario->email }}</p>
      </div>
      <span class="badge flex-shrink-0 text-xs font-semibold {{ $activoActual ? 'badge-activo' : 'badge-inactivo' }}">
        <span class="badge-dot w-1.5 h-1.5 rounded-full mr-1 inline-block"></span>
        {{ $activoActual ? 'Activo' : 'Inactivo' }}
      </span>
    </div>

    {{-- Formulario --}}
    <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST" class="px-6 py-5 space-y-5">
      @csrf
      @method('PUT')

      {{-- Campos --}}
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        <div class="sm:col-span-2">
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nombre completo *</label>
          <input type="text" name="name" value="{{ old('name', $usuario->name) }}"
                 class="field @error('name') is-invalid @enderror" required/>
          @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Correo electrónico *</label>
          <input type="email" name="email" value="{{ old('email', $usuario->email) }}"
                 class="field @error('email') is-invalid @enderror" required/>
          @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">
            Nueva contraseña
            <span class="text-gray-400 font-normal">(opcional)</span>
          </label>
          <input type="password" name="password" minlength="8" placeholder="Dejar en blanco para mantener"
                 class="field @error('password') is-invalid @enderror"/>
          @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Confirmar contraseña</label>
          <input type="password" name="password_confirmation" placeholder="Repite la nueva contraseña"
                 class="field"/>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Rol *</label>
          <select name="role_id" class="field @error('role_id') is-invalid @enderror" required>
            <option value="">Seleccionar rol...</option>
            @foreach($roles as $role)
              <option value="{{ $role->id }}"
                {{ old('role_id', $usuario->profile?->role_id) == $role->id ? 'selected' : '' }}>
                {{ $role->nombre }}
              </option>
            @endforeach
          </select>
          @error('role_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Cédula / Identificación</label>
          <input type="text" name="identificacion" maxlength="20"
                 value="{{ old('identificacion', $usuario->profile?->identificacion) }}"
                 class="field @error('identificacion') is-invalid @enderror"/>
          @error('identificacion')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Teléfono</label>
          <input type="text" name="telefono" maxlength="20"
                 value="{{ old('telefono', $usuario->profile?->telefono) }}"
                 class="field @error('telefono') is-invalid @enderror"/>
          @error('telefono')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Dependencia / Facultad</label>
          <input type="text" name="dependencia"
                 value="{{ old('dependencia', $usuario->profile?->dependencia) }}"
                 class="field @error('dependencia') is-invalid @enderror"/>
          @error('dependencia')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
      </div>

      {{-- ── Estado de la cuenta ── --}}
      @if($esSelf)
        <input type="hidden" name="activo" value="1"/>
        <div class="estado-panel-locked flex items-center justify-between gap-4 rounded-xl border border-gray-200 px-4 py-3">
          <div>
            <p class="text-sm font-semibold text-gray-800">Estado de la cuenta</p>
            <p class="text-xs mt-0.5 text-green-600">Activo · no puedes desactivar tu propia cuenta</p>
          </div>
          <div class="flex items-center gap-2 flex-shrink-0">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" class="text-gray-400">
              <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="2"/>
              <path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <div class="toggle-locked relative w-11 h-6 rounded-full">
              <span class="absolute top-0.5 right-0.5 w-5 h-5 bg-white rounded-full shadow-sm"></span>
            </div>
          </div>
        </div>
      @else
        <div x-data="{ activo: {{ $activoActual ? 'true' : 'false' }} }"
             class="estado-panel flex items-center justify-between gap-4 rounded-xl border border-gray-100 px-4 py-3">
          <div>
            <p class="text-sm font-semibold text-gray-800">Estado de la cuenta</p>
            <p class="text-xs mt-0.5"
               :style="activo ? 'color:var(--green)' : 'color:var(--magenta)'"
               x-text="activo ? 'Activo · el usuario puede iniciar sesión' : 'Inactivo · acceso bloqueado'">
            </p>
          </div>
          <div class="flex items-center gap-2 flex-shrink-0">
            <input type="hidden" name="activo" value="0"/>
            <input type="checkbox" name="activo" value="1" id="activo_edit" x-model="activo" class="sr-only"/>
            <button type="button" @click="activo = !activo" role="switch" :aria-checked="activo.toString()"
                    class="toggle-btn relative w-11 h-6 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-1"
                    :class="activo ? 'toggle-on-green' : 'toggle-off'">
              
            </button>
          </div>
        </div>
      @endif

      {{-- ── Botones de acción ── --}}
      <div class="flex items-center gap-3 pt-1">
        <a href="{{ route('admin.usuarios.index') }}"
           class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition-colors text-center">
          Cancelar
        </a>
        <button type="submit"
                class="btn-save flex-1 py-2.5 rounded-xl text-white font-semibold text-sm transition-all hover:brightness-110 flex items-center justify-center gap-2">
          <svg width="15" height="15" fill="none" viewBox="0 0 24 24">
            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M17 21v-8H7v8M7 3v5h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          Guardar cambios
        </button>
      </div>
    </form>
  </div>

  {{-- ── Zona de peligro ── --}}
  @unless($esSelf)
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 flex items-center justify-between gap-4">
      <div>
        <p class="text-sm font-semibold text-danger">Eliminar usuario</p>
        <p class="text-xs text-gray-400 mt-0.5">Esta acción es permanente e irreversible</p>
      </div>
      <form action="{{ route('admin.usuarios.destroy', $usuario->id) }}" method="POST"
            onsubmit="return confirm('¿Eliminar a {{ addslashes($usuario->name) }}? Esta acción no se puede deshacer.')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-danger flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
            <polyline points="3 6 5 6 21 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6M10 11v6M14 11v6M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          Eliminar usuario
        </button>
      </form>
    </div>
  </div>
  @endunless

</div>
@endsection
