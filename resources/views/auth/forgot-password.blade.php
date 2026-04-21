{{-- resources/views/auth/forgot-password.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SIGPA – Recuperar contraseña</title>
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

  <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-5" style="background:rgba(26,79,214,.08)">
    <svg width="28" height="28" fill="none" viewBox="0 0 24 24">
      <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="var(--blue)" stroke-width="1.8"/>
      <path d="M22 6l-10 7L2 6" stroke="var(--blue)" stroke-width="1.8" stroke-linecap="round"/>
    </svg>
  </div>

  <h2 style="font-size:1.45rem;font-weight:800;color:#0d1117;letter-spacing:-.02em;">Recuperar contraseña</h2>
  <p class="text-gray-500 text-sm mt-2 mb-7 leading-relaxed">
    Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.
  </p>

  @if(session('status'))
    <div class="mb-6 flex items-start gap-3 rounded-xl px-4 py-3" style="background:rgba(0,182,122,.08);border:1px solid rgba(0,182,122,.3)">
      <svg class="flex-shrink-0 mt-0.5" width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="var(--green)" stroke-width="2"/><path d="M8 12l3 3 5-5" stroke="var(--green)" stroke-width="2" stroke-linecap="round"/></svg>
      <p class="text-sm font-medium" style="color:#059669">{{ session('status') }}</p>
    </div>
  @endif

  <form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="mb-6">
      <label class="block text-sm font-semibold text-gray-700 mb-2" for="email">Correo electrónico</label>
      <div class="relative">
        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" stroke-width="1.8"/><path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </div>
        <input type="email" id="email" name="email" value="{{ old('email') }}"
          class="field field-pl @error('email') err @enderror"
          placeholder="usuario@institución.edu.co" required autofocus/>
      </div>
      @error('email')
        <p class="err-msg">
          <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          {{ $message }}
        </p>
      @enderror
    </div>

    <button type="submit" class="btn-auth" id="submitBtn">
      <span id="btnText">Enviar enlace de recuperación</span>
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
