{{-- resources/views/layouts/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="es" x-data="dashboardApp()" x-init="init()">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <title>SIGPA – @yield('title', 'Dashboard')</title>
  @vite(['resources/css/app.css'])
  <link rel="stylesheet" href="{{ asset('css/sigpa.css') }}"/>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <script defer src="{{ asset('js/sigpa.js') }}"></script>
  <script defer src="{{ asset('js/alpine.min.js') }}"></script>
</head>
<body class="flex h-screen">

{{-- ════════════════════════════════
     SIDEBAR
════════════════════════════════ --}}
<aside id="sidebar" class="sidebar" :class="{'open': sidebarOpen}">

  {{-- Logo --}}
  <div class="flex items-center gap-3 px-4 h-16 border-b flex-shrink-0" style="border-color:rgba(255,255,255,.07)">
    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:var(--green)">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </div>
    <div>
      <p style="font-family:'Syne',sans-serif;font-weight:800;color:#fff;font-size:.95rem;line-height:1.1;">SIGPA</p>
      <p style="color:rgba(255,255,255,.3);font-size:.65rem;">v2.0</p>
    </div>
    <button @click="sidebarOpen=false" class="ml-auto lg:hidden text-gray-600 hover:text-white">
      <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    </button>
  </div>

  {{-- User badge --}}
  <div class="px-3 py-3 border-b flex-shrink-0" style="border-color:rgba(255,255,255,.05)">
    <div class="flex items-center gap-3 px-2 py-2 rounded-xl" style="background:rgba(255,255,255,.05)">
      <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0" style="background:@yield('accent-color','var(--green)');color:#fff;">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
      </div>
      <div class="min-w-0">
        <p class="text-white text-xs font-semibold truncate">{{ auth()->user()->name }}</p>
        <p class="text-xs font-medium" style="color:@yield('accent-color','var(--green)')">@yield('role-label','Usuario')</p>
      </div>
    </div>
  </div>

  {{-- Nav --}}
  <nav class="flex-1 px-2 py-3 space-y-0.5 overflow-y-auto">
    @yield('sidebar-nav')
  </nav>

  {{-- Logout --}}
  <div class="px-2 pb-3 pt-2 flex-shrink-0" style="border-top:1px solid rgba(255,255,255,.05)">
    <form method="POST" action="{{ route('logout') }}" id="logoutForm">
      @csrf
      <button type="submit" class="nav-item w-full" style="color:rgba(239,68,68,.8)">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Cerrar sesión
      </button>
    </form>
  </div>
</aside>

{{-- Mobile overlay --}}
<div x-show="sidebarOpen" @click="sidebarOpen=false" class="mobile-overlay lg:hidden" style="display:none"></div>

{{-- ════════════════════════════════
     MAIN AREA
════════════════════════════════ --}}
<div class="flex-1 flex flex-col overflow-hidden min-w-0">

  {{-- TOP BAR --}}
  <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 flex-shrink-0 z-30">
    <div class="flex items-center gap-3">
      <button @click="sidebarOpen=!sidebarOpen" class="text-gray-500 hover:text-gray-800 lg:hidden p-1">
        <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      </button>
      <div>
        <h1 style="font-size:1rem;font-weight:700;color:#0d1117;line-height:1.2;">@yield('page-title','Dashboard')</h1>
        <p class="text-gray-400 text-xs">@yield('page-subtitle','')</p>
      </div>
    </div>

    <div class="flex items-center gap-2">
      {{-- Search --}}
      <div class="hidden sm:flex items-center gap-2 bg-gray-100 rounded-xl px-3 py-2 text-sm text-gray-500">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        <span class="text-xs">Buscar...</span>
      </div>

      {{-- Notifications --}}
      <div class="relative">
        <button @click="notifOpen=!notifOpen;if(notifOpen)markSeen()"
          class="relative w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
          <svg width="17" height="17" fill="none" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
          <span x-show="unreadCount>0"
            class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full text-white flex items-center justify-center"
            style="background:var(--magenta);font-size:.6rem;font-weight:700"
            x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
        </button>

        {{-- Notification panel --}}
        <div x-show="notifOpen" x-transition @click.outside="notifOpen=false" class="notif-panel" style="display:none">
          <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <h4 style="font-family:'Syne',sans-serif;font-weight:700;font-size:.9rem;">Notificaciones</h4>
            <div class="flex items-center gap-2">
              <button @click="clearAll()" class="text-xs text-gray-400 hover:text-gray-700">Limpiar todo</button>
              <button @click="notifOpen=false" class="text-gray-400 hover:text-gray-700">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              </button>
            </div>
          </div>
          <div class="max-h-80 overflow-y-auto">
            <template x-if="notifications.length===0">
              <div class="py-10 text-center text-gray-400">
                <svg class="mx-auto mb-2 opacity-40" width="32" height="32" fill="none" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" stroke="currentColor" stroke-width="1.5"/></svg>
                <p class="text-sm">Sin notificaciones</p>
              </div>
            </template>
            <template x-for="n in notifications" :key="n.id">
              <div class="flex items-start gap-3 px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition-colors"
                   :class="{'bg-blue-50/40':!n.read}">
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5"
                     :style="`background:${n.color}20`">
                  <svg width="14" height="14" fill="none" viewBox="0 0 24 24" :style="`stroke:${n.color}`">
                    <path x-show="n.type==='prestamo'" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2" stroke-linecap="round"/>
                    <path x-show="n.type==='usuario'" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8z" stroke-width="2" stroke-linecap="round"/>
                    <path x-show="n.type==='equipo'" d="M2 5h20a2 2 0 012 2v10a2 2 0 01-2 2H2a2 2 0 01-2-2V7a2 2 0 012-2z" stroke-width="2"/>
                    <path x-show="n.type==='sistema'" d="M12 2l3 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77 6.18 21 7 14.14 2 9.27l6.91-1.01L12 2z" stroke-width="2" stroke-linejoin="round"/>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-800 leading-snug" x-text="n.title"></p>
                  <p class="text-xs text-gray-500 mt-0.5 leading-snug" x-text="n.body"></p>
                  <p class="text-xs text-gray-400 mt-1" x-text="n.time"></p>
                </div>
                <span x-show="!n.read" class="w-2 h-2 rounded-full flex-shrink-0 mt-1.5" style="background:var(--blue)"></span>
              </div>
            </template>
          </div>
          <div class="px-4 py-2.5 border-t border-gray-100">
            <a href="#" class="text-xs font-semibold" style="color:var(--blue)">Ver todas las notificaciones →</a>
          </div>
        </div>
      </div>

      {{-- Avatar --}}
      <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm text-white flex-shrink-0"
           style="background:@yield('accent-color','var(--green)')">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
      </div>
    </div>
  </header>

  {{-- PAGE CONTENT --}}
  <main class="flex-1 overflow-y-auto p-4 sm:p-6">

    {{-- Flash messages --}}
    @if(session('success'))
      <div class="mb-4 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium"
           style="background:rgba(0,182,122,.08);border:1px solid rgba(0,182,122,.25);color:#059669">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M8 12l3 3 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="mb-4 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium"
           style="background:rgba(224,23,108,.06);border:1px solid rgba(224,23,108,.2);color:#be185d">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        {{ session('error') }}
      </div>
    @endif

    @yield('content')
  </main>
</div>

{{-- Toast --}}
<div x-show="toast.show" x-transition class="toast" style="display:none">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="var(--green)" stroke-width="2"/><path d="M8 12l3 3 5-5" stroke="var(--green)" stroke-width="2" stroke-linecap="round"/></svg>
  <span x-text="toast.msg"></span>
</div>

@yield('scripts')
</body>
</html>