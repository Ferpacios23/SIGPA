@extends('layouts.dashboard')
@section('title', 'Roles')
@section('accent-color', 'var(--blue)')
@section('role-label', '⭐ Administrador')
@section('page-title', 'Roles del sistema')
@section('page-subtitle', 'Permisos y accesos por rol')
 
@section('sidebar-nav')
  <p class="nav-section">Principal</p>
  <a href="{{ route('dashboard.admin') }}" class="nav-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1" stroke="currentColor" stroke-width="2"/><rect x="14" y="3" width="7" height="5" rx="1" stroke="currentColor" stroke-width="2"/><rect x="14" y="12" width="7" height="9" rx="1" stroke="currentColor" stroke-width="2"/><rect x="3" y="16" width="7" height="5" rx="1" stroke="currentColor" stroke-width="2"/></svg>Dashboard</a>
  <p class="nav-section mt-2">Gestión</p>
  <a href="{{ route('admin.usuarios.index') }}" class="nav-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/></svg>Usuarios</a>
  <a href="{{ route('admin.roles.index') }}" class="nav-item active" style="background:rgba(26,79,214,.2);color:#6ea0ff;"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>Roles</a>
  <a href="{{ route('admin.aulas.index') }}" class="nav-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="2"/></svg>Aulas</a>
  <a href="{{ route('admin.equipos.index') }}" class="nav-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 19v2M16 19v2M5 19h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>Equipos</a>
  <p class="nav-section mt-2">Análisis</p>
  <a href="{{ route('admin.reportes.index') }}" class="nav-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M9 17H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2h-2M9 17v4m6-4v4M9 21h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>Reportes</a>
  <a href="{{ route('admin.configuracion') }}" class="nav-item"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/><path d="M12 1v2M12 21v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M1 12h2M21 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>Configuración</a>
@endsection
 
@section('content')
<div class="space-y-5">
  <h2 style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;">Roles definidos</h2>
 
  @php
    $roleConfig = [
      'admin'      => ['color'=>'var(--blue)',    'bg'=>'rgba(26,79,214,.08)',  'desc'=>'Acceso total al sistema, gestión de usuarios, aulas, equipos y reportes.'],
      'secretaria' => ['color'=>'var(--green)',   'bg'=>'rgba(0,182,122,.08)',  'desc'=>'Gestiona y aprueba solicitudes de préstamos de aulas.'],
      'tecnico'    => ['color'=>'var(--magenta)', 'bg'=>'rgba(224,23,108,.08)', 'desc'=>'Controla el inventario y préstamos de equipos tecnológicos.'],
    ];
  @endphp
 
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    @foreach($roles as $role)
      @php $rc = $roleConfig[$role->slug] ?? ['color'=>'#64748b','bg'=>'rgba(100,116,139,.08)','desc'=>'']; @endphp
      <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4" style="border-color:{{ $rc['color'] }}">
        <div class="flex items-start justify-between mb-4">
          <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background:{{ $rc['bg'] }}">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="{{ $rc['color'] }}" stroke-width="2" stroke-linejoin="round"/></svg>
          </div>
          <span class="text-2xl font-bold" style="font-family:'Syne',sans-serif;color:{{ $rc['color'] }}">
            {{ $role->profiles_count }}
            <span class="text-sm text-gray-400 font-normal">usuarios</span>
          </span>
        </div>
        <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;color:#0d1117">{{ $role->nombre }}</h3>
        <p class="text-gray-500 text-sm mt-1 leading-relaxed">{{ $rc['desc'] ?: $role->descripcion }}</p>
        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
          <span class="text-xs text-gray-400">Slug: <code class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">{{ $role->slug }}</code></span>
          <span class="inline-flex items-center gap-1.5 text-xs font-semibold" style="color:{{ $rc['color'] }}">
            <span class="w-2 h-2 rounded-full" style="background:{{ $rc['color'] }}"></span>
            {{ $role->activo ? 'Activo' : 'Inactivo' }}
          </span>
        </div>
      </div>
    @endforeach
  </div>
 
  <div class="bg-blue-50 border border-blue-200 rounded-2xl px-5 py-4 text-sm text-blue-700">
    <strong>Nota:</strong> Los roles son fijos del sistema. Para asignar un rol a un usuario, edítalo desde la sección
    <a href="{{ route('admin.usuarios.index') }}" class="font-semibold underline">Usuarios</a>.
  </div>
</div>
@endsection
 