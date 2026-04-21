{{-- resources/views/admin/aulas/horarios.blade.php --}}
@extends('layouts.dashboard')
@section('title', 'Horario — ' . $aula->codigo)
@section('accent-color', 'var(--blue)')
@section('role-label', '⭐ Administrador')
@section('page-title', 'Horario del Aula')
@section('page-subtitle', $aula->codigo . ($aula->nombre ? ' — ' . $aula->nombre : ''))

@section('sidebar-nav')
  <p class="nav-section">Principal</p>
  <a href="{{ route('dashboard.admin') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1" stroke="currentColor" stroke-width="2"/><rect x="14" y="3" width="7" height="5" rx="1" stroke="currentColor" stroke-width="2"/><rect x="14" y="12" width="7" height="9" rx="1" stroke="currentColor" stroke-width="2"/><rect x="3" y="16" width="7" height="5" rx="1" stroke="currentColor" stroke-width="2"/></svg>
    Dashboard
  </a>
  <p class="nav-section mt-2">Gestión</p>
  <a href="{{ route('admin.usuarios.index') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/></svg>
    Usuarios
  </a>
  <a href="{{ route('admin.roles.index') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
    Roles
  </a>
  <a href="{{ route('admin.aulas.index') }}" class="nav-item active" style="background:rgba(26,79,214,.15);color:#60a5fa;">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="2"/></svg>
    Aulas
  </a>
  <a href="{{ route('admin.equipos.index') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 19v2M16 19v2M5 19h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    Equipos
  </a>
  <a href="{{ route('admin.horarios.index') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    Horarios
  </a>
  <p class="nav-section mt-2">Análisis</p>
  <a href="{{ route('admin.reportes.index') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M9 17H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2h-2M9 17v4m6-4v4M9 21h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    Reportes
  </a>
  <a href="{{ route('admin.configuracion') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/><path d="M12 1v2M12 21v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M1 12h2M21 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    Configuración
  </a>
@endsection



@section('content')
<div x-data="horariosAulaApp()">

  {{-- Alertas --}}
  @if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
      {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      {{ session('error') }}
    </div>
  @endif

  {{-- Header con info del aula --}}
  <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div class="flex items-center gap-4">
      <a href="{{ route('admin.aulas.index') }}"
         class="w-9 h-9 rounded-xl bg-white shadow-sm flex items-center justify-center hover:bg-gray-50 transition-colors text-gray-500">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </a>
      <div>
        <div class="flex items-center gap-3">
          <h2 style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;">{{ $aula->codigo }}</h2>
          @if($aula->nombre)
            <span class="text-gray-400 text-sm">{{ $aula->nombre }}</span>
          @endif
          <span class="badge {{ $aula->estado === 'disponible' ? 'bg-green-100 text-green-700' : ($aula->estado === 'ocupada' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-700') }}">
            {{ ucfirst(str_replace('_', ' ', $aula->estado)) }}
          </span>
        </div>
        <p class="text-gray-400 text-sm mt-0.5">
          Capacidad: {{ $aula->capacidad }} personas
          @if($aula->ubicacion) · {{ $aula->ubicacion }}@endif
          · <span class="font-semibold text-indigo-600">{{ $horarios->count() }} {{ $horarios->count() === 1 ? 'clase programada' : 'clases programadas' }}</span>
        </p>
      </div>
    </div>
    <button @click="showForm=true"
      class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-semibold text-sm"
      style="background:var(--blue);box-shadow:0 4px 16px rgba(26,79,214,.3)">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg>
      Agregar clase
    </button>
  </div>

  {{-- ══ GRILLA SEMANAL VISUAL ══ --}}
  @php
    $dias = ['lunes','martes','miercoles','jueves','viernes','sabado'];
    $nombresDias = ['Lun','Mar','Mié','Jue','Vie','Sáb'];
  @endphp

  <div class="mb-6">
    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Distribución semanal</h3>
    <div class="grid-semana">
      @foreach($dias as $i => $dia)
        <div class="grid-dia">
          <div class="grid-dia-header header-{{ $dia }}">{{ $nombresDias[$i] }}</div>
          @if(isset($porDia[$dia]) && $porDia[$dia]->count())
            @foreach($porDia[$dia] as $h)
              <div class="grid-bloque {{ $dia }} {{ !$h->activo ? 'opacity-40' : '' }}">
                <div class="grid-bloque-hora">
                  {{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }}–{{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}
                </div>
                <div class="font-semibold text-[.7rem] mt-0.5 truncate" title="{{ $h->materia }}">{{ $h->materia }}</div>
                @if($h->grupo)
                  <div class="text-[.65rem] opacity-70">Gr. {{ $h->grupo }}</div>
                @endif
                @if(!$h->activo)
                  <div class="text-[.62rem] mt-0.5 opacity-60 italic">Inactivo</div>
                @endif
              </div>
            @endforeach
          @else
            <div class="grid-vacio">Sin clases</div>
          @endif
        </div>
      @endforeach
    </div>
  </div>

  {{-- ══ TABLA DE CLASES PROGRAMADAS ══ --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
      <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1rem;">Clases programadas</h3>
      <span class="text-gray-400 text-xs">El aula queda bloqueada para préstamos en los horarios activos</span>
    </div>

    @if($horarios->isEmpty())
      <div class="py-16 text-center text-gray-400">
        <svg class="mx-auto mb-3 opacity-30" width="40" height="40" fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <p class="font-medium text-sm">No hay clases programadas para esta aula</p>
        <button @click="showForm=true" class="mt-2 text-sm font-semibold text-blue-600 hover:underline">+ Agregar primera clase</button>
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-gray-400 text-xs uppercase tracking-wider border-b border-gray-100">
              <th class="text-left px-5 py-3 font-medium">Día</th>
              <th class="text-left px-5 py-3 font-medium">Horario</th>
              <th class="text-left px-5 py-3 font-medium">Materia / Grupo</th>
              <th class="text-left px-5 py-3 font-medium">Docente</th>
              <th class="text-left px-5 py-3 font-medium">Periodo</th>
              <th class="text-left px-5 py-3 font-medium">Estado</th>
              <th class="text-left px-5 py-3 font-medium">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @php
              $coloresDia = [
                'lunes'     => 'bg-blue-100 text-blue-700',
                'martes'    => 'bg-purple-100 text-purple-700',
                'miercoles' => 'bg-green-100 text-green-700',
                'jueves'    => 'bg-yellow-100 text-yellow-700',
                'viernes'   => 'bg-orange-100 text-orange-700',
                'sabado'    => 'bg-pink-100 text-pink-700',
              ];
            @endphp
            @foreach($horarios as $h)
              <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors {{ !$h->activo ? 'opacity-60' : '' }}">
                <td class="px-5 py-3.5">
                  <span class="badge {{ $coloresDia[$h->dia_semana] ?? 'bg-gray-100 text-gray-600' }}"
                        style="border-radius:6px;text-transform:uppercase;letter-spacing:.04em;font-size:.68rem">
                    {{ ucfirst($h->dia_semana) }}
                  </span>
                </td>
                <td class="px-5 py-3.5 font-mono text-xs text-gray-700 font-semibold">
                  {{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }}
                  –
                  {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}
                </td>
                <td class="px-5 py-3.5">
                  <p class="font-semibold text-gray-800">{{ $h->materia }}</p>
                  @if($h->grupo)<p class="text-gray-400 text-xs">Grupo {{ $h->grupo }}</p>@endif
                </td>
                <td class="px-5 py-3.5 text-gray-600 text-sm">{{ $h->docente?->name ?? '—' }}</td>
                <td class="px-5 py-3.5 text-xs text-gray-500">
                  {{ $h->fecha_inicio->format('d/m/Y') }}<br>al {{ $h->fecha_fin->format('d/m/Y') }}
                </td>
                <td class="px-5 py-3.5">
                  <span class="badge {{ $h->activo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $h->activo ? '🔒 Bloqueado' : 'Inactivo' }}
                  </span>
                </td>
                <td class="px-5 py-3.5">
                  <div class="flex gap-2 flex-wrap">
                    {{-- Toggle activo --}}
                    <form action="{{ route('admin.aulas.horarios.toggle', [$aula->id, $h->id]) }}" method="POST">
                      @csrf @method('PATCH')
                      <button type="submit"
                        class="text-xs font-semibold {{ $h->activo ? 'text-yellow-600' : 'text-green-600' }} hover:underline">
                        {{ $h->activo ? 'Desactivar' : 'Activar' }}
                      </button>
                    </form>
                    {{-- Eliminar --}}
                    <form action="{{ route('admin.aulas.horarios.destroy', [$aula->id, $h->id]) }}" method="POST"
                          onsubmit="return confirm('¿Eliminar este bloque de horario?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="text-xs font-semibold text-red-500 hover:underline">Eliminar</button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  {{-- ══ MODAL AGREGAR CLASE ══ --}}
  <div x-show="showForm" class="modal-overlay" @click.self="showForm=false" style="display:none">
    <div class="modal-box">
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <div>
          <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;">Agregar clase al aula {{ $aula->codigo }}</h3>
          <p class="text-gray-400 text-xs mt-0.5">El aula quedará bloqueada para préstamos en este horario</p>
        </div>
        <button @click="showForm=false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
      </div>

      <form action="{{ route('admin.aulas.horarios.store', $aula->id) }}" method="POST" class="px-6 py-5 space-y-4">
        @csrf

        @if($errors->any())
          <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 space-y-0.5">
            @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
          </div>
        @endif

        <div class="grid grid-cols-2 gap-4">

          {{-- Docente --}}
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Docente *</label>
            @if($docentes->isEmpty())
              <div class="rounded-xl bg-yellow-50 border border-yellow-200 px-4 py-3 text-sm text-yellow-700">
                No hay docentes registrados en el sistema.
                <a href="{{ route('admin.usuarios.create') }}" class="underline font-semibold ml-1">Crear docente</a>
              </div>
            @else
              <select name="docente_id" class="field" required>
                <option value="">Seleccionar docente...</option>
                @foreach($docentes as $d)
                  <option value="{{ $d->id }}" {{ old('docente_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                @endforeach
              </select>
              @error('docente_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            @endif
          </div>

          {{-- Materia --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Materia *</label>
            <input type="text" name="materia" value="{{ old('materia') }}" class="field" placeholder="Ej: Cálculo diferencial" required/>
            @error('materia')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Grupo --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Grupo</label>
            <input type="text" name="grupo" value="{{ old('grupo') }}" class="field" placeholder="2A, 3B..."/>
          </div>

          {{-- Día --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Día de la semana *</label>
            <select name="dia_semana" class="field" required>
              <option value="">Seleccionar día...</option>
              @foreach(['lunes','martes','miercoles','jueves','viernes','sabado'] as $d)
                <option value="{{ $d }}" {{ old('dia_semana') === $d ? 'selected' : '' }}>{{ ucfirst($d) }}</option>
              @endforeach
            </select>
            @error('dia_semana')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Hora inicio --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Hora inicio *</label>
            <input type="time" name="hora_inicio" value="{{ old('hora_inicio') }}" class="field" required/>
            @error('hora_inicio')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Hora fin --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Hora fin *</label>
            <input type="time" name="hora_fin" value="{{ old('hora_fin') }}" class="field" required/>
            @error('hora_fin')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Periodo inicio --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Inicio del periodo *</label>
            <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio') }}" class="field" required/>
            @error('fecha_inicio')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Periodo fin --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Fin del periodo *</label>
            <input type="date" name="fecha_fin" value="{{ old('fecha_fin') }}" class="field" required/>
            @error('fecha_fin')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>

        </div>

        <div class="rounded-xl bg-blue-50 border border-blue-100 px-4 py-3 text-xs text-blue-700 flex items-start gap-2">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" class="mt-0.5 flex-shrink-0"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          El aula <strong>{{ $aula->codigo }}</strong> quedará bloqueada cada <strong x-text="'...'">semana</strong> en la franja registrada durante el periodo indicado. La secretaría no podrá crear préstamos en ese horario.
        </div>

        <div class="flex gap-3 pt-2">
          <button type="button" @click="showForm=false"
            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50">
            Cancelar
          </button>
          <button type="submit"
            class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm"
            style="background:var(--blue)">
            Registrar clase
          </button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>window.SIGPA_PAGE = { hasErrors: {{ $errors->any() ? 'true' : 'false' }} };</script>
@endsection
