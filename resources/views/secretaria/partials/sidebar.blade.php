{{-- Macro de sidebar para secretaría — úsalo en cada vista --}}
{{-- resources/views/secretaria/partials/sidebar.blade.php --}}

<p class="nav-section">Principal</p>
<a href="{{ route('secretaria.dashboard') }}"
   class="nav-item {{ request()->routeIs('secretaria.dashboard') ? 'active' : '' }}"
   style="{{ request()->routeIs('secretaria.dashboard') ? 'background:rgba(0,182,122,.2);color:#4ade9a;' : '' }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
    <rect x="3" y="3" width="7" height="9" rx="1" stroke="currentColor" stroke-width="2"/>
    <rect x="14" y="3" width="7" height="5" rx="1" stroke="currentColor" stroke-width="2"/>
    <rect x="14" y="12" width="7" height="9" rx="1" stroke="currentColor" stroke-width="2"/>
    <rect x="3" y="16" width="7" height="5" rx="1" stroke="currentColor" stroke-width="2"/>
  </svg>
  Dashboard
</a>

<p class="nav-section mt-2">Gestión</p>

<a href="{{ route('secretaria.prestamos') }}"
   class="nav-item {{ request()->routeIs('secretaria.prestamos') ? 'active' : '' }}"
   style="{{ request()->routeIs('secretaria.prestamos') ? 'background:rgba(0,182,122,.2);color:#4ade9a;' : '' }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
             M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
          stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    <path d="M9 12h6M9 16h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
  </svg>
  Préstamos
  @php $pend = \App\Models\PrestamoAula::where('estado','pendiente')->count(); @endphp
  @if($pend > 0)
    <span class="ml-auto text-xs font-bold px-1.5 py-0.5 rounded-full"
          style="background:var(--magenta);color:#fff;font-size:.6rem;">{{ $pend }}</span>
  @endif
</a>

<a href="{{ route('secretaria.aulas') }}"
   class="nav-item {{ request()->routeIs('secretaria.aulas') ? 'active' : '' }}"
   style="{{ request()->routeIs('secretaria.aulas') ? 'background:rgba(0,182,122,.2);color:#4ade9a;' : '' }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
    <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
    <path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="2"/>
  </svg>
  Aulas
</a>

<a href="{{ route('secretaria.equipos.index') }}"
   class="nav-item {{ request()->routeIs('secretaria.equipos.*') ? 'active' : '' }}"
   style="{{ request()->routeIs('secretaria.equipos.*') ? 'background:rgba(0,182,122,.2);color:#4ade9a;' : '' }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
    <rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
    <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" stroke="currentColor" stroke-width="2"/>
    <path d="M12 12v4M10 14h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
  </svg>
  Equipos
  @php $pendEq = \App\Models\PrestamoEquipo::where('estado','pendiente')->count(); @endphp
  @if($pendEq > 0)
    <span class="ml-auto text-xs font-bold px-1.5 py-0.5 rounded-full"
          style="background:var(--magenta);color:#fff;font-size:.6rem;">{{ $pendEq }}</span>
  @endif
</a>

<p class="nav-section mt-2">Historial</p>

<a href="{{ route('secretaria.historial') }}"
   class="nav-item {{ request()->routeIs('secretaria.historial') ? 'active' : '' }}"
   style="{{ request()->routeIs('secretaria.historial') ? 'background:rgba(0,182,122,.2);color:#4ade9a;' : '' }}">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
    <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
  </svg>
  Historial
</a>