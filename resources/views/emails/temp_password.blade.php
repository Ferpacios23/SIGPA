<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tu cuenta en SIGPA</title>
  <style>
    body { margin:0; padding:0; background:#f3f4f6; font-family:'Segoe UI',Arial,sans-serif; }
    .wrap { max-width:560px; margin:40px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.07); }
    .header { background:#1A4FD6; padding:32px 40px; text-align:center; }
    .header-logo { display:inline-flex; align-items:center; gap:10px; }
    .header-icon { width:36px; height:36px; background:rgba(255,255,255,.15); border-radius:8px; display:inline-flex; align-items:center; justify-content:center; }
    .header-name { color:#fff; font-size:1.3rem; font-weight:800; letter-spacing:.5px; }
    .body { padding:36px 40px; }
    .greeting { font-size:1rem; color:#374151; margin:0 0 16px; }
    .greeting strong { color:#111827; }
    .info { font-size:.9rem; color:#6b7280; line-height:1.6; margin:0 0 24px; }
    .card { background:#f8faff; border:1.5px solid rgba(26,79,214,.15); border-radius:12px; padding:20px 24px; margin:0 0 24px; }
    .card-label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#9ca3af; margin:0 0 6px; }
    .card-value { font-size:1.1rem; font-family:'Courier New',monospace; font-weight:700; color:#1A4FD6; background:#fff; border:1px solid rgba(26,79,214,.2); border-radius:8px; padding:10px 16px; display:inline-block; letter-spacing:.08em; margin:0; }
    .warning { display:flex; gap:10px; background:rgba(247,107,28,.06); border:1px solid rgba(247,107,28,.28); border-radius:10px; padding:14px 16px; margin:0 0 24px; }
    .warning-icon { flex-shrink:0; margin-top:1px; color:#c2410c; }
    .warning-text { font-size:.82rem; color:#c2410c; line-height:1.5; margin:0; }
    .btn-wrap { text-align:center; margin:0 0 28px; }
    .btn { display:inline-block; background:#1A4FD6; color:#fff; text-decoration:none; font-weight:700; font-size:.9rem; padding:12px 32px; border-radius:10px; }
    .divider { border:none; border-top:1px solid #e5e7eb; margin:0 0 20px; }
    .footer { font-size:.75rem; color:#9ca3af; text-align:center; line-height:1.6; }
  </style>
</head>
<body>
<div class="wrap">

  {{-- Header --}}
  <div class="header">
    <div class="header-logo">
      <div class="header-icon">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24">
          <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"
                stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <span class="header-name">SIGPA</span>
    </div>
  </div>

  {{-- Body --}}
  <div class="body">

    <p class="greeting">Hola, <strong>{{ $user->name }}</strong></p>

    <p class="info">
      Se ha creado tu cuenta en <strong>SIGPA</strong> — Sistema Integral de Gestión de
      Préstamos Académicos. A continuación encontrarás tus credenciales de acceso inicial.
    </p>

    {{-- Credenciales --}}
    <div class="card">
      <p class="card-label">Correo electrónico</p>
      <p class="card-value">{{ $user->email }}</p>
    </div>

    <div class="card">
      <p class="card-label">Rol asignado</p>
      <p class="card-value">{{ $user->profile?->role?->nombre ?? 'Sin rol' }}</p>
    </div>

    <div class="card">
      <p class="card-label">Contraseña temporal</p>
      <p class="card-value">{{ $tempPassword }}</p>
    </div>

    {{-- Advertencia --}}
    <div class="warning">
      <svg class="warning-icon" width="16" height="16" fill="none" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
        <path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
      <p class="warning-text">
        Esta es una <strong>contraseña temporal</strong>. Al ingresar por primera vez
        el sistema te pedirá que la cambies por una contraseña personal y segura.
      </p>
    </div>
    

    <hr class="divider"/>

    <p class="footer">
      Si no esperabas este correo puedes ignorarlo.<br/>
      © {{ date('Y') }} SIGPA · Sistema Integral de Gestión de Préstamos Académicos
    </p>

  </div>
</div>
</body>
</html>
