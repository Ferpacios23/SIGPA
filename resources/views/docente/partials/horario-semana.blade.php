{{-- Partial: grilla de horario semanal del docente --}}
{{-- Requiere: $nombresD, $diasConFecha, $horariosSemana --}}
<div class="grid-semana">
  @foreach($nombresD as $dia)
    @php
      $fecha  = $diasConFecha[$dia];
      $clases = $horariosSemana[$dia];
      $esHoy  = $fecha->isToday();
    @endphp
    <div class="grid-dia {{ $esHoy ? 'grid-dia-hoy' : '' }}">

      <div class="grid-dia-header header-{{ $dia }}">{{ ucfirst($dia) }}</div>

      <div class="flex items-center justify-between px-2 py-1">
        <p class="text-xs text-gray-400">{{ $fecha->format('d/m') }}</p>
        @if($esHoy)
          <span class="badge-hoy">HOY</span>
        @endif
      </div>

      @if($clases->isEmpty())
        <p class="grid-vacio">Sin clases</p>
      @else
        @foreach($clases as $h)
          <div class="grid-bloque grid-bloque-orange">
            <p class="bloque-nombre">{{ $h->materia }}</p>
            <p class="bloque-hora">{{ substr($h->hora_inicio,0,5) }}–{{ substr($h->hora_fin,0,5) }}</p>
            <p class="bloque-salon">{{ $h->aula?->nombre ?? $h->aula?->codigo ?? '—' }}</p>
            @if($h->grupo)
              <p class="bloque-grupo">{{ $h->grupo }}</p>
            @endif
          </div>
        @endforeach
      @endif

    </div>
  @endforeach
</div>
