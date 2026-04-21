<p class="nav-section">Principal</p>
<a href="{{ route('tecnico.dashboard') }}"
   class="nav-item {{ request()->routeIs('tecnico.dashboard') ? 'active' : '' }}"
   style="{{ request()->routeIs('tecnico.dashboard') ? 'background:rgba(224,23,108,.2);color:#f472b6;' : '' }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
    <rect x="3" y="3" width="7" height="9" rx="1" stroke="currentColor" stroke-width="2"/>
    <rect x="14" y="3" width="7" height="5" rx="1" stroke="currentColor" stroke-width="2"/>
    <rect x="14" y="12" width="7" height="9" rx="1" stroke="currentColor" stroke-width="2"/>
    <rect x="3" y="16" width="7" height="5" rx="1" stroke="currentColor" stroke-width="2"/>
  </svg>
  Dashboard
</a>

<p class="nav-section mt-2">Gestión</p>

<a href="{{ route('tecnico.asignaciones') }}"
   class="nav-item {{ request()->routeIs('tecnico.asignaciones') ? 'active' : '' }}"
   style="{{ request()->routeIs('tecnico.asignaciones') ? 'background:rgba(224,23,108,.2);color:#f472b6;' : '' }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
             M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
          stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    <path d="M9 12h6M9 16h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
  </svg>
  Asignaciones
  @php
    $pendHoy = \App\Models\PrestamoAula::whereIn('estado',['aprobado','activo'])
               ->whereDate('fecha_prestamo', today())->count();
  @endphp
  @if($pendHoy > 0)
    <span class="ml-auto text-xs font-bold px-1.5 py-0.5 rounded-full"
          style="background:rgba(224,23,108,.8);color:#fff;font-size:.6rem;">{{ $pendHoy }}</span>
  @endif
</a>

<a href="{{ route('tecnico.inventario') }}"
   class="nav-item {{ request()->routeIs('tecnico.inventario') ? 'active' : '' }}"
   style="{{ request()->routeIs('tecnico.inventario') ? 'background:rgba(224,23,108,.2);color:#f472b6;' : '' }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
    <rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
    <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" stroke="currentColor" stroke-width="2"/>
    <path d="M12 12v4M10 14h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
  </svg>
  Inventario
</a>
