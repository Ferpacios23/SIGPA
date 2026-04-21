{{-- resources/views/admin/configuracion.blade.php --}}
@extends('layouts.dashboard')
@section('title', 'Configuración')
@section('accent-color', 'var(--blue)')
@section('role-label', '⭐ Administrador')
@section('page-title', 'Configuración')
@section('page-subtitle', 'Ajustes generales del sistema')

@section('sidebar-nav')
  <p class="nav-section">Principal</p>
  <a href="{{ route('dashboard.admin') }}" class="nav-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1" stroke="currentColor" stroke-width="2"/><rect x="14" y="3" width="7" height="5" rx="1" stroke="currentColor" stroke-width="2"/><rect x="14" y="12" width="7" height="9" rx="1" stroke="currentColor" stroke-width="2"/><rect x="3" y="16" width="7" height="5" rx="1" stroke="currentColor" stroke-width="2"/></svg>Dashboard</a>
  <p class="nav-section mt-2">Gestión</p>
  <a href="{{ route('admin.usuarios.index') }}" class="nav-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/></svg>Usuarios</a>
  <a href="{{ route('admin.roles.index') }}" class="nav-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>Roles</a>
  <a href="{{ route('admin.aulas.index') }}" class="nav-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="2"/></svg>Aulas</a>
  <a href="{{ route('admin.equipos.index') }}" class="nav-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 19v2M16 19v2M5 19h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>Equipos</a>
  <p class="nav-section mt-2">Análisis</p>
  <a href="{{ route('admin.reportes.index') }}" class="nav-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M9 17H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2h-2M9 17v4m6-4v4M9 21h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>Reportes</a>
  <a href="{{ route('admin.configuracion') }}" class="nav-item active" style="background:rgba(26,79,214,.2);color:#6ea0ff;"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/><path d="M12 1v2M12 21v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M1 12h2M21 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>Configuración</a>
@endsection

@section('content')
<div class="max-w-2xl space-y-5">

  {{-- Info del sistema --}}
  <div class="bg-white rounded-2xl shadow-sm p-6">
    <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1rem;margin-bottom:16px;">Información del sistema</h3>
    <div class="space-y-3 text-sm">
      @foreach([
        ['Sistema',      'SIGPA v2.0'],
        ['Laravel',      app()->version()],
        ['PHP',          phpversion()],
        ['Entorno',      app()->environment()],
        ['Base de datos','MySQL / MariaDB'],
        ['Zona horaria', config('app.timezone')],
      ] as [$k,$v])
      <div class="flex items-center justify-between py-2 border-b border-gray-50">
        <span class="text-gray-500 font-medium">{{ $k }}</span>
        <span class="text-gray-800 font-semibold font-mono text-xs bg-gray-100 px-2 py-1 rounded-lg">{{ $v }}</span>
      </div>
      @endforeach
    </div>
  </div>

  {{-- Tolerancia por defecto --}}
  <div class="bg-white rounded-2xl shadow-sm p-6">
    <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1rem;margin-bottom:4px;">Configuración de préstamos</h3>
    <p class="text-gray-400 text-xs mb-5">Ajusta los parámetros globales de préstamos</p>
    <div class="space-y-4">
      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tolerancia por defecto (minutos)</label>
        <div class="flex gap-3">
          <input type="number" value="15" min="5" max="60" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm w-28 outline-none focus:border-blue-400"/>
          <button class="px-4 py-2.5 rounded-xl text-white text-sm font-semibold" style="background:var(--blue)">Guardar</button>
        </div>
        <p class="text-gray-400 text-xs mt-1.5">Minutos de gracia antes de liberar un aula automáticamente si no se confirma asistencia.</p>
      </div>
    </div>
  </div>

  {{-- Cuenta del admin --}}
  <div class="bg-white rounded-2xl shadow-sm p-6">
    <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1rem;margin-bottom:16px;">Mi cuenta</h3>
    <div class="flex items-center gap-4 mb-5">
      <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg text-white"
           style="background:var(--blue)">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
      </div>
      <div>
        <p class="font-bold text-gray-800">{{ auth()->user()->name }}</p>
        <p class="text-gray-400 text-sm">{{ auth()->user()->email }}</p>
      </div>
    </div>
    <a href="{{ route('admin.usuarios.index') }}"
       class="inline-flex items-center gap-2 text-sm font-semibold px-4 py-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors">
      <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      Editar perfil
    </a>
  </div>

  {{-- Zona de peligro --}}
  <div class="bg-white rounded-2xl shadow-sm p-6 border border-red-100">
    <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1rem;color:#ef4444;margin-bottom:4px;">Zona de peligro</h3>
    <p class="text-gray-400 text-xs mb-4">Estas acciones son irreversibles. Procede con cuidado.</p>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="inline-flex items-center gap-2 text-sm font-semibold px-4 py-2.5 rounded-xl border border-red-200 text-red-600 hover:bg-red-50 transition-colors">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Cerrar sesión
      </button>
    </form>
  </div>

</div>
@endsection