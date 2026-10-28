@extends('layouts.app')

@section('titulo', trim($tecnico->nombre . ' ' . $tecnico->apellido) ?: ($tecnico->tecnico ?: 'Tecnico'))
@section('subtitulo', 'Ficha del técnico · ' . ($tecnico->tecnico ?: 'sin handle'))

@section('contenido')
    @php
        $iniciales = mb_strtoupper(mb_substr((string) $tecnico->nombre, 0, 1) . mb_substr((string) $tecnico->apellido, 0, 1));

        // Metricas de las 10 jornadas cargadas en la ficha.
        $instalaciones = $produccion->sum(fn ($p) => (int) $p->dosplay + (int) $p->tresplay);
        $mantenimiento = $produccion->sum(fn ($p) => (int) $p->mtto_int + (int) $p->mtto_ext);
        $bajas = $produccion->sum(fn ($p) => (int) $p->bajas + (int) $p->baja_desmonte);

        // Semáforo de vencimientos del vehículo.
        $toneVencimiento = function ($fecha) {
            if (! $fecha) {
                return 'slate';
            }

            return $fecha->isPast() ? 'rose' : ($fecha->diffInDays(now()) <= 30 ? 'amber' : 'emerald');
        };

        $contacto = [
            ['DNI', $tecnico->dni, true],
            ['Correo', $tecnico->mail, false],
            ['Teléfono', $tecnico->tel, false],
            ['Ingreso', $tecnico->ingreso ? \Illuminate\Support\Carbon::parse($tecnico->ingreso)->locale('es')->isoFormat('D MMMM YYYY') : null, false],
            ['Recurso', $tecnico->id_recurso, true],
            ['Modelo', $tecnico->modelo, false],
            ['Serie', $tecnico->sn, true],
            ['Flota', $tecnico->flota, false],
            ['Chomba', $tecnico->chomba, false],
            ['Pantalón', $tecnico->pantalon, false],
            ['Zapato', $tecnico->zapato, false],
        ];
    @endphp

    {{-- ================================ encabezado =============================== --}}
    <x-card>
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex min-w-0 items-start gap-4">
                <span class="grid h-14 w-14 shrink-0 place-items-center rounded-full text-lg font-semibold text-white {{ $tecnico->activo === 'Si' ? 'bg-brand-600' : 'bg-slate-200 dark:bg-slate-700' }}">
                    {{ $iniciales ?: '—' }}
                </span>

                <div class="min-w-0">
                    <h2 class="truncate text-xl font-semibold tracking-tight">
                        {{ trim($tecnico->nombre . ' ' . $tecnico->apellido) ?: 'Sin nombre' }}
                    </h2>
                    <p class="mt-0.5 font-mono text-xs text-slate-500 dark:text-slate-400">{{ $tecnico->tecnico ?: '—' }}</p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        <x-icon name="map-pin" class="mr-1 inline h-3.5 w-3.5 align-[-2px]" />
                        {{ $tecnico->zona ?: 'Sin zona' }} · {{ $tecnico->deposito ?: 'Sin depósito' }}
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-badge tone="sky">{{ $tecnico->tipo ?: 'Sin tipo' }}</x-badge>
                <x-badge :tone="$tecnico->activo === 'Si' ? 'emerald' : 'slate'">
                    {{ $tecnico->activo === 'Si' ? 'Activo' : 'Inactivo' }}
                </x-badge>
            </div>
        </div>
    </x-card>

    {{-- =================================== KPIs =================================== --}}
    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-kpi label="Altas asociadas" :value="number_format($altas, 0, ',', '.')" icon="signal" tone="brand"
               hint="por handle del técnico" />
        <x-kpi label="Instalaciones" :value="number_format($instalaciones, 0, ',', '.')" icon="check" tone="emerald"
               hint="últimas 10 jornadas" />
        <x-kpi label="Mantenimiento" :value="number_format($mantenimiento, 0, ',', '.')" icon="wrench" tone="sky"
               hint="internos y externos" />
        <x-kpi label="Bajas" :value="number_format($bajas, 0, ',', '.')" icon="trend-down" tone="rose"
               hint="bajas y desmontes" />
    </div>

    {{-- ============================ contacto y vehículo =========================== --}}
    <div class="mt-4 grid gap-4 xl:grid-cols-2">
        <x-card title="Contacto y equipamiento" sub="Datos del legajo">
            <dl class="grid gap-4 sm:grid-cols-2">
                @foreach ($contacto as [$etiqueta, $valor, $mono])
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $etiqueta }}</dt>
                        <dd class="mt-1 {{ $mono ? 'font-mono text-xs break-all' : 'text-sm' }}">{{ filled($valor) ? $valor : '—' }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-card>

        <x-card title="Vehículo" sub="Unidad asignada y vencimientos">
            @if ($vehiculo)
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Patente</dt>
                        <dd class="mt-1 font-mono text-xs break-all">{{ $vehiculo->patente ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Vehículo</dt>
                        <dd class="mt-1 text-sm">{{ $vehiculo->auto ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Color</dt>
                        <dd class="mt-1 text-sm">{{ $vehiculo->color ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Estado</dt>
                        <dd class="mt-1">
                            <x-badge :tone="$vehiculo->vigente === 'Si' ? 'emerald' : 'slate'">
                                {{ $vehiculo->vigente === 'Si' ? 'Vigente' : 'Fuera de servicio' }}
                            </x-badge>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">VTV</dt>
                        <dd class="mt-1 flex flex-wrap items-center gap-2">
                            <span class="tnum text-sm">{{ $vehiculo->vtv ? $vehiculo->vtv->format('d/m/Y') : '—' }}</span>
                            <x-badge :tone="$toneVencimiento($vehiculo->vtv)">
                                {{ $vehiculo->vtv ? ($vehiculo->vtv->isPast() ? 'Vencida' : 'Al día') : 'Sin dato' }}
                            </x-badge>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Seguro</dt>
                        <dd class="mt-1 flex flex-wrap items-center gap-2">
                            <span class="tnum text-sm">{{ $vehiculo->seguro ? $vehiculo->seguro->format('d/m/Y') : '—' }}</span>
                            <x-badge :tone="$toneVencimiento($vehiculo->seguro)">
                                {{ $vehiculo->seguro ? ($vehiculo->seguro->isPast() ? 'Vencido' : 'Al día') : 'Sin dato' }}
                            </x-badge>
                        </dd>
                    </div>
                </dl>

                <p class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                    Seguro propio:
                    <span class="font-medium">{{ $vehiculo->vigenteseg === 'Si' ? 'sí' : 'no' }}</span>
                </p>
            @else
                <x-empty icon="truck" title="Sin vehículo asociado"
                         :sub="$tecnico->patente
                             ? 'No se encontró la patente «' . $tecnico->patente . '» en la flota.'
                             : 'El técnico no tiene patente cargada.'" />
            @endif
        </x-card>
    </div>

    {{-- ============================== producción reciente ============================== --}}
    <x-card class="mt-4" title="Producción reciente"
            sub="Últimas 10 jornadas registradas" :pad="false">
        @if ($produccion->isEmpty())
            <x-empty icon="clipboard" title="Sin producción registrada"
                     sub="El técnico no tiene jornadas cargadas en la tabla de producción." />
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Zona</th>
                            <th class="text-right">2P</th>
                            <th class="text-right">3P</th>
                            <th class="text-right">STB</th>
                            <th class="text-right">Mtto int</th>
                            <th class="text-right">Mtto ext</th>
                            <th class="text-right">Bajas</th>
                            <th>Horario</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($produccion as $p)
                            <tr>
                                <td class="tnum whitespace-nowrap">
                                    {{ $p->fecha ? \Illuminate\Support\Carbon::parse($p->fecha)->locale('es')->isoFormat('D MMM YYYY') : '—' }}
                                </td>
                                <td class="text-slate-500 dark:text-slate-400">{{ $p->zona ?: '—' }}</td>
                                <td class="tnum text-right">{{ number_format((int) $p->dosplay, 0, ',', '.') }}</td>
                                <td class="tnum text-right">{{ number_format((int) $p->tresplay, 0, ',', '.') }}</td>
                                <td class="tnum text-right">{{ number_format((int) $p->stb, 0, ',', '.') }}</td>
                                <td class="tnum text-right">{{ number_format((int) $p->mtto_int, 0, ',', '.') }}</td>
                                <td class="tnum text-right">{{ number_format((int) $p->mtto_ext, 0, ',', '.') }}</td>
                                <td class="tnum text-right text-slate-500 dark:text-slate-400">{{ number_format((int) $p->bajas, 0, ',', '.') }}</td>
                                <td class="tnum whitespace-nowrap font-mono text-xs text-slate-500 dark:text-slate-400">
                                    {{ $p->horadep ?: '—' }} → {{ $p->fin ?: '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>

    {{-- ============================== entregas de material ============================== --}}
    <x-card class="mt-4" title="Entregas de material"
            sub="Últimos 10 movimientos del depósito" :pad="false">
        @if ($entregas->isEmpty())
            <x-empty icon="cube" title="Sin entregas registradas"
                     sub="No hay movimientos de material para este técnico." />
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
        <a href="{{ route('tecnicos.index') }}" class="btn-secondary">
            <x-icon name="chevron-left" class="h-4 w-4" /> Volver
        </a>
    </div>
@endsection
