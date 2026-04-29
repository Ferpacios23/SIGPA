{{-- resources/views/docente/partials/sidebar.blade.php --}}

<p class="nav-section">Principal</p>
<a href="{{ route('docente.dashboard') }}"
   class="nav-item {{ request()->routeIs('docente.dashboard') ? 'active' : '' }}"
   style="{{ request()->routeIs('docente.dashboard') ? 'background:rgba(247,107,28,.2);color:#fb923c;' : '' }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
    <rect x="3" y="3" width="7" height="9" rx="1" stroke="currentColor" stroke-width="2"/>
    <rect x="14" y="3" width="7" height="5" rx="1" stroke="currentColor" stroke-width="2"/>
    <rect x="14" y="12" width="7" height="9" rx="1" stroke="currentColor" stroke-width="2"/>
    <rect x="3" y="16" width="7" height="5" rx="1" stroke="currentColor" stroke-width="2"/>
  </svg>
  Dashboard
</a>

<p class="nav-section mt-2">Mis Solicitudes</p>

<a href="{{ route('docente.solicitudes.index') }}"
   class="nav-item {{ request()->routeIs('docente.solicitudes.*') ? 'active' : '' }}"
   style="{{ request()->routeIs('docente.solicitudes.*') ? 'background:rgba(247,107,28,.2);color:#fb923c;' : '' }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
             M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
          stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    <path d="M9 12h6M9 16h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
  </svg>
  Solicitudes de Salón
  @php
    $misPendientes = \App\Models\PrestamoAula::where('user_id', auth()->id())->where('estado', 'pendiente')->count();
  @endphp
  @if($misPendientes > 0)
    <span class="ml-auto text-xs font-bold px-1.5 py-0.5 rounded-full"
          style="background:var(--orange);color:#fff;font-size:.6rem;">{{ $misPendientes }}</span>
  @endif
</a>
