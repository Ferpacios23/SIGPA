{{-- resources/views/auth/reset-password.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SIGPA – Nueva contraseña</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}"/>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
</head>
<body class="body-centered">
<div class="bg-mesh"></div>
<div class="grid-overlay"></div>

<div class="auth-card">

  <div class="flex items-center gap-3 mb-8">
    <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:var(--green)">
      <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </div>
    <span style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.05rem;color:#0d1117;">SIGPA</span>
  </div>

  <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-5" style="background:rgba(0,182,122,.08)">
    <svg width="28" height="28" fill="none" viewBox="0 0 24 24">
      <rect x="3" y="11" width="18" height="11" rx="2" stroke="var(--green)" stroke-width="1.8"/>
      <path d="M7 11V7a5 5 0 0110 0v4" stroke="var(--green)" stroke-width="1.8" stroke-linecap="round"/>
      <circle cx="12" cy="16" r="1.5" fill="var(--green)"/>
    </svg>
  </div>

  <h2 style="font-size:1.45rem;font-weight:800;color:#0d1117;letter-spacing:-.02em;">Nueva contraseña</h2>
  <p class="text-gray-500 text-sm mt-2 mb-7 leading-relaxed">
    Crea una contraseña segura de al menos 8 caracteres.
  </p>

  <form method="POST" action="{{ route('password.update') }}">
    @csrf

    {{-- Token oculto --}}
    <input type="hidden" name="token" value="{{ $token }}"/>

    {{-- Email --}}
    <div class="mb-5">
      <label class="block text-sm font-semibold text-gray-700 mb-2" for="email">Correo electrónico</label>
      <div class="relative">
        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" stroke-width="1.8"/><path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </div>
        <input type="email" id="email" name="email"
          value="{{ old('email', $email ?? '') }}"
          class="field field-pl @error('email') err @enderror"
          placeholder="usuario@institución.edu.co" required/>
      </div>
      @error('email')
        <p class="err-msg">
          <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          {{ $message }}
        </p>
      @enderror
    </div>

    {{-- Nueva contraseña --}}
    <div class="mb-2">
      <label class="block text-sm font-semibold text-gray-700 mb-2" for="password">Nueva contraseña</label>
      <div class="relative">
        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </div>
        <input type="password" id="password" name="password"
          class="field field-pl field-pr @error('password') err @enderror"
          placeholder="Mínimo 8 caracteres" required
          oninput="checkStrength(this.value)"/>
        <button type="button" class="eye-btn" onclick="togglePass('password','eye1')">
          <svg id="eye1" width="18" height="18" fill="none" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
        </button>
      </div>
      {{-- Barra de fortaleza --}}
      <div class="mt-2 h-1 rounded-full bg-gray-100 overflow-hidden">
        <div id="strengthBar" class="strength-bar h-full" style="width:0%;background:var(--magenta)"></div>
      </div>
      <p id="strengthLabel" class="text-xs mt-1 text-gray-400"></p>
      @error('password')
        <p class="err-msg">
          <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          {{ $message }}
        </p>
      @enderror
    </div>

    {{-- Confirmar contraseña --}}
    <div class="mb-7 mt-5">
      <label class="block text-sm font-semibold text-gray-700 mb-2" for="password_confirmation">Confirmar contraseña</label>
      <div class="relative">
        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </div>
        <input type="password" id="password_confirmation" name="password_confirmation"
          class="field field-pl field-pr"
          placeholder="Repite tu contraseña" required/>
        <button type="button" class="eye-btn" onclick="togglePass('password_confirmation','eye2')">
          <svg id="eye2" width="18" height="18" fill="none" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
        </button>
      </div>
    </div>

    <button type="submit" class="btn-auth" id="submitBtn">
      <span id="btnText">Restablecer contraseña</span>
      <span id="btnSpinner" class="hidden justify-center">
        <span class="spinner"></span>
      </span>
    </button>
  </form>

  <div class="mt-6 text-center">
    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">
      <svg width="15" height="15" fill="none" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      Volver al inicio de sesión
    </a>
  </div>
</div>

<script src="{{ asset('js/auth.js') }}"></script>
</body>
</html>
