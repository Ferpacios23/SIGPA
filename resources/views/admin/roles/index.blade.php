@extends('layouts.dashboard')
@section('title', 'Roles')
@section('accent-color', 'var(--blue)')
@section('role-label', '⭐ Administrador')
@section('page-title', 'Roles del sistema')
@section('page-subtitle', 'Permisos y accesos por rol')
 
@section('sidebar-nav')
  @include('admin.partials.sidebar')
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
 