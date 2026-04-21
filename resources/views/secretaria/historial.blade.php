{{-- resources/views/secretaria/historial.blade.php --}}
@extends('layouts.dashboard')
@section('title', 'Historial')
@section('accent-color', 'var(--green)')
@section('role-label', '🏫 Secretaría')
@section('page-title', 'Historial de Préstamos')
@section('page-subtitle', 'Registro de actividad pasada')

@section('sidebar-nav')
  @include('secretaria.partials.sidebar')
@endsection



@section('content')
<div class="space-y-5">

  {{-- Filtros de fecha --}}
  <form method="GET" action="{{ route('secretaria.historial') }}"
        class="bg-white rounded-2xl shadow-sm p-4 flex flex-wrap gap-3 items-end">
    <div>
      <label class="block text-xs font-semibold text-gray-700 mb-1.5">Desde</label>
      <input type="date" name="desde" value="{{ $desde }}" class="field"/>
    </div>
    <div>
      <label class="block text-xs font-semibold text-gray-700 mb-1.5">Hasta</label>
      <input type="date" name="hasta" value="{{ $hasta }}" class="field"/>
    </div>
    <button type="submit" class="px-5 py-2.5 rounded-xl text-white font-semibold text-sm" style="background:var(--green)">Filtrar</button>
  </form>

  {{-- Resumen --}}
  <div class="grid grid-cols-3 gap-4">
    @foreach([
      ['Total del período', $resumen['total'],       'var(--blue)',    'rgba(26,79,214,.08)'],
      ['Finalizados',       $resumen['finalizados'], 'var(--green)',   'rgba(0,182,122,.08)'],
      ['Cancelados',        $resumen['cancelados'],  'var(--magenta)', 'rgba(224,23,108,.08)'],
    ] as [$lbl,$val,$col,$bg])
    <div class="bg-white rounded-2xl p-4 shadow-sm">
      <p style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:{{ $col }}">{{ $val }}</p>
      <p class="text-gray-500 text-sm mt-0.5">{{ $lbl }}</p>
    </div>
    @endforeach
  </div>

  {{-- Tabla --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
      <h3 style="font-weight:700;font-size:.95rem;">Préstamos finalizados / cancelados</h3>
      <p class="text-gray-400 text-xs mt-0.5">{{ $desde }} → {{ $hasta }}</p>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b border-gray-100">
          <tr class="text-gray-400 text-xs uppercase tracking-wider">
            <th class="text-left px-5 py-3 font-medium">Docente</th>
            <th class="text-left px-5 py-3 font-medium">Aula</th>
            <th class="text-left px-5 py-3 font-medium">Fecha</th>
            <th class="text-left px-5 py-3 font-medium">Horario</th>
            <th class="text-left px-5 py-3 font-medium">Estado</th>
            <th class="text-left px-5 py-3 font-medium">Aprobado por</th>
          </tr>
        </thead>
        <tbody>
          @forelse($prestamos as $p)
            @php
              $badge = [
                'finalizado'             => 'bg-gray-100 text-gray-600',
                'cancelado'              => 'bg-red-100 text-red-600',
                'liberado_por_tolerancia'=> 'bg-orange-100 text-orange-600',
              ][$p->estado] ?? 'bg-gray-100 text-gray-500';
            @endphp
            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
              <td class="px-5 py-3.5">
                <p class="font-semibold text-gray-800">{{ $p->user->name ?? '—' }}</p>
                <p class="text-xs text-gray-400">{{ $p->user->profile?->identificacion ?? '' }}</p>
              </td>
              <td class="px-5 py-3.5 font-bold text-gray-700">{{ $p->aula->codigo ?? '—' }}</td>
              <td class="px-5 py-3.5 text-gray-500 text-xs">{{ \Carbon\Carbon::parse($p->fecha_prestamo)->format('d M Y') }}</td>
              <td class="px-5 py-3.5 text-gray-500 text-xs">{{ substr($p->hora_inicio,0,5) }} – {{ substr($p->hora_fin,0,5) }}</td>
              <td class="px-5 py-3.5">
                <span class="badge {{ $badge }}">{{ ucfirst(str_replace('_',' ',$p->estado)) }}</span>
                @if($p->estado === 'cancelado' && $p->motivo_cancelacion)
                  <p class="text-xs text-gray-400 mt-0.5 italic">{{ Str::limit($p->motivo_cancelacion, 40) }}</p>
                @endif
              </td>
              <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $p->aprobadoPor->name ?? '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">Sin registros en este período.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $prestamos->links() }}</div>
  </div>

</div>
@endsection