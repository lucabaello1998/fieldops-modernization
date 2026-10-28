@extends('layouts.app')

@section('titulo', 'Alta · OT ' . ($alta->ot ?: $alta->id))
@section('subtitulo', $alta->cliente ?: 'Detalle de instalación')

@section('contenido')
    @php
        // Bloque de datos del domicilio: [etiqueta, valor, monoespaciado].
        $domicilio = [
            ['Dirección', $alta->direccion, false],
            ['Localidad', $alta->localidad, false],
            ['Teléfono', $alta->telefono, false],
            ['NIM', $alta->nim, true],
            ['ID de actividad', $alta->id_actividad, true],
            ['Zona de tarea', $alta->zona_tarea, false],
        ];

        // Equipos agrupados por tipo: [etiqueta, valor].
        $gruposEquipos = [
            'ONT' => [
                ['SN', $alta->sn_ont],
                ['MAC', $alta->mac_ont],
            ],
            'STB 1' => [
                ['SN', $alta->sn_uno_stb],
                ['MAC', $alta->mac_uno_stb],
            ],
            'STB 2' => [
                ['SN', $alta->sn_dos_stb],
                ['MAC', $alta->mac_dos_stb],
            ],
            'STB 3' => [
                ['SN', $alta->sn_tres_stb],
                ['MAC', $alta->mac_tres_stb],
            ],
            'AP 1' => [
                ['MAC', $alta->ap_uno_mac],
                ['SN', $alta->ap_uno_sn],
            ],
            'AP 2' => [
                ['MAC', $alta->ap_dos_mac],
                ['SN', $alta->ap_dos_sn],
            ],
            'AP 3' => [
                ['MAC', $alta->ap_tres_mac],
                ['SN', $alta->ap_tres_sn],
            ],
        ];

        $trazabilidad = [
            ['Registrada por', $alta->quien, false],
            ['Fecha y hora', $alta->cuando ? \Illuminate\Support\Carbon::parse($alta->cuando)->locale('es')->isoFormat('D MMMM YYYY · HH:mm') : null, false],
            ['Token', $alta->token, true],
        ];
    @endphp

    {{-- ================================ encabezado =============================== --}}
    <x-card>
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Orden de trabajo</p>
                <h2 class="mt-1 font-mono text-3xl font-semibold tracking-tight">OT {{ $alta->ot ?: '—' }}</h2>
                <p class="mt-1 truncate text-sm text-slate-500 dark:text-slate-400">{{ $alta->cliente ?: 'Sin cliente' }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    <x-icon name="clock" class="mr-1 inline h-3.5 w-3.5 align-[-2px]" />
                    @if ($alta->calendario)
                        {{ \Illuminate\Support\Carbon::parse($alta->calendario)->locale('es')->isoFormat('D MMMM YYYY') }}
                    @else
                        Sin fecha de calendario
                    @endif
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-badge tone="brand">{{ $alta->zona ?: 'Sin zona' }}</x-badge>
                <x-badge tone="sky">{{ $alta->deposito ?: 'Sin depósito' }}</x-badge>
                @if ($alta->completo === 'Si')
                    <x-badge tone="emerald">Completo</x-badge>
                @else
                    <x-badge tone="amber">Pendiente</x-badge>
                @endif
            </div>
        </div>
    </x-card>

    {{-- ================================== fichas ================================= --}}
    <div class="mt-4 grid gap-4 xl:grid-cols-2">

        <x-card title="Domicilio" sub="Ubicación y datos de contacto">
            <dl class="grid gap-4 sm:grid-cols-2">
                @foreach ($domicilio as [$etiqueta, $valor, $mono])
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $etiqueta }}</dt>
                        <dd class="mt-1 {{ $mono ? 'font-mono text-xs break-all' : 'text-sm' }}">{{ filled($valor) ? $valor : '—' }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-card>

        <x-card title="Técnico" sub="Responsable de la instalación">
            @if ($tecnico)
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Nombre</dt>
                        <dd class="mt-1 text-sm">{{ trim($tecnico->nombre . ' ' . $tecnico->apellido) ?: 'Sin nombre' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Handle</dt>
                        <dd class="mt-1 font-mono text-xs break-all">{{ $tecnico->tecnico ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Zona</dt>
                        <dd class="mt-1 text-sm">{{ $tecnico->zona ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tipo</dt>
                        <dd class="mt-1 text-sm">{{ $tecnico->tipo ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Estado</dt>
                        <dd class="mt-1">
                            <x-badge :tone="$tecnico->activo === 'Si' ? 'emerald' : 'slate'">
                                {{ $tecnico->activo === 'Si' ? 'Activo' : 'Inactivo' }}
                            </x-badge>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Teléfono</dt>
                        <dd class="mt-1 text-sm">{{ $tecnico->tel ?: '—' }}</dd>
                    </div>
                </dl>

                <a href="{{ route('tecnicos.show', $tecnico) }}" class="btn-secondary btn-sm mt-5">
                    Ver ficha del técnico <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                </a>
            @else
                <x-empty icon="users" title="Sin ficha de técnico asociada"
                         :sub="'No se encontró ningún técnico con el handle «' . ($alta->tecnico ?: '—') . '».'" />
            @endif
        </x-card>

        <x-card title="Equipos" sub="ONT, decodificadores (STB) y access points (AP)">
            <div class="space-y-5">
                @foreach ($gruposEquipos as $nombre => $campos)
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $nombre }}</p>
                        <dl class="mt-2 grid grid-cols-2 gap-3">
                            @foreach ($campos as [$etiqueta, $valor])
                                <div>
                                    <dt class="text-[10px] uppercase tracking-wide text-slate-400 dark:text-slate-500">{{ $etiqueta }}</dt>
                                    <dd class="mt-0.5 font-mono text-xs break-all {{ filled($valor) ? '' : 'text-slate-400 dark:text-slate-600' }}">
                                        {{ filled($valor) ? $valor : '—' }}
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endforeach
            </div>
        </x-card>

        <x-card title="Trazabilidad" sub="Origen del registro">
            <dl class="space-y-4">
                @foreach ($trazabilidad as [$etiqueta, $valor, $mono])
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $etiqueta }}</dt>
                        <dd class="mt-1 {{ $mono ? 'font-mono text-xs break-all' : 'text-sm' }}">{{ filled($valor) ? $valor : '—' }}</dd>
                    </div>
                @endforeach
                <div>
                    <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Identificador</dt>
                    <dd class="mt-1 font-mono text-xs break-all">#{{ $alta->id }}</dd>
                </div>
            </dl>
        </x-card>
    </div>

    {{-- ============================== entregas de material ============================== --}}
    <x-card class="mt-4" title="Entregas de material"
            sub="Últimos movimientos del técnico (hasta 10)" :pad="false">
        @if ($entregas->isEmpty())
            <x-empty icon="cube" title="Sin entregas registradas"
                     sub="El técnico no tiene movimientos de material cargados." />
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Centro</th>
                            <th>Material</th>
                            <th class="text-right">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($entregas as $entrega)
                            <tr>
                                <td class="tnum whitespace-nowrap text-slate-500 dark:text-slate-400">
                                    {{ $entrega->fechaegre ? \Illuminate\Support\Carbon::parse($entrega->fechaegre)->locale('es')->isoFormat('D MMM YYYY') : '—' }}
                                </td>
                                <td>{{ $entrega->centro ?: '—' }}</td>
                                <td>{{ $entrega->material ?: '—' }}</td>
                                <td class="tnum text-right">{{ number_format((int) $entrega->cantidad, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>

    <div class="mt-4">
        <a href="{{ route('altas.index') }}" class="btn-secondary">
            <x-icon name="chevron-left" class="h-4 w-4" /> Volver
        </a>
    </div>
@endsection
