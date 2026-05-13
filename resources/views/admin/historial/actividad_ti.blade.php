{{-- resources/views/admin/historial/actividad_ti.blade.php --}}
@extends('layouts.dashboard')
@section('title', 'Actividad Técnico TI')
@section('accent-color', 'var(--orange)')
@section('role-label', '⭐ Administrador')
@section('page-title', 'Actividad del área TI')
@section('page-subtitle', 'Registro de todo lo creado, modificado y gestionado por el Técnico TI')

@section('sidebar-nav')
  @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="space-y-5">

  {{-- Filtros --}}
  <div class="bg-white rounded-2xl shadow-sm p-5">
    <form method="GET" action="{{ route('admin.historial.actividad-ti') }}" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Desde</label>
        <input type="date" name="desde" value="{{ $desde }}"
               class="border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-orange-400"/>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Hasta</label>
        <input type="date" name="hasta" value="{{ $hasta }}"
               class="border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-orange-400"/>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tipo de acción</label>
        <select name="accion" class="field border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-orange-400">
          <option value="">Todas</option>
          <option value="creacion_equipo"   @selected($accion === 'creacion_equipo')>Creó equipo</option>
          <option value="cambio_estado_equipo" @selected($accion === 'cambio_estado_equipo')>Modificó estado</option>
          <option value="asignacion_ti"     @selected($accion === 'asignacion_ti')>Asignó equipo</option>
          <option value="devolucion_ti"     @selected($accion === 'devolucion_ti')>Registró devolución</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Técnico</label>
        <input type="text" name="usuario" value="{{ $usuario }}" placeholder="Nombre o correo"
               class="border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-orange-400"/>
      </div>
      <button type="submit" class="px-5 py-2.5 rounded-xl text-white font-semibold text-sm" style="background:var(--orange)">
        Filtrar
      </button>
      @if($accion || $usuario)
        <a href="{{ route('admin.historial.actividad-ti', ['desde'=>$desde,'hasta'=>$hasta]) }}"
           class="px-5 py-2.5 rounded-xl font-semibold text-sm text-gray-600 border border-gray-200">
          Limpiar
        </a>
      @endif
    </form>
  </div>

  {{-- Tarjetas de resumen --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach([
      ['Equipos creados',       $resumen['creacion_equipo'],  'var(--green)',   'rgba(0,182,122,.08)',   'creacion_equipo'],
      ['Estados modificados',   $resumen['cambio_estado'],    'var(--orange)',  'rgba(247,107,28,.08)',  'cambio_estado_equipo'],
      ['Equipos asignados',     $resumen['asignacion'],       'var(--blue)',    'rgba(26,79,214,.08)',   'asignacion_ti'],
      ['Devoluciones',          $resumen['devolucion'],       'var(--magenta)', 'rgba(224,23,108,.08)',  'devolucion_ti'],
    ] as [$lbl, $val, $col, $bg, $filtro])
    <a href="{{ route('admin.historial.actividad-ti', ['desde'=>$desde,'hasta'=>$hasta,'accion'=>$filtro]) }}"
       class="bg-white rounded-2xl p-4 shadow-sm block hover:shadow-md transition-shadow">
      <p style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:{{ $col }}">{{ $val }}</p>
      <p class="text-gray-500 text-sm mt-0.5">{{ $lbl }}</p>
    </a>
    @endforeach
  </div>

  {{-- Tabla de actividad --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
      <div>
        <h3 style="font-family:'Syne',sans-serif;font-weight:700;font-size:.95rem;">Detalle de acciones</h3>
        <p class="text-gray-400 text-xs mt-0.5">{{ $registros->total() }} registros · {{ $desde }} → {{ $hasta }}</p>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b border-gray-100">
          <tr class="text-gray-400 text-xs uppercase tracking-wider">
            <th class="text-left px-5 py-3 font-medium">Fecha y hora</th>
            <th class="text-left px-5 py-3 font-medium">Técnico</th>
            <th class="text-left px-5 py-3 font-medium">Acción</th>
            <th class="text-left px-5 py-3 font-medium">Descripción</th>
            <th class="text-left px-5 py-3 font-medium">Detalle del cambio</th>
          </tr>
        </thead>
        <tbody>
          @forelse($registros as $r)
            @php
              $config = [
                'creacion_equipo'      => ['label' => 'Creó equipo',        'badge' => 'bg-green-100 text-green-700'],
                'cambio_estado_equipo' => ['label' => 'Modificó estado',    'badge' => 'bg-orange-100 text-orange-600'],
                'asignacion_ti'        => ['label' => 'Asignó equipo',      'badge' => 'bg-blue-100 text-blue-700'],
                'devolucion_ti'        => ['label' => 'Registró devolución','badge' => 'bg-purple-100 text-purple-700'],
              ][$r->tipo_accion] ?? ['label' => $r->tipo_accion, 'badge' => 'bg-gray-100 text-gray-500'];

              $payload = is_array($r->payload) ? $r->payload : (json_decode($r->payload, true) ?? []);
            @endphp
            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
              <td class="px-5 py-3 text-gray-500 text-xs whitespace-nowrap">
                {{ $r->created_at->format('d M Y, H:i') }}
              </td>
              <td class="px-5 py-3">
                <p class="font-medium text-gray-800">{{ $r->user->name ?? '—' }}</p>
                <p class="text-xs text-gray-400">{{ $r->user->email ?? '' }}</p>
              </td>
              <td class="px-5 py-3">
                <span class="badge {{ $config['badge'] }}">{{ $config['label'] }}</span>
              </td>
              <td class="px-5 py-3 text-gray-600 text-xs max-w-xs">
                {{ $r->descripcion }}
              </td>
              <td class="px-5 py-3 text-xs">
                @if($r->tipo_accion === 'cambio_estado_equipo' && isset($payload['estado_anterior'], $payload['estado_nuevo']))
                  <div class="flex items-center gap-1.5">
                    <span class="badge bg-gray-100 text-gray-500">{{ $payload['estado_anterior'] }}</span>
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    <span class="badge bg-orange-100 text-orange-700">{{ $payload['estado_nuevo'] }}</span>
                  </div>
                  @if(!empty($payload['observacion']))
                    <p class="text-gray-400 mt-1">Obs: {{ $payload['observacion'] }}</p>
                  @endif
                @elseif($r->tipo_accion === 'creacion_equipo' && isset($payload['codigo_inventario']))
                  <p class="text-gray-500">Cód: <span class="font-mono font-semibold">{{ $payload['codigo_inventario'] }}</span></p>
                  @if(!empty($payload['marca']))
                    <p class="text-gray-400">{{ $payload['marca'] }}{{ isset($payload['modelo']) ? ' · '.$payload['modelo'] : '' }}</p>
                  @endif
                @elseif($r->tipo_accion === 'asignacion_ti' && isset($payload['aula']))
                  <p class="text-gray-500">Aula: <span class="font-semibold">{{ $payload['aula'] }}</span></p>
                @elseif($r->tipo_accion === 'devolucion_ti' && isset($payload['estado_devolucion']))
                  <span class="badge {{ match($payload['estado_devolucion']) {
                    'bueno'       => 'bg-green-100 text-green-700',
                    'regular'     => 'bg-yellow-100 text-yellow-700',
                    'dañado'      => 'bg-red-100 text-red-600',
                    'dado_de_baja'=> 'bg-gray-100 text-gray-500',
                    default       => 'bg-gray-100 text-gray-500',
                  } }}">
                    Estado: {{ $payload['estado_devolucion'] }}
                  </span>
                @else
                  <span class="text-gray-300">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-5 py-10 text-center text-gray-400">
                Sin actividad del área TI en este período.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $registros->links() }}</div>
  </div>

</div>
@endsection
