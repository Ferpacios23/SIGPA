{{-- resources/views/admin/historial/accesos.blade.php --}}
@extends('layouts.dashboard')
@section('title', 'Historial de accesos')
@section('accent-color', 'var(--orange)')
@section('role-label', '⭐ Administrador')
@section('page-title', 'Historial de accesos')
@section('page-subtitle', 'Registro de inicios y cierres de sesión en el sistema')

@section('sidebar-nav')
  @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="space-y-5">

  {{-- Filtros --}}
  <div class="bg-white rounded-2xl shadow-sm p-5">
    <form method="GET" action="{{ route('admin.historial.accesos') }}" class="flex flex-wrap gap-4 items-end">
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
        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tipo</label>
        <select name="tipo" class="field border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-orange-400">
          <option value="">Todos</option>
          <option value="login"  @selected($tipo === 'login')>Inicio de sesión</option>
          <option value="logout" @selected($tipo === 'logout')>Cierre de sesión</option>
        </select>
      </div>
      <button type="submit" class="px-5 py-2.5 rounded-xl text-white font-semibold text-sm" style="background:var(--orange)">
        Filtrar
      </button>
    </form>
  </div>

  {{-- Contador --}}
  <div class="flex items-center justify-between">
    <p class="text-sm text-gray-500">
      <span class="font-semibold text-gray-800">{{ $accesos->total() }}</span> registros encontrados
    </p>
    <p class="text-xs text-gray-400">{{ $desde }} → {{ $hasta }}</p>
  </div>

  {{-- Tabla --}}
  <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b border-gray-100">
          <tr class="text-gray-400 text-xs uppercase tracking-wider">
            <th class="text-left px-5 py-3 font-medium">Fecha y hora</th>
            <th class="text-left px-5 py-3 font-medium">Usuario</th>
            <th class="text-left px-5 py-3 font-medium">Tipo</th>
            <th class="text-left px-5 py-3 font-medium">Dirección IP</th>
            <th class="text-left px-5 py-3 font-medium">Navegador / Dispositivo</th>
          </tr>
        </thead>
        <tbody>
          @forelse($accesos as $a)
            @php
              $esLogin = $a->tipo_accion === 'acceso_login';
            @endphp
            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
              <td class="px-5 py-3 text-gray-500 text-xs whitespace-nowrap">
                {{ $a->created_at->format('d M Y, H:i:s') }}
              </td>
              <td class="px-5 py-3">
                <p class="font-medium text-gray-800">{{ $a->user->name ?? '(usuario eliminado)' }}</p>
                <p class="text-xs text-gray-400">{{ $a->user->email ?? '' }}</p>
              </td>
              <td class="px-5 py-3">
                @if($esLogin)
                  <span class="badge bg-green-100 text-green-700">Ingreso</span>
                @else
                  <span class="badge bg-gray-100 text-gray-500">Salida</span>
                @endif
              </td>
              <td class="px-5 py-3 text-gray-500 text-xs font-mono">{{ $a->ip_address ?? '—' }}</td>
              <td class="px-5 py-3 text-gray-400 text-xs truncate max-w-xs" title="{{ $a->user_agent }}">
                {{ $a->user_agent ? \Illuminate\Support\Str::limit($a->user_agent, 60) : '—' }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-5 py-10 text-center text-gray-400">
                Sin registros de acceso en este período.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $accesos->links() }}</div>
  </div>

</div>
@endsection
