{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SIGPA – Iniciar Sesión</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}"/>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
</head>
<body>

{{-- ─── LEFT PANEL (hidden on mobile) ─── --}}
<div class="left-panel hidden lg:flex flex-col justify-between flex-1 p-12 relative z-0">
  <div class="grid-overlay"></div>

  {{-- Logo --}}
  <div class="relative z-10 flex items-center gap-3">
    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:var(--green)">
      <svg width="22" height="22" fill="none" viewBox="0 0 24 24">
        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"
              stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
    <span style="font-family:'Syne',sans-serif;font-weight:800;color:#fff;font-size:1.2rem;letter-spacing:-.01em;">SIGPA</span>
  </div>

  {{-- Center content --}}
  <div class="relative z-10 max-w-md">
    <div class="pill mb-6">
      <span style="width:7px;height:7px;background:var(--green);border-radius:50%;display:inline-block;"></span>
      Sistema activo
    </div>
    <h1 style="font-size:clamp(2rem,4vw,3rem);font-weight:800;color:#fff;line-height:1.1;letter-spacing:-.03em;">
      Gestión Integral<br>de Préstamos<br>
      <span style="background:linear-gradient(90deg,var(--green),#00d68f);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Académicos</span>
    </h1>
    <p class="text-gray-400 mt-4 leading-relaxed" style="font-size:.95rem;">
      Plataforma centralizada para la reserva de aulas y equipos tecnológicos de la institución.
    </p>

    {{-- Roles info --}}
    <div class="mt-10 space-y-3">
      <p class="text-gray-600 text-xs font-semibold uppercase tracking-widest mb-4">Roles del sistema</p>
      <div class="info-card" style="animation-delay:.1s">
        <div class="flex items-center gap-3">
          <span class="role-badge" style="background:rgba(26,79,214,.2);color:#6ea0ff;">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
            Administrador
          </span>
          <span class="text-gray-500 text-xs">Control total del sistema</span>
        </div>
      </div>
      <div class="info-card" style="animation-delay:.2s">
        <div class="flex items-center gap-3">
          <span class="role-badge" style="background:rgba(0,182,122,.15);color:#4ade9a;">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="2"/></svg>
            Secretaría
          </span>
          <span class="text-gray-500 text-xs">Gestión de préstamos de aulas</span>
        </div>
      </div>
      <div class="info-card" style="animation-delay:.3s">
        <div class="flex items-center gap-3">
          <span class="role-badge" style="background:rgba(224,23,108,.15);color:#f472b6;">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 19v2M16 19v2M5 19h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Técnico TI
          </span>
          <span class="text-gray-500 text-xs">Gestión de equipos tecnológicos</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Bottom stat --}}
  <div class="relative z-10 flex gap-8">
    <div>
      <p style="font-family:'Syne',sans-serif;font-size:1.6rem;font-weight:800;color:#fff;">24/7</p>
      <p class="text-gray-600 text-xs mt-0.5">Disponibilidad</p>
    </div>
    <div>
      <p style="font-family:'Syne',sans-serif;font-size:1.6rem;font-weight:800;color:var(--green);">4</p>
      <p class="text-gray-600 text-xs mt-0.5">Roles definidos</p>
    </div>
    <div>
      <p style="font-family:'Syne',sans-serif;font-size:1.6rem;font-weight:800;color:var(--blue);">∞</p>
      <p class="text-gray-600 text-xs mt-0.5">Recursos</p>
    </div>
  </div>
</div>

