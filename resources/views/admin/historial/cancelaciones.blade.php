{{-- resources/views/admin/historial/cancelaciones.blade.php --}}
@extends('layouts.dashboard')
@section('title', 'Cancelaciones por inasistencia')
@section('accent-color', 'var(--orange)')
@section('role-label', '⭐ Administrador')
@section('page-title', 'Cancelaciones por inasistencia')
@section('page-subtitle', 'Préstamos cancelados o liberados por falta de asistencia')

@section('sidebar-nav')
  @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="space-y-5">

  {{-- Filtros --}}
  <div class="bg-white rounded-2xl shadow-sm p-5">
    <form method="GET" action="{{ route('admin.historial.cancelaciones') }}" class="flex flex-wrap gap-4 items-end">
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
      <button type="submit" class="px-5 py-2.5 rounded-xl text-white font-semibold text-sm" style="background:var(--orange)">
        Filtrar
      </button>
    </form>
  </div>

  {{-- Resumen --}}
  <div class="grid grid-cols-2 gap-4">
    <div class="bg-white rounded-2xl p-4 shadow-sm">
      <p style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:var(--orange)">
        {{ $totales['inasistencia'] }}
      </p>
      <p class="text-gray-500 text-sm mt-0.5">Liberados por tolerancia (inasistencia)</p>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm">
      <p style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:var(--magenta)">
        {{ $totales['cancelado'] }}
      </p>
      <p class="text-gray-500 text-sm mt-0.5">Cancelados manualmente</p>
    </div>
  </div>

  {{-- Tabla --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
      <h3 style="font-family:'Syne',sans-serif;font-weight:700;font-size:.95rem;">
        Detalle de cancelaciones
      </h3>
      <p class="text-gray-400 text-xs mt-0.5">{{ $desde }} → {{ $hasta }}</p>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b border-gray-100">
          <tr class="text-gray-400 text-xs uppercase tracking-wider">
            <th class="text-left px-5 py-3 font-medium">#</th>
            <th class="text-left px-5 py-3 font-medium">Usuario</th>
            <th class="text-left px-5 py-3 font-medium">Aula</th>
            <th class="text-left px-5 py-3 font-medium">Fecha préstamo</th>
            <th class="text-left px-5 py-3 font-medium">Horario</th>
            <th class="text-left px-5 py-3 font-medium">Motivo</th>
            <th class="text-left px-5 py-3 font-medium">Estado</th>
            <th class="text-left px-5 py-3 font-medium">Cancelado en</th>
          </tr>
        </thead>
        <tbody>
          @forelse($cancelaciones as $p)
            @php
              $esInasistencia = $p->estado === 'liberado_por_tolerancia';
              $badge = $esInasistencia
                ? 'bg-orange-100 text-orange-600'
                : 'bg-red-100 text-red-600';
              $label = $esInasistencia ? 'Por inasistencia' : 'Cancelado';
            @endphp
            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
              <td class="px-5 py-3 text-gray-400 text-xs">{{ $p->id }}</td>
              <td class="px-5 py-3">
                <p class="font-medium text-gray-800">{{ $p->user->name ?? '—' }}</p>
                <p class="text-xs text-gray-400">{{ $p->user->email ?? '' }}</p>
              </td>
              <td class="px-5 py-3 font-bold text-gray-700">{{ $p->aula->codigo ?? '—' }}</td>
              <td class="px-5 py-3 text-gray-500 text-xs">
                {{ \Carbon\Carbon::parse($p->fecha_prestamo)->format('d M Y') }}
              </td>
              <td class="px-5 py-3 text-gray-500 text-xs">
                {{ substr($p->hora_inicio, 0, 5) }} – {{ substr($p->hora_fin, 0, 5) }}
              </td>
              <td class="px-5 py-3 text-gray-500 text-xs">{{ $p->motivo_cancelacion ?? '—' }}</td>
              <td class="px-5 py-3">
                <span class="badge {{ $badge }}">{{ $label }}</span>
              </td>
              <td class="px-5 py-3 text-gray-400 text-xs">
                {{ $p->cancelado_en ? \Carbon\Carbon::parse($p->cancelado_en)->format('d M Y, H:i') : '—' }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="px-5 py-10 text-center text-gray-400">
                Sin cancelaciones en este período.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $cancelaciones->links() }}</div>
  </div>

</div>
@endsection
