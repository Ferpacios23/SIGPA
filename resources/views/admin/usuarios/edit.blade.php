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
<div class="max-w-2xl mx-auto">

  {{-- Breadcrumb --}}
  <div class="flex items-center gap-2 text-sm text-gray-500 mb-5">
    <a href="{{ route('admin.usuarios.index') }}" class="hover:text-blue-600 transition-colors">Usuarios</a>
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    <span class="text-gray-800 font-medium">Editar: {{ $usuario->name }}</span>
  </div>

  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    {{-- User header --}}
    <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex items-center gap-4">
      <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg flex-shrink-0"
           style="background:rgba(26,79,214,.12);color:var(--blue)">
        {{ strtoupper(substr($usuario->name, 0, 1)) }}
      </div>
      <div>
        <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;">{{ $usuario->name }}</h3>
        <p class="text-gray-400 text-xs mt-0.5">{{ $usuario->email }}</p>
      </div>
    </div>

    <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST" class="px-6 py-5 space-y-4">
      @csrf
      @method('PUT')

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
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nueva contraseña <span class="text-gray-400 font-normal">(opcional)</span></label>
          <input type="password" name="password"
                 class="field @error('password') is-invalid @enderror" placeholder="Dejar en blanco para mantener" minlength="8"/>
          @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Confirmar nueva contraseña</label>
          <input type="password" name="password_confirmation" class="field" placeholder="Repite la nueva contraseña"/>
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
          <input type="text" name="identificacion"
                 value="{{ old('identificacion', $usuario->profile?->identificacion) }}"
                 class="field @error('identificacion') is-invalid @enderror" maxlength="20"/>
          @error('identificacion')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Teléfono</label>
          <input type="text" name="telefono"
                 value="{{ old('telefono', $usuario->profile?->telefono) }}"
                 class="field @error('telefono') is-invalid @enderror" maxlength="20"/>
          @error('telefono')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Dependencia / Facultad</label>
          <input type="text" name="dependencia"
                 value="{{ old('dependencia', $usuario->profile?->dependencia) }}"
                 class="field @error('dependencia') is-invalid @enderror"/>
          @error('dependencia')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2 flex items-center gap-2 pt-1">
          <input type="hidden" name="activo" value="0"/>
          <input type="checkbox" name="activo" value="1" id="activo_edit"
                 {{ old('activo', $usuario->profile?->activo ?? true) ? 'checked' : '' }}
                 class="w-4 h-4 rounded" style="accent-color:var(--blue)"/>
          <label for="activo_edit" class="text-sm text-gray-700 cursor-pointer">Usuario activo</label>
        </div>
      </div>

      <div class="flex gap-3 pt-2">
        <a href="{{ route('admin.usuarios.index') }}"
           class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50 transition-colors text-center">
          Cancelar
        </a>
        <button type="submit"
                class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm transition-all hover:brightness-110"
                style="background:var(--blue)">
          Guardar cambios
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
