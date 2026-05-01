{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SIGPA – Iniciar Sesión</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  @vite(['resources/css/app.css'])
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}"/>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
</head>
<body>

{{-- ─── LEFT PANEL ─── --}}
<div class="left-panel hidden lg:flex flex-col justify-between flex-1 p-12 relative z-0">
  <div class="grid-overlay"></div>

  {{-- Logo --}}
  <div class="logo-wrap relative z-10">
    <div class="logo-icon w-10 h-10">
      <svg width="22" height="22" fill="none" viewBox="0 0 24 24">
        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"
              stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
    <span class="logo-name logo-name--light">SIGPA</span>
  </div>

  {{-- Center content --}}
  <div class="left-content">
    <div class="pill mb-6">
      <span class="pill-dot"></span>
      Sistema activo
    </div>
    <h1 class="left-title">
      Gestión Integral<br>de Préstamos<br>
      <span class="text-gradient">Académicos</span>
    </h1>
    <p class="left-subtitle">
      Plataforma centralizada para la reserva de aulas y equipos tecnológicos de la institución.
    </p>

    {{-- Role cards --}}
    <div class="mt-8">
      <p class="text-gray-600 text-xs font-semibold uppercase tracking-widest mb-3">Roles del sistema</p>
      <div class="grid grid-cols-2 gap-2">
        <div class="info-card" style="animation-delay:.1s">
          <span class="role-badge info-card-name" style="background:rgba(26,79,214,.2);color:#6ea0ff;">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
            Admin
          </span>
          <p class="text-gray-500 text-xs mt-1.5">Control total del sistema</p>
        </div>
        <div class="info-card" style="animation-delay:.2s">
          <span class="role-badge info-card-name" style="background:rgba(0,182,122,.15);color:#4ade9a;">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="2"/></svg>
            Secretaría
          </span>
          <p class="text-gray-500 text-xs mt-1.5">Gestión de préstamos</p>
        </div>
        <div class="info-card" style="animation-delay:.3s">
          <span class="role-badge info-card-name" style="background:rgba(224,23,108,.15);color:#f472b6;">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 19v2M16 19v2M5 19h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Técnico TI
          </span>
          <p class="text-gray-500 text-xs mt-1.5">Gestión de equipos</p>
        </div>
        <div class="info-card" style="animation-delay:.4s">
          <span class="role-badge info-card-name" style="background:rgba(247,107,28,.15);color:#fb923c;">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M12 14c-4 0-6 2-6 3v1h12v-1c0-1-2-3-6-3z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
            Docente
          </span>
          <p class="text-gray-500 text-xs mt-1.5">Solicitud de salones</p>
        </div>
      </div>
    </div>
  </div>

  {{-- Bottom stats --}}
  <div class="left-stats">
    <div>
      <p class="stat-value stat-value--white">24/7</p>
      <p class="stat-label">Disponibilidad</p>
    </div>
    <div>
      <p class="stat-value stat-value--green">4</p>
      <p class="stat-label">Roles definidos</p>
    </div>
    <div>
      <p class="stat-value stat-value--blue">∞</p>
      <p class="stat-label">Recursos</p>
    </div>
  </div>
</div>

{{-- ─── RIGHT PANEL ─── --}}
<div class="right-panel">
  <div class="login-card">

    {{-- Mobile logo --}}
    <div class="lg:hidden logo-wrap card-mobile-logo">
      <div class="logo-icon w-9 h-9">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24">
          <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"
                stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <span class="logo-name logo-name--dark">SIGPA</span>
    </div>

    <div class="login-header">
      <h2 class="login-title">Bienvenido de nuevo</h2>
      <p class="login-subtitle">Ingresa tus credenciales para acceder al sistema</p>
    </div>

    @if(session('error'))
      <div class="card-alert flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
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
      <div class="form-group">
        <label class="form-label" for="email">Correo electrónico</label>
        <div class="relative">
          <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" stroke-width="1.8"/>
              <path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
          </div>
          <input type="email" id="email" name="email" value="{{ old('email') }}"
                 autocomplete="email" placeholder="usuario@institución.edu.co"
                 class="field field-pl @error('email') err @enderror" required/>
        </div>
        @error('email')
          <p class="err-msg">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            {{ $message }}
          </p>
        @enderror
      </div>

      {{-- Password --}}
      <div class="form-group">
        <div class="form-label-row">
          <label class="form-label" for="password">Contraseña</label>
          <a href="{{ route('password.request') }}" class="forgot-link">¿Olvidaste tu contraseña?</a>
        </div>
        <div class="relative">
          <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
              <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="1.8"/>
              <path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
          </div>
          <input type="password" id="password" name="password"
                 autocomplete="current-password" placeholder="••••••••"
                 class="field field-pl field-pr @error('password') err @enderror" required/>
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
      <div class="remember-row">
        <input type="checkbox" id="remember" name="remember"
               class="w-4 h-4 rounded border-gray-300 cursor-pointer"/>
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

    {{-- Role chips --}}
    <div class="roles-section">
      <p class="roles-section-label">Acceso según tu rol</p>
      <div class="roles-chips">
        <span class="role-chip" style="background:#eff6ff;color:#1A4FD6;border:1px solid #bfdbfe;">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
          Admin
        </span>
        <span class="role-chip" style="background:#f0fdf4;color:#00B67A;border:1px solid #bbf7d0;">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="2"/></svg>
          Secretaría
        </span>
        <span class="role-chip" style="background:#fff0f6;color:#E0176C;border:1px solid #fbcfe8;">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 19v2M16 19v2M5 19h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          Técnico TI
        </span>
        <span class="role-chip" style="background:#fff7ed;color:#F76B1C;border:1px solid #fed7aa;">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M12 13c-3 0-5 1.5-5 2.5V17h10v-1.5C17 14.5 15 13 12 13z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="8" r="2.5" stroke="currentColor" stroke-width="1.8"/></svg>
          Docente
        </span>
      </div>
    </div>

    <p class="auth-footer">© {{ date('Y') }} SIGPA · Sistema Integral de Gestión de Préstamos Académicos</p>
  </div>
</div>

<script src="{{ asset('js/auth.js') }}"></script>
</body>
</html>
