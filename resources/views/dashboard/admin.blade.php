{{-- resources/views/dashboard/admin.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Panel Administrador')
@section('accent-color', 'var(--blue)')
@section('accent-var', '#1A4FD6')
@section('role-label', '⭐ Administrador')
@section('page-title', 'Panel de Administración')
@section('page-subtitle', 'Control total del sistema SIGPA')

@section('sidebar-nav')
  @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="space-y-5" x-data="adminDash()">

  {{-- ── WELCOME BANNER ── --}}
  <div class="rounded-2xl p-5 text-white relative overflow-hidden" style="background:linear-gradient(135deg,#1A4FD6 0%,#0f2ea8 100%)">
    <div class="absolute inset-0 opacity-10" style="background-image:linear-gradient(rgba(255,255,255,.15) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.15) 1px,transparent 1px);background-size:32px 32px"></div>
    <div class="relative flex items-center justify-between flex-wrap gap-4">
      <div>
        <p class="text-blue-300 text-xs font-semibold uppercase tracking-wider mb-1">Bienvenido de nuevo</p>
        <h2 style="font-size:1.4rem;font-weight:800;">{{ auth()->user()->name }}</h2>
        <p class="text-blue-300 text-sm mt-0.5">Tienes acceso completo al sistema SIGPA</p>
      </div>
      <div class="flex gap-5 sm:gap-8">
        <div class="text-center">
          <p style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;">{{ $totalUsers }}</p>
          <p class="text-blue-300 text-xs mt-0.5">Usuarios</p>
        </div>
        <div class="text-center">
          <p style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;">{{ $totalAulas }}</p>
          <p class="text-blue-300 text-xs mt-0.5">Aulas</p>
        </div>
        <div class="text-center">
          <p style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;">{{ $todayLoans }}</p>
          <p class="text-blue-300 text-xs mt-0.5">Préstamos hoy</p>
        </div>
        <div class="text-center">
          <p style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;">{{ $pendingLoans }}</p>
          <p class="text-blue-300 text-xs mt-0.5">Pendientes</p>
        </div>
      </div>
    </div>
  </div>

  {{-- ── STATS ── --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @php
      $stats = [
        ['label'=>'Usuarios activos',  'value'=>$totalUsers,     'sub'=>'registrados en el sistema', 'color'=>'var(--blue)',    'bg'=>'rgba(26,79,214,.08)',  'icon'=>'user'],
        ['label'=>'Aulas disponibles', 'value'=>$aulasLibres,    'sub'=>'de '.$totalAulas.' totales',  'color'=>'var(--green)',   'bg'=>'rgba(0,182,122,.08)',  'icon'=>'room'],
        ['label'=>'Equipos libres',    'value'=>$equiposLibres,  'sub'=>'de '.$totalEquipos.' totales','color'=>'var(--magenta)','bg'=>'rgba(224,23,108,.08)', 'icon'=>'eq'],
        ['label'=>'Préstamos hoy',     'value'=>$todayLoans,     'sub'=>$pendingLoans.' pendientes',  'color'=>'var(--orange)',  'bg'=>'rgba(247,107,28,.08)', 'icon'=>'loan'],
      ];
    @endphp
    @foreach($stats as $s)
    <div class="stat-card">
      <div class="flex items-start justify-between mb-3">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:{{ $s['bg'] }}">
          @if($s['icon']==='user')
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" stroke="{{ $s['color'] }}" stroke-width="2"/><path d="M4 20c0-3.314 3.582-6 8-6s8 2.686 8 6" stroke="{{ $s['color'] }}" stroke-width="2" stroke-linecap="round"/></svg>
          @elseif($s['icon']==='room')
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke="{{ $s['color'] }}" stroke-width="2"/><path d="M3 9h18M9 21V9" stroke="{{ $s['color'] }}" stroke-width="2"/></svg>
          @elseif($s['icon']==='eq')
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2" stroke="{{ $s['color'] }}" stroke-width="2"/><path d="M8 19v2M16 19v2M5 19h14" stroke="{{ $s['color'] }}" stroke-width="2" stroke-linecap="round"/></svg>
          @else
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" stroke="{{ $s['color'] }}" stroke-width="2" stroke-linecap="round"/></svg>
          @endif
        </div>
        <span class="text-xs font-semibold" style="color:{{ $s['color'] }}">↑</span>
      </div>
      <p style="font-family:'Syne',sans-serif;font-size:1.7rem;font-weight:800;color:{{ $s['color'] }}">{{ $s['value'] }}</p>
      <p class="text-gray-700 text-sm font-medium mt-0.5">{{ $s['label'] }}</p>
      <p class="text-gray-400 text-xs mt-0.5">{{ $s['sub'] }}</p>
    </div>
    @endforeach
  </div>

  {{-- ── AULAS GRID ── --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3">
      <div>
        <h3 style="font-weight:700;font-size:.95rem;">Estado de Aulas</h3>
        <p class="text-gray-400 text-xs mt-0.5">Vista en tiempo real</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap">
        <div class="flex items-center gap-4 text-xs text-gray-500 mr-2">
          <span class="flex items-center gap-1.5"><span class="dot dot-green"></span>Disponible</span>
          <span class="flex items-center gap-1.5"><span class="dot dot-red"></span>Ocupada</span>
          <span class="flex items-center gap-1.5"><span class="dot dot-yellow"></span>Mant.</span>
          <span class="flex items-center gap-1.5"><span class="dot dot-gray"></span>Inactiva</span>
        </div>
        <a href="{{ route('admin.aulas.index') }}" class="btn-primary text-xs" style="background:var(--green)">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg>
          Nueva aula
        </a>
      </div>
    </div>
    <div class="p-5">
      @if($aulas->isEmpty())
        <div class="text-center py-10 text-gray-400">
          <svg class="mx-auto mb-3 opacity-40" width="40" height="40" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="1.5"/></svg>
          <p class="text-sm font-medium">No hay aulas registradas</p>
          <a href="{{ route('admin.aulas.index') }}" class="text-xs mt-1 inline-block" style="color:var(--blue)">Agregar primera aula →</a>
        </div>
      @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
          @foreach($aulas as $aula)
            @php
              $stateConfig = [
                'disponible'        => ['bar'=>'var(--green)',   'dot'=>'dot-green',  'badge'=>'bg-green-100 text-green-700',  'label'=>'Libre'],
                'ocupada'           => ['bar'=>'var(--magenta)', 'dot'=>'dot-red',    'badge'=>'bg-red-100 text-red-600',      'label'=>'Ocupada'],
                'en_mantenimiento'  => ['bar'=>'var(--orange)',  'dot'=>'dot-yellow', 'badge'=>'bg-yellow-100 text-yellow-700','label'=>'Mant.'],
                'inactiva'          => ['bar'=>'#94a3b8',        'dot'=>'dot-gray',   'badge'=>'bg-gray-100 text-gray-500',    'label'=>'Inactiva'],
              ];
              $sc = $stateConfig[$aula->estado] ?? $stateConfig['inactiva'];
            @endphp
            <div class="aula-card" onclick="window.location='{{ route('admin.aulas.index') }}'">
              <div class="status-bar" style="background:{{ $sc['bar'] }}"></div>
              <div class="flex items-start justify-between mt-1">
                <div>
                  <p class="font-bold text-gray-800 text-sm leading-tight">{{ $aula->codigo }}</p>
                  @if($aula->nombre)
                    <p class="text-gray-400 text-xs mt-0.5 leading-tight truncate max-w-[80px]">{{ $aula->nombre }}</p>
                  @endif
                </div>
                <span class="dot {{ $sc['dot'] }} flex-shrink-0 mt-1"></span>
              </div>
              <div class="mt-3 flex items-center justify-between">
                <div class="flex items-center gap-1 text-gray-500 text-xs">
                  <svg width="11" height="11" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3" stroke="currentColor" stroke-width="2"/><path d="M5 20c0-3.314 3.134-6 7-6s7 2.686 7 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                  {{ $aula->capacidad }}
                </div>
                <span class="badge text-xs {{ $sc['badge'] }}">{{ $sc['label'] }}</span>
              </div>
              @if($aula->ubicacion)
                <p class="text-gray-400 text-xs mt-1.5 truncate">{{ $aula->ubicacion }}</p>
              @endif
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>

  {{-- ── ROW: Usuarios + Equipos ── --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- USUARIOS --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 style="font-weight:700;font-size:.95rem;">Usuarios recientes</h3>
        <a href="{{ route('admin.usuarios.index') }}" class="btn-primary text-xs" style="background:var(--blue)">
          <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg>
          Nuevo
        </a>
      </div>
      <div class="divide-y divide-gray-50">
        @forelse($recentUsers as $u)
          @php
            $rSlug  = $u->profile?->role?->slug ?? '';
            $rName  = $u->profile?->role?->nombre ?? '—';
            $rBadge = ['admin'=>'bg-blue-100 text-blue-700','secretaria'=>'bg-green-100 text-green-700','tecnico'=>'bg-pink-100 text-pink-700'];
          @endphp
          <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition-colors">
            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0"
                 style="background:{{ ['admin'=>'rgba(26,79,214,.15)','secretaria'=>'rgba(0,182,122,.15)','tecnico'=>'rgba(224,23,108,.15)'][$rSlug] ?? 'rgba(100,116,139,.15)' }};
                        color:{{ ['admin'=>'#1A4FD6','secretaria'=>'#00B67A','tecnico'=>'#E0176C'][$rSlug] ?? '#64748b' }}">
              {{ strtoupper(substr($u->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-gray-800 truncate">{{ $u->name }}</p>
              <p class="text-xs text-gray-400 truncate">{{ $u->email }}</p>
            </div>
            <span class="badge text-xs {{ $rBadge[$rSlug] ?? 'bg-gray-100 text-gray-600' }}">{{ $rName }}</span>
          </div>
        @empty
          <div class="px-5 py-8 text-center text-gray-400 text-sm">Sin usuarios registrados.</div>
        @endforelse
      </div>
      <div class="px-5 py-3 border-t border-gray-50">
        <a href="{{ route('admin.usuarios.index') }}" class="text-xs font-semibold" style="color:var(--blue)">Ver todos los usuarios →</a>
      </div>
    </div>

    {{-- EQUIPOS --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 style="font-weight:700;font-size:.95rem;">Equipos tecnológicos</h3>
        <a href="{{ route('admin.equipos.index') }}" class="btn-primary text-xs" style="background:var(--magenta)">
          <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg>
          Nuevo
        </a>
      </div>
      {{-- Mini stats equipos --}}
      <div class="grid grid-cols-3 gap-px bg-gray-100">
        <div class="bg-white px-4 py-3 text-center">
          <p style="font-family:'Syne',sans-serif;font-size:1.3rem;font-weight:800;color:var(--green)">{{ $equiposLibres }}</p>
          <p class="text-xs text-gray-500">Disponibles</p>
        </div>
        <div class="bg-white px-4 py-3 text-center">
          <p style="font-family:'Syne',sans-serif;font-size:1.3rem;font-weight:800;color:var(--magenta)">{{ $equiposPrestados }}</p>
          <p class="text-xs text-gray-500">Prestados</p>
        </div>
        <div class="bg-white px-4 py-3 text-center">
          <p style="font-family:'Syne',sans-serif;font-size:1.3rem;font-weight:800;color:var(--orange)">{{ $totalEquipos }}</p>
          <p class="text-xs text-gray-500">Total</p>
        </div>
      </div>
      <div class="divide-y divide-gray-50 max-h-48 overflow-y-auto">
        @forelse($recentEquipos as $eq)
          <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition-colors">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:{{ $eq->disponible ? 'rgba(0,182,122,.1)' : 'rgba(224,23,108,.1)' }}">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24"
                   style="stroke:{{ $eq->disponible ? 'var(--green)' : 'var(--magenta)' }}">
                <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="2"/>
                <path d="M8 19v2M16 19v2M5 19h14" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-gray-800 truncate">{{ $eq->nombre }}</p>
              <p class="text-xs text-gray-400">{{ $eq->codigo_inventario }}</p>
            </div>
            <span class="badge {{ $eq->disponible ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
              {{ $eq->disponible ? 'Libre' : 'Prestado' }}
            </span>
          </div>
        @empty
          <div class="px-5 py-8 text-center text-gray-400 text-sm">Sin equipos registrados.</div>
        @endforelse
      </div>
      <div class="px-5 py-3 border-t border-gray-50">
        <a href="{{ route('admin.equipos.index') }}" class="text-xs font-semibold" style="color:var(--magenta)">Ver todos los equipos →</a>
      </div>
    </div>
  </div>

  {{-- ── ROLES ── --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
      <h3 style="font-weight:700;font-size:.95rem;">Roles del sistema</h3>
      <a href="{{ route('admin.roles.index') }}" class="text-xs font-semibold" style="color:var(--blue)">Ver detalle →</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-px bg-gray-100">
      @php
        $roleConfig = [
          'admin'      => ['color'=>'var(--blue)',    'bg'=>'rgba(26,79,214,.08)',  'icon'=>'star'],
          'secretaria' => ['color'=>'var(--green)',   'bg'=>'rgba(0,182,122,.08)',  'icon'=>'room'],
          'tecnico'    => ['color'=>'var(--magenta)', 'bg'=>'rgba(224,23,108,.08)', 'icon'=>'eq'],
        ];
      @endphp
      @foreach($roles as $role)
        @php $rc = $roleConfig[$role->slug] ?? ['color'=>'#64748b','bg'=>'rgba(100,116,139,.08)','icon'=>'user']; @endphp
        <div class="bg-white px-5 py-4">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:{{ $rc['bg'] }}">
            @if($rc['icon']==='star')
              <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="{{ $rc['color'] }}" stroke-width="2" stroke-linejoin="round"/></svg>
            @elseif($rc['icon']==='room')
              <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke="{{ $rc['color'] }}" stroke-width="2"/><path d="M3 9h18M9 21V9" stroke="{{ $rc['color'] }}" stroke-width="2"/></svg>
            @elseif($rc['icon']==='eq')
              <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2" stroke="{{ $rc['color'] }}" stroke-width="2"/><path d="M8 19v2M16 19v2M5 19h14" stroke="{{ $rc['color'] }}" stroke-width="2" stroke-linecap="round"/></svg>
            @else
              <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" stroke="{{ $rc['color'] }}" stroke-width="2"/><path d="M4 20c0-3.314 3.582-6 8-6s8 2.686 8 6" stroke="{{ $rc['color'] }}" stroke-width="2" stroke-linecap="round"/></svg>
            @endif
          </div>
          <p class="font-bold text-gray-800 text-sm">{{ $role->nombre }}</p>
          <p class="text-xs text-gray-400 mt-0.5 leading-snug">{{ $role->descripcion }}</p>
          <p class="mt-3 text-xs font-semibold" style="color:{{ $rc['color'] }}">
            {{ $role->profiles_count ?? 0 }} usuario{{ ($role->profiles_count ?? 0) !== 1 ? 's' : '' }}
          </p>
        </div>
      @endforeach
    </div>
  </div>

  {{-- ── PRÉSTAMOS RECIENTES ── --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3">
      <h3 style="font-weight:700;font-size:.95rem;">Préstamos recientes</h3>
      <a href="{{ route('admin.reportes.index') }}" class="btn-primary text-xs" style="background:var(--orange)">
        Ver reporte completo
      </a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b border-gray-100">
          <tr class="text-gray-400 text-xs uppercase tracking-wider">
            <th class="text-left px-5 py-3 font-medium">Usuario</th>
            <th class="text-left px-5 py-3 font-medium">Aula</th>
            <th class="text-left px-5 py-3 font-medium">Fecha</th>
            <th class="text-left px-5 py-3 font-medium">Horario</th>
            <th class="text-left px-5 py-3 font-medium">Estado</th>
            <th class="text-left px-5 py-3 font-medium">Acción</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentLoans as $loan)
            @php
              $loanStates = [
                'pendiente'              => 'bg-yellow-100 text-yellow-700',
                'aprobado'               => 'bg-blue-100 text-blue-700',
                'activo'                 => 'bg-green-100 text-green-700',
                'finalizado'             => 'bg-gray-100 text-gray-500',
                'cancelado'              => 'bg-red-100 text-red-600',
                'liberado_por_tolerancia'=> 'bg-orange-100 text-orange-600',
              ];
              $stateClass = $loanStates[$loan->estado] ?? 'bg-gray-100 text-gray-500';
              $stateLabel = ucfirst(str_replace('_', ' ', $loan->estado));
            @endphp
            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
              <td class="px-5 py-3.5">
                <div class="font-medium text-gray-800 leading-tight">{{ $loan->user->name ?? '—' }}</div>
                <div class="text-xs text-gray-400">{{ $loan->user->email ?? '' }}</div>
              </td>
              <td class="px-5 py-3.5 font-medium text-gray-700">{{ $loan->aula->codigo ?? '—' }}</td>
              <td class="px-5 py-3.5 text-gray-500 text-xs">{{ \Carbon\Carbon::parse($loan->fecha_prestamo)->format('d M Y') }}</td>
              <td class="px-5 py-3.5 text-gray-500 text-xs">{{ substr($loan->hora_inicio,0,5) }} – {{ substr($loan->hora_fin,0,5) }}</td>
              <td class="px-5 py-3.5"><span class="badge {{ $stateClass }}">{{ $stateLabel }}</span></td>
              <td class="px-5 py-3.5">
                @if($loan->estado === 'pendiente')
                  <form method="POST" action="{{ route('admin.prestamos.aprobar', $loan->id) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-sm text-white" style="background:var(--green)">Aprobar</button>
                  </form>
                @elseif(in_array($loan->estado, ['aprobado','activo']))
                  <form method="POST" action="{{ route('admin.prestamos.cancelar', $loan->id) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-sm text-white" style="background:var(--magenta)">Cancelar</button>
                  </form>
                @else
                  <span class="text-xs text-gray-400">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400 text-sm">No hay préstamos registrados.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection