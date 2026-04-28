{{-- resources/views/admin/horarios/index.blade.php --}}
@extends('layouts.dashboard')
@section('title', 'Horarios Académicos')
@section('accent-color', 'var(--blue)')
@section('role-label', '⭐ Administrador')
@section('page-title', 'Horarios Académicos')
@section('page-subtitle', 'Gestión de clases programadas y bloqueo de aulas')

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
  <a href="{{ route('admin.aulas.index') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="2"/></svg>
    Aulas
  </a>
  <a href="{{ route('admin.equipos.index') }}" class="nav-item">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 19v2M16 19v2M5 19h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    Equipos
  </a>
  <a href="{{ route('admin.horarios.index') }}" class="nav-item active" style="background:rgba(26,79,214,.15);color:#60a5fa;">
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
<div x-data="horariosApp()">

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

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
    <div>
      <h2 style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;">Horario académico</h2>
      <p class="text-gray-500 text-sm mt-0.5">{{ $horarios->total() }} clases registradas · Las aulas quedan bloqueadas automáticamente</p>
    </div>
    <button @click="openCreate()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-semibold text-sm"
      style="background:var(--blue);box-shadow:0 4px 16px rgba(26,79,214,.3)">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg>
      Nueva Clase
    </button>
  </div>

  {{-- Filtros --}}
  <form method="GET" class="bg-white rounded-2xl shadow-sm p-4 mb-5 flex flex-wrap gap-3">
    <select name="dia" class="field w-auto min-w-36" onchange="this.form.submit()">
      <option value="">Todos los días</option>
      @foreach(['lunes','martes','miercoles','jueves','viernes','sabado'] as $d)
        <option value="{{ $d }}" {{ request('dia') === $d ? 'selected' : '' }}>{{ ucfirst($d) }}</option>
      @endforeach
    </select>
    <select name="aula_id" class="field w-auto min-w-40" onchange="this.form.submit()">
      <option value="">Todas las aulas</option>
      @foreach($aulas as $a)
        <option value="{{ $a->id }}" {{ request('aula_id') == $a->id ? 'selected' : '' }}>{{ $a->codigo }} — {{ $a->nombre }}</option>
      @endforeach
    </select>
    <select name="docente_id" class="field w-auto min-w-44" onchange="this.form.submit()">
      <option value="">Todos los docentes</option>
      @foreach($docentes as $d)
        <option value="{{ $d->id }}" {{ request('docente_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
      @endforeach
    </select>
    @if(request()->hasAny(['dia','aula_id','docente_id']))
      <a href="{{ route('admin.horarios.index') }}" class="field w-auto text-gray-500 text-sm flex items-center px-4">Limpiar</a>
    @endif
  </form>

  {{-- Tabla --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b border-gray-100">
          <tr class="text-gray-400 text-xs uppercase tracking-wider">
            <th class="text-left px-5 py-3 font-medium">Día</th>
            <th class="text-left px-5 py-3 font-medium">Horario</th>
            <th class="text-left px-5 py-3 font-medium">Materia / Grupo</th>
            <th class="text-left px-5 py-3 font-medium">Docente</th>
            <th class="text-left px-5 py-3 font-medium">Aula</th>
            <th class="text-left px-5 py-3 font-medium">Periodo</th>
            <th class="text-left px-5 py-3 font-medium">Estado</th>
            <th class="text-left px-5 py-3 font-medium">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @php
            $coloresDia = [
              'lunes'     => ['bg-blue-100',   'text-blue-700'],
              'martes'    => ['bg-purple-100',  'text-purple-700'],
              'miercoles' => ['bg-green-100',   'text-green-700'],
              'jueves'    => ['bg-yellow-100',  'text-yellow-700'],
              'viernes'   => ['bg-orange-100',  'text-orange-700'],
              'sabado'    => ['bg-pink-100',    'text-pink-700'],
            ];
          @endphp
          @forelse($horarios as $h)
            @php [$bgDia, $txtDia] = $coloresDia[$h->dia_semana] ?? ['bg-gray-100','text-gray-600']; @endphp
            <tr class="border-b border-gray-50 transition-colors">
              <td class="px-5 py-3.5">
                <span class="dia-pill {{ $bgDia }} {{ $txtDia }}">{{ ucfirst($h->dia_semana) }}</span>
              </td>
              <td class="px-5 py-3.5 font-mono text-xs text-gray-700">
                {{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }}
                –
                {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}
              </td>
              <td class="px-5 py-3.5">
                <p class="font-semibold text-gray-800">{{ $h->materia }}</p>
                @if($h->grupo)<p class="text-gray-400 text-xs">Grupo {{ $h->grupo }}</p>@endif
              </td>
              <td class="px-5 py-3.5 text-gray-600">{{ $h->docente?->name ?? '—' }}</td>
              <td class="px-5 py-3.5">
                <span class="font-mono font-semibold text-blue-700">{{ $h->aula?->codigo }}</span>
                @if($h->aula?->nombre)<span class="text-gray-400 text-xs ml-1">{{ $h->aula->nombre }}</span>@endif
              </td>
              <td class="px-5 py-3.5 text-xs text-gray-500">
                {{ $h->fecha_inicio->format('d/m/Y') }} al {{ $h->fecha_fin->format('d/m/Y') }}
              </td>
              <td class="px-5 py-3.5">
                @if($h->activo)
                  <span class="badge bg-green-100 text-green-700">Activo</span>
                @else
                  <span class="badge bg-gray-100 text-gray-500">Inactivo</span>
                @endif
              </td>
              <td class="px-5 py-3.5">
                <div class="flex gap-2">
                  <button @click="openEdit({{ $h->id }})" class="text-xs font-semibold text-blue-600 hover:underline">Editar</button>
                  <form action="{{ route('admin.horarios.destroy', $h->id) }}" method="POST"
                        onsubmit="return confirm('¿Eliminar este horario? El aula quedará disponible en ese bloque.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs font-semibold text-red-500 hover:underline">Eliminar</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="px-5 py-10 text-center text-gray-400">
                No hay horarios registrados.
                <button @click="openCreate()" class="text-blue-600 hover:underline ml-1">Registrar el primero</button>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $horarios->links() }}</div>
  </div>

  {{-- Aviso sobre bloqueo automático --}}
  <div class="mt-4 rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-700 flex items-start gap-3">
    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" class="mt-0.5 flex-shrink-0"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    <p>Los horarios activos <strong>reservan el aula</strong> en esa franja horaria. Durante la clase, la secretaría verá el aula en estado <em>En espera de check-in</em> y deberá confirmar la asistencia para marcarla como ocupada.</p>
  </div>

  {{-- Modal Crear / Editar --}}
  <div x-show="showModal" class="modal-overlay" @click.self="showModal=false" style="display:none">
    <div class="modal-box">
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;" x-text="editId ? 'Editar Horario' : 'Nueva Clase Programada'"></h3>
        <button @click="showModal=false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
      </div>

      <form :action="editId ? '/admin/horarios/' + editId : '/admin/horarios'" method="POST" class="px-6 py-5 space-y-4">
        @csrf
        <input type="hidden" name="_method" :value="editId ? 'PUT' : 'POST'"/>

        @if($errors->any())
          <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 space-y-0.5">
            @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
          </div>
        @endif

        <div class="grid grid-cols-2 gap-4">

          {{-- Docente --}}
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Docente *</label>
            <select name="docente_id" x-model="form.docente_id" class="field" required>
              <option value="">Seleccionar docente...</option>
              @foreach($docentes as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
              @endforeach
            </select>
            @error('docente_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Materia --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Materia *</label>
            <input type="text" name="materia" x-model="form.materia" class="field" placeholder="Cálculo diferencial" required/>
            @error('materia')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Grupo --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Grupo</label>
            <input type="text" name="grupo" x-model="form.grupo" class="field" placeholder="2A, 3B..."/>
          </div>

          {{-- Aula --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Aula *</label>
            <select name="aula_id" x-model="form.aula_id" class="field" required>
              <option value="">Seleccionar aula...</option>
              @foreach($aulas as $a)
                <option value="{{ $a->id }}">{{ $a->codigo }}{{ $a->nombre ? ' — '.$a->nombre : '' }}</option>
              @endforeach
            </select>
            @error('aula_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Día de la semana --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Día de la semana *</label>
            <select name="dia_semana" x-model="form.dia_semana" class="field" required>
              <option value="">Seleccionar día...</option>
              @foreach(['lunes','martes','miercoles','jueves','viernes','sabado'] as $d)
                <option value="{{ $d }}">{{ ucfirst($d) }}</option>
              @endforeach
            </select>
            @error('dia_semana')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Hora inicio --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Hora inicio *</label>
            <input type="time" name="hora_inicio" x-model="form.hora_inicio" class="field" required/>
            @error('hora_inicio')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Hora fin --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Hora fin *</label>
            <input type="time" name="hora_fin" x-model="form.hora_fin" class="field" required/>
            @error('hora_fin')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Fecha inicio del periodo --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Inicio del periodo *</label>
            <input type="date" name="fecha_inicio" x-model="form.fecha_inicio" class="field" required/>
            @error('fecha_inicio')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Fecha fin del periodo --}}
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Fin del periodo *</label>
            <input type="date" name="fecha_fin" x-model="form.fecha_fin" class="field" required/>
            @error('fecha_fin')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Activo (solo al editar) --}}
          <div x-show="editId" class="col-span-2">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" name="activo" value="1" x-model="form.activo" class="w-4 h-4 rounded" style="accent-color:var(--blue)"/>
              <span class="text-sm text-gray-700">Horario activo (reserva el aula · requiere check-in)</span>
            </label>
          </div>

        </div>

        <div class="flex gap-3 pt-2">
          <button type="button" @click="showModal=false"
                  class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50">
            Cancelar
          </button>
          <button type="submit"
                  class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm"
                  style="background:var(--blue)"
                  x-text="editId ? 'Actualizar' : 'Registrar Horario'">
          </button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection

@section('scripts')
@php
  $pageData = [
    'horariosData' => $horarios->getCollection()->keyBy('id'),
    'hasErrors'    => $errors->any(),
    'editId'       => $errors->any() && old('_method') === 'PUT' ? request()->route('horario') : null,
    'oldForm'      => $errors->any() ? [
      'docente_id'  => old('docente_id'),
      'aula_id'     => old('aula_id'),
      'materia'     => old('materia'),
      'grupo'       => old('grupo'),
      'dia_semana'  => old('dia_semana'),
      'hora_inicio' => old('hora_inicio'),
      'hora_fin'    => old('hora_fin'),
      'fecha_inicio'=> old('fecha_inicio'),
      'fecha_fin'   => old('fecha_fin'),
    ] : null,
  ];
@endphp
<script>window.SIGPA_PAGE = @json($pageData);</script>
@endsection
