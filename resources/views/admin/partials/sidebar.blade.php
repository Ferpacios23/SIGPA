{{-- resources/views/admin/partials/sidebar.blade.php --}}
@php
  $on  = fn($p) => request()->routeIs($p) ? 'background:rgba(26,79,214,.2);color:#6ea0ff;' : '';
  $cls = fn($p) => 'nav-item ' . (request()->routeIs($p) ? 'active' : '');
@endphp

<p class="nav-section">Principal</p>

<a href="{{ route('dashboard.admin') }}"
   class="{{ $cls('dashboard.admin') }}"
   style="{{ $on('dashboard.admin') }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1" stroke="currentColor" stroke-width="2"/><rect x="14" y="3" width="7" height="5" rx="1" stroke="currentColor" stroke-width="2"/><rect x="14" y="12" width="7" height="9" rx="1" stroke="currentColor" stroke-width="2"/><rect x="3" y="16" width="7" height="5" rx="1" stroke="currentColor" stroke-width="2"/></svg>
  Dashboard
</a>

<p class="nav-section mt-2">Gestión</p>

<a href="{{ route('admin.usuarios.index') }}"
   class="{{ $cls('admin.usuarios.*') }}"
   style="{{ $on('admin.usuarios.*') }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
  Usuarios
</a>

<a href="{{ route('admin.roles.index') }}"
   class="{{ $cls('admin.roles.*') }}"
   style="{{ $on('admin.roles.*') }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
  Roles
</a>

<a href="{{ route('admin.aulas.index') }}"
   class="{{ $cls('admin.aulas.*') }}"
   style="{{ $on('admin.aulas.*') }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="2"/></svg>
  Aulas
</a>

<a href="{{ route('admin.horarios.index') }}"
   class="{{ $cls('admin.horarios.*') }}"
   style="{{ $on('admin.horarios.*') }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
  Horarios
</a>

<a href="{{ route('admin.equipos.index') }}"
   class="{{ $cls('admin.equipos.*') }}"
   style="{{ $on('admin.equipos.*') }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 19v2M16 19v2M5 19h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
  Equipos
</a>

<p class="nav-section mt-2">Análisis</p>

<a href="{{ route('admin.reportes.index') }}"
   class="{{ $cls('admin.reportes.*') }}"
   style="{{ $on('admin.reportes.*') }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M9 17H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2h-2M9 17v4m6-4v4M9 21h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
  Reportes
</a>

<p class="nav-section mt-2">Historial</p>

<a href="{{ route('admin.historial.prestamos') }}"
   class="{{ $cls('admin.historial.prestamos') }}"
   style="{{ $on('admin.historial.prestamos') }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><polyline points="14 2 14 8 20 8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><line x1="16" y1="13" x2="8" y2="13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><line x1="16" y1="17" x2="8" y2="17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><polyline points="10 9 9 9 8 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
  Préstamos
</a>

<a href="{{ route('admin.historial.accesos') }}"
   class="{{ $cls('admin.historial.accesos') }}"
   style="{{ $on('admin.historial.accesos') }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
  Accesos
</a>

<a href="{{ route('admin.historial.cancelaciones') }}"
   class="{{ $cls('admin.historial.cancelaciones') }}"
   style="{{ $on('admin.historial.cancelaciones') }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><line x1="15" y1="9" x2="9" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><line x1="9" y1="9" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
  Inasistencias
</a>

<a href="{{ route('admin.historial.actividad-ti') }}"
   class="{{ $cls('admin.historial.actividad-ti') }}"
   style="{{ $on('admin.historial.actividad-ti') }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 19v2M16 19v2M5 19h14M9 9l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
  Actividad TI
</a>

<p class="nav-section mt-2">Sistema</p>

<a href="{{ route('admin.configuracion') }}"
   class="{{ $cls('admin.configuracion') }}"
   style="{{ $on('admin.configuracion') }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/><path d="M19.07 4.93l-1.41 1.41M1 12h2M21 12h2M4.93 19.07l1.41-1.41M4.93 4.93l1.41 1.41M19.07 19.07l-1.41-1.41M12 1v2M12 21v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
  Configuración
</a>
