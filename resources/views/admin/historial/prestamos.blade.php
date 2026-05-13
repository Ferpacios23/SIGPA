{{-- resources/views/admin/historial/prestamos.blade.php --}}
@extends('layouts.dashboard')
@section('title', 'Historial de préstamos')
@section('accent-color', 'var(--orange)')
@section('role-label', '⭐ Administrador')
@section('page-title', 'Historial de préstamos')
@section('page-subtitle', 'Registro completo de todos los préstamos de aulas')

@section('sidebar-nav')
  @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="space-y-5">

  {{-- Filtros --}}
  <div class="bg-white rounded-2xl shadow-sm p-5">
    <form method="GET" action="{{ route('admin.historial.prestamos') }}" class="flex flex-wrap gap-4 items-end">
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
        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Estado</label>
        <select name="estado" class="field border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-orange-400">
          <option value="">Todos</option>
          @foreach($estados as $e)
            <option value="{{ $e }}" @selected($estado === $e)>{{ ucfirst(str_replace('_', ' ', $e)) }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Usuario</label>
        <input type="text" name="usuario" value="{{ $usuario }}" placeholder="Nombre o correo"
               class="border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-orange-400"/>
      </div>
      <button type="submit" class="px-5 py-2.5 rounded-xl text-white font-semibold text-sm" style="background:var(--orange)">
        Filtrar
      </button>
      @if($estado || $usuario)
        <a href="{{ route('admin.historial.prestamos', ['desde'=>$desde,'hasta'=>$hasta]) }}"
           class="px-5 py-2.5 rounded-xl font-semibold text-sm text-gray-600 border border-gray-200">
          Limpiar
        </a>
      @endif
    </form>
  </div>

  {{-- Contador --}}
  <div class="flex items-center justify-between">
    <p class="text-sm text-gray-500">
      <span class="font-semibold text-gray-800">{{ $prestamos->total() }}</span> registros encontrados
    </p>
    <p class="text-xs text-gray-400">{{ $desde }} → {{ $hasta }}</p>
  </div>

  {{-- Tabla --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
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
            <th class="text-left px-5 py-3 font-medium">Motivo cancelación</th>
          </tr>
        </thead>
        <tbody>
          @forelse($prestamos as $p)
            @php
              $badge = [
                'pendiente'              => 'bg-yellow-100 text-yellow-700',
                'aprobado'               => 'bg-blue-100 text-blue-700',
                'activo'                 => 'bg-green-100 text-green-700',
                'finalizado'             => 'bg-gray-100 text-gray-500',
                'cancelado'              => 'bg-red-100 text-red-600',
                'liberado_por_tolerancia'=> 'bg-orange-100 text-orange-600',
              ][$p->estado] ?? 'bg-gray-100 text-gray-500';
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
              <td class="px-5 py-3">
                <span class="badge {{ $badge }}">
                  {{ ucfirst(str_replace('_', ' ', $p->estado)) }}
                </span>
              </td>
              <td class="px-5 py-3 text-gray-500 text-xs">{{ $p->aprobadoPor->name ?? '—' }}</td>
              <td class="px-5 py-3 text-gray-500 text-xs">{{ $p->motivo_cancelacion ?? '—' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="px-5 py-10 text-center text-gray-400">
                Sin préstamos en este período.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $prestamos->links() }}</div>
  </div>

</div>
@endsection