{{-- ─── RIGHT PANEL (Login form) ─── --}}
<div class="flex items-center justify-center w-full lg:w-auto lg:min-w-[520px] px-4 py-12" style="background:#f4f6fb">
  <div class="login-card">

    {{-- Mobile logo --}}
    <div class="lg:hidden flex items-center gap-3 mb-8">
      <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:var(--green)">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24">
          <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"
                stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <span style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;color:#0d1117;">SIGPA</span>
    </div>

    <div class="mb-8">
      <h2 style="font-size:1.6rem;font-weight:800;color:#0d1117;letter-spacing:-.02em;">Bienvenido de nuevo</h2>
      <p class="text-gray-500 text-sm mt-1">Ingresa tus credenciales para acceder al sistema</p>
    </div>

    {{-- Session error --}}
    @if(session('error'))
      <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
        <svg class="flex-shrink-0 mt-0.5" width="16" height="16" fill="none" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10" stroke="#E0176C" stroke-width="2"/>
          <path d="M12 8v4M12 16h.01" stroke="#E0176C" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <p class="text-red-600 text-sm">{{ session('error') }}</p>
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm">
      @csrf

      {{-- Email --}}
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-2" for="email">
          Correo electrónico
        </label>
        <div class="relative">
          <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" stroke-width="1.8"/>
              <path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
          </div>
          <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            autocomplete="email"
            class="field field-pl @error('email') err @enderror"
            placeholder="usuario@institución.edu.co"
            required
          />
        </div>
        @error('email')
          <p class="err-msg">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            {{ $message }}
          </p>
        @enderror
      </div>

      {{-- Password --}}
      <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
          <label class="text-sm font-semibold text-gray-700" for="password">Contraseña</label>
          <a href="{{ route('password.request') }}" class="text-xs font-medium hover:underline" style="color:var(--blue)">
          {{--<a href="{{ route('password.request') }}" class="text-xs font-medium hover:underline" style="color:var(--blue)">--}}
            ¿Olvidaste tu contraseña?
          </a>
        </div>
        <div class="relative">
          <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
              <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="1.8"/>
              <path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
          </div>
          <input
            type="password"
            id="password"
            name="password"
            autocomplete="current-password"
            class="field field-pl field-pr @error('password') err @enderror"
            placeholder="••••••••"
            required
          />
          <button type="button" class="eye-btn" onclick="togglePass('password','eyeIcon')" title="Ver contraseña">
            <svg id="eyeIcon" width="18" height="18" fill="none" viewBox="0 0 24 24">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.8"/>
              <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>
            </svg>
          </button>
        </div>
        @error('password')
          <p class="err-msg">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            {{ $message }}
          </p>
        @enderror
      </div>

      {{-- Remember me --}}
      <div class="flex items-center gap-2 mb-7">
        <input
          type="checkbox"
          id="remember"
          name="remember"
          class="w-4 h-4 rounded border-gray-300 cursor-pointer"
          style="accent-color:var(--green)"
        />
        <label for="remember" class="text-sm text-gray-600 cursor-pointer select-none">
          Mantener sesión iniciada
        </label>
      </div>

      {{-- Submit --}}
      <button type="submit" class="btn-auth" id="submitBtn">
        <span id="btnText">Ingresar al sistema</span>
        <span id="btnSpinner" class="hidden justify-center">
          <span class="spinner"></span>
        </span>
      </button>
    </form>

    {{-- Divider roles --}}
    <div class="mt-8 pt-6 border-t border-gray-100">
      <p class="text-center text-xs text-gray-400 mb-4 font-medium">Acceso según tu rol</p>
      <div class="flex flex-wrap justify-center gap-2">
        <span class="role-badge" style="background:#eff6ff;color:#1A4FD6;border:1px solid #bfdbfe;">
          <svg width="11" height="11" fill="none" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
          Admin
        </span>
        <span class="role-badge" style="background:#f0fdf4;color:#00B67A;border:1px solid #bbf7d0;">
          <svg width="11" height="11" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="2"/></svg>
          Secretaría
        </span>
        <span class="role-badge" style="background:#fff0f6;color:#E0176C;border:1px solid #fbcfe8;">
          <svg width="11" height="11" fill="none" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/></svg>
          Técnico TI
        </span>
      </div>
    </div>

    <p class="text-center text-xs text-gray-400 mt-6">
      © {{ date('Y') }} SIGPA · Sistema Integral de Gestión de Préstamos Académicos
    </p>
  </div>
</div>

<script src="{{ asset('js/auth.js') }}"></script>
</body>
</html>