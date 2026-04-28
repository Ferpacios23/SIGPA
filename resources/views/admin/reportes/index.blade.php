{{-- resources/views/admin/reportes/index.blade.php --}}
@extends('layouts.dashboard')
@section('title', 'Reportes')
@section('accent-color', 'var(--orange)')
@section('role-label', '⭐ Administrador')
@section('page-title', 'Reportes')
@section('page-subtitle', 'Historial y análisis de préstamos')
 
@section('sidebar-nav')
  @include('admin.partials.sidebar')
@endsection
 
@section('content')
<div class="space-y-5">
 
  {{-- Filtros de fecha --}}
  <div class="bg-white rounded-2xl shadow-sm p-5">
    <form method="GET" action="{{ route('admin.reportes.index') }}" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Desde</label>
        <input type="date" name="desde" value="{{ $desde }}" class="border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-orange-400"/>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Hasta</label>
        <input type="date" name="hasta" value="{{ $hasta }}" class="border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-orange-400"/>
      </div>
      <button type="submit" class="px-5 py-2.5 rounded-xl text-white font-semibold text-sm" style="background:var(--orange)">Filtrar</button>
      <a href="{{ route('admin.reportes.export', ['desde'=>$desde,'hasta'=>$hasta]) }}"
         class="px-5 py-2.5 rounded-xl text-white font-semibold text-sm inline-flex items-center gap-2"
         style="background:#0d1117">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg>
        Exportar CSV
      </a>
    </form>
  </div>
 
  {{-- Resumen --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach([
      ['Total préstamos', $resumen['total'],       'var(--blue)',    'rgba(26,79,214,.08)'],
      ['Aprobados',       $resumen['aprobados'],   'var(--green)',   'rgba(0,182,122,.08)'],
      ['Finalizados',     $resumen['finalizados'], 'var(--orange)',  'rgba(247,107,28,.08)'],
      ['Cancelados',      $resumen['cancelados'],  'var(--magenta)', 'rgba(224,23,108,.08)'],
    ] as [$lbl,$val,$col,$bg])
    <div class="bg-white rounded-2xl p-4 shadow-sm">
      <p style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:{{ $col }}">{{ $val }}</p>
      <p class="text-gray-500 text-sm mt-0.5">{{ $lbl }}</p>
    </div>
    @endforeach
  </div>
 
  {{-- Aulas más usadas --}}
  @if($aulasMasUsadas->isNotEmpty())
  <div class="bg-white rounded-2xl shadow-sm p-5">
    <h3 style="font-family:'Syne',sans-serif;font-weight:700;font-size:.95rem;margin-bottom:16px;">Aulas más solicitadas</h3>
    <div class="space-y-3">
      @foreach($aulasMasUsadas as $aula)
        @php $pct = $resumen['total'] > 0 ? round($aula->prestamos_count / $resumen['total'] * 100) : 0; @endphp
        <div>
          <div class="flex justify-between text-xs text-gray-600 mb-1">
            <span class="font-medium">{{ $aula->codigo }}{{ $aula->nombre ? ' – '.$aula->nombre : '' }}</span>
            <span>{{ $aula->prestamos_count }} préstamos ({{ $pct }}%)</span>
          </div>
          <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
            <div class="h-full rounded-full" style="width:{{ $pct }}%;background:var(--orange);transition:width 1s ease"></div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
  @endif
 
  {{-- Tabla de préstamos --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
      <h3 style="font-family:'Syne',sans-serif;font-weight:700;font-size:.95rem;">Detalle de préstamos</h3>
      <p class="text-gray-400 text-xs mt-0.5">{{ $desde }} → {{ $hasta }}</p>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b border-gray-100">
          <tr class="text-gray-400 text-xs uppercase tracking-wider">
            <th class="text-left px-5 py-3 font-medium">#</th>
            <th class="text-left px-5 py-3 font-medium">Usuario</th>
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
              $badge = ['pendiente'=>'bg-yellow-100 text-yellow-700','aprobado'=>'bg-blue-100 text-blue-700','activo'=>'bg-green-100 text-green-700','finalizado'=>'bg-gray-100 text-gray-500','cancelado'=>'bg-red-100 text-red-600','liberado_por_tolerancia'=>'bg-orange-100 text-orange-600'][$p->estado] ?? 'bg-gray-100 text-gray-500';
            @endphp
            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
              <td class="px-5 py-3 text-gray-400 text-xs">{{ $p->id }}</td>
              <td class="px-5 py-3">
                <p class="font-medium text-gray-800">{{ $p->user->name ?? '—' }}</p>
                <p class="text-xs text-gray-400">{{ $p->user->email ?? '' }}</p>
              </td>
              <td class="px-5 py-3 font-bold text-gray-700">{{ $p->aula->codigo ?? '—' }}</td>
              <td class="px-5 py-3 text-gray-500 text-xs">{{ \Carbon\Carbon::parse($p->fecha_prestamo)->format('d M Y') }}</td>
              <td class="px-5 py-3 text-gray-500 text-xs">{{ substr($p->hora_inicio,0,5) }} – {{ substr($p->hora_fin,0,5) }}</td>
              <td class="px-5 py-3"><span class="badge {{ $badge }}">{{ ucfirst(str_replace('_',' ',$p->estado)) }}</span></td>
              <td class="px-5 py-3 text-gray-500 text-xs">{{ $p->aprobadoPor->name ?? '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400">Sin préstamos en este período.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $prestamos->links() }}</div>
  </div>
</div>
@endsection