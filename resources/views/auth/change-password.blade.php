{{-- resources/views/auth/change-password.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SIGPA – Cambiar Contraseña</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  @vite('resources/css/app.css')
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
      Primera sesión
    </div>
    <h1 class="left-title">
      Protege tu<br>cuenta con una<br>
      <span class="text-gradient">contraseña segura</span>
    </h1>
    <p class="left-subtitle">
      Este paso es obligatorio. Una vez que establezcas tu contraseña personal, tendrás acceso completo al sistema.
    </p>

    {{-- Requisitos de contraseña --}}
    <div class="mt-8">
      <p class="text-gray-600 text-xs font-semibold uppercase tracking-widest mb-3">Requisitos mínimos</p>
      <div class="grid grid-cols-2 gap-2">
        <div class="info-card" style="animation-delay:.1s">
          <span class="role-badge info-card-name" style="background:rgba(26,79,214,.2);color:#6ea0ff;">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/></svg>
            8+ caracteres
          </span>
          <p class="text-gray-500 text-xs mt-1.5">Longitud mínima</p>
        </div>
        <div class="info-card" style="animation-delay:.2s">
          <span class="role-badge info-card-name" style="background:rgba(0,182,122,.15);color:#4ade9a;">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/></svg>
            Aa Mayúscula
          </span>
          <p class="text-gray-500 text-xs mt-1.5">Al menos una mayúscula y minúscula</p>
        </div>
        <div class="info-card" style="animation-delay:.3s">
          <span class="role-badge info-card-name" style="background:rgba(224,23,108,.15);color:#f472b6;">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/></svg>
            123 Número
          </span>
          <p class="text-gray-500 text-xs mt-1.5">Al menos un número</p>
        </div>
        <div class="info-card" style="animation-delay:.4s">
          <span class="role-badge info-card-name" style="background:rgba(247,107,28,.15);color:#fb923c;">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M12 1a3 3 0 0 0-3 3v1H5a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-4V4a3 3 0 0 0-3-3z" fill="currentColor" opacity=".9"/></svg>
            Personal
          </span>
          <p class="text-gray-500 text-xs mt-1.5">Distinta a la temporal</p>
        </div>
      </div>
    </div>
  </div>

  {{-- Bottom stats --}}
  <div class="left-stats">
    <div>
      <p class="stat-value stat-value--white">8+</p>
      <p class="stat-label">Caracteres mín.</p>
    </div>
    <div>
      <p class="stat-value stat-value--green">1×</p>
      <p class="stat-label">Solo una vez</p>
    </div>
    <div>
      <p class="stat-value stat-value--blue">∞</p>
      <p class="stat-label">Tu elección</p>
    </div>
  </div>
</div>

{{-- ─── RIGHT PANEL ─── --}}
<div class="right-panel">
  <div class="login-card">

    {{-- Mobile logo --}}
    <div class="lg:hidden logo-wrap mb-8">
      <div class="logo-icon w-9 h-9">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24">
          <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"
                stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <span class="logo-name logo-name--dark">SIGPA</span>
    </div>

    <div class="login-header">
      <h2 class="login-title">Establece tu contraseña</h2>
      <p class="login-subtitle">Es tu primer acceso. Crea una contraseña personal para continuar.</p>
    </div>

    {{-- Aviso primer login --}}
    <div class="mb-5 flex items-start gap-3 rounded-xl px-4 py-3 text-sm"
         style="background:rgba(247,107,28,.06);border:1px solid rgba(247,107,28,.28);color:#c2410c">
      <svg class="flex-shrink-0 mt-0.5" width="15" height="15" fill="none" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
        <path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
      <p class="text-sm" style="color:#c2410c">
        Ingresaste con una <strong>contraseña temporal</strong>. Debes establecer una nueva antes de acceder al sistema.
      </p>
    </div>

    {{-- Errores de validación --}}
    @if($errors->any())
      <div class="card-alert flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
        <svg class="flex-shrink-0 mt-0.5" width="16" height="16" fill="none" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10" stroke="#E0176C" stroke-width="2"/>
          <path d="M12 8v4M12 16h.01" stroke="#E0176C" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <div>
          @foreach($errors->all() as $error)
            <p class="text-red-600 text-sm">{{ $error }}</p>
          @endforeach
        </div>
      </div>
    @endif

    <form method="POST" action="{{ route('password.change.update') }}" id="loginForm">
      @csrf

      {{-- Nueva contraseña --}}
      <div class="form-group">
        <label class="form-label" for="password">Nueva contraseña</label>
        <div class="relative">
          <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
              <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="1.8"/>
              <path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
          </div>
          <input type="password" id="password" name="password"
                 autocomplete="new-password" placeholder="Mínimo 8 caracteres"
                 class="field field-pl field-pr @error('password') err @enderror"
                 oninput="checkStrength(this.value)" required/>
          <button type="button" class="eye-btn" onclick="togglePass('password','eyeIcon1')" title="Ver contraseña">
            <svg id="eyeIcon1" width="18" height="18" fill="none" viewBox="0 0 24 24">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.8"/>
              <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>
            </svg>
          </button>
        </div>
        {{-- Barra de fortaleza --}}
        <div class="mt-2 flex items-center gap-2">
          <div class="flex-1 h-1 rounded-full bg-gray-100 overflow-hidden">
            <div id="strengthBar" class="strength-bar h-full w-0"></div>
          </div>
          <span id="strengthLabel" class="text-xs font-semibold w-14 text-right"></span>
        </div>
        @error('password')
          <p class="err-msg">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            {{ $message }}
          </p>
        @enderror
      </div>

      {{-- Confirmar contraseña --}}
      <div class="form-group">
        <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
        <div class="relative">
          <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
              <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="1.8"/>
              <path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
          </div>
          <input type="password" id="password_confirmation" name="password_confirmation"
                 autocomplete="new-password" placeholder="Repite tu contraseña"
                 class="field field-pl field-pr" required/>
          <button type="button" class="eye-btn" onclick="togglePass('password_confirmation','eyeIcon2')" title="Ver contraseña">
            <svg id="eyeIcon2" width="18" height="18" fill="none" viewBox="0 0 24 24">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.8"/>
              <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>
            </svg>
          </button>
        </div>
      </div>

      {{-- Submit --}}
      <button type="submit" class="btn-auth" id="submitBtn">
        <span id="btnText">Guardar y continuar</span>
        <span id="btnSpinner" class="hidden justify-center">
          <span class="spinner"></span>
        </span>
      </button>
    </form>

    {{-- Cerrar sesión --}}
    <div class="auth-footer" style="margin-top:1rem">
      <form method="POST" action="{{ route('logout') }}" class="inline">
        @csrf
        <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 transition-colors underline-offset-2 hover:underline">
          Cerrar sesión
        </button>
      </form>
    </div>

    <p class="auth-footer">© {{ date('Y') }} SIGPA · Sistema Integral de Gestión de Préstamos Académicos</p>
  </div>
</div>

<script src="{{ asset('js/auth.js') }}"></script>
</body>
</html>
