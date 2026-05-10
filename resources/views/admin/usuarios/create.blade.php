{{-- resources/views/admin/usuarios/create.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Nuevo Usuario')
@section('accent-color', 'var(--blue)')
@section('role-label', '⭐ Administrador')
@section('page-title', 'Nuevo Usuario')
@section('page-subtitle', 'Crear cuenta en el sistema')

@section('sidebar-nav')
  @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-4">

  {{-- Breadcrumb --}}
  <div class="flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('admin.usuarios.index') }}" class="hover:text-blue-600 transition-colors">Usuarios</a>
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
      <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </svg>
    <span class="text-gray-800 font-medium">Nuevo usuario</span>
  </div>

  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

    {{-- Header --}}
    <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex items-center gap-4">
      <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0"
           style="background:rgba(26,79,214,.08)">
        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" style="color:var(--blue)">
          <path d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          <path d="M6 21v-1a6 6 0 0 1 12 0v1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          <path d="M19 8h2M21 8v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
      </div>
      <div>
        <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.05rem;color:#111827">
          Nuevo usuario
        </h3>
        <p class="text-gray-400 text-xs mt-0.5">Completa la información para crear la cuenta</p>
      </div>
    </div>

    <form action="{{ route('admin.usuarios.store') }}" method="POST" class="px-6 py-5 space-y-6">
      @csrf

      {{-- ── Sección: Acceso al sistema ── --}}
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">
          Acceso al sistema
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

          <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nombre completo *</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="field @error('name') is-invalid @enderror"
                   placeholder="Ej: Juan García" required/>
            @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>

          <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Correo electrónico *</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="field @error('email') is-invalid @enderror"
                   placeholder="correo@ejemplo.com" required/>
            @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>

        </div>
      </div>

      {{-- ── Aviso contraseña temporal ── --}}
      <div class="flex items-start gap-3 rounded-xl px-4 py-3 text-sm"
           style="background:rgba(247,107,28,.06);border:1px solid rgba(247,107,28,.25);color:#c2410c">
        <svg class="mt-0.5 shrink-0" width="15" height="15" fill="none" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
          <path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <span>
          El sistema generará una <strong>contraseña temporal</strong> automáticamente.
          Se mostrará al crear el usuario para que puedas comunicársela.
          El usuario deberá cambiarla en su primer inicio de sesión.
        </span>
      </div>

      {{-- ── Divider ── --}}
      <div class="border-t border-gray-100"></div>

      {{-- ── Sección: Perfil del usuario ── --}}
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">
          Perfil del usuario
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Rol *</label>
            <select name="role_id" class="field @error('role_id') is-invalid @enderror" required>
              <option value="">Seleccionar rol...</option>
              @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                  {{ $role->nombre }}
                </option>
              @endforeach
            </select>
            @error('role_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Cédula / Identificación</label>
            <input type="text" name="identificacion" value="{{ old('identificacion') }}"
                   class="field @error('identificacion') is-invalid @enderror"
                   placeholder="Número de documento" maxlength="20"/>
            @error('identificacion')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Teléfono</label>
            <input type="text" name="telefono" value="{{ old('telefono') }}"
                   class="field @error('telefono') is-invalid @enderror"
                   placeholder="Número de contacto" maxlength="20"/>
            @error('telefono')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Dependencia / Facultad</label>
            <input type="text" name="dependencia" value="{{ old('dependencia') }}"
                   class="field @error('dependencia') is-invalid @enderror"
                   placeholder="Ej: Facultad de Ingeniería"/>
            @error('dependencia')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>

        </div>
      </div>

      {{-- ── Botones ── --}}
      <div class="flex items-center gap-3 pt-1 border-t border-gray-100">
        <a href="{{ route('admin.usuarios.index') }}"
           class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition-colors text-center">
          Cancelar
        </a>
        <button type="submit"
                class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm transition-all hover:brightness-110 flex items-center justify-center gap-2"
                style="background:var(--blue)">
          <svg width="15" height="15" fill="none" viewBox="0 0 24 24">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
            <path d="M19 8v6M22 11h-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          Crear usuario
        </button>
      </div>

    </form>
  </div>

</div>
@endsection
