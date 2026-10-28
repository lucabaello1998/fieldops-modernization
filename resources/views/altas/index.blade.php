@extends('layouts.app')

@section('titulo', 'Altas')
@section('subtitulo', 'Instalaciones registradas')

@section('contenido')
    {{-- ================================== filtros ================================== --}}
    <x-card title="Filtros" sub="Buscá por OT, cliente, NIM o dirección y acotá por zona, depósito, estado o fecha">
        <form method="GET" action="{{ route('altas.index') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="sm:col-span-2">
                <label class="label" for="q">Búsqueda</label>
                <input id="q" name="q" type="search" value="{{ request('q') }}" class="input"
                       placeholder="OT, cliente, NIM o dirección">
            </div>

            <div>
                <label class="label" for="zona">Zona</label>
                <select id="zona" name="zona" class="select">
                    <option value="">Todas</option>
                    @foreach ($zonas as $zona)
                        <option value="{{ $zona }}" @selected(request('zona') === $zona)>{{ $zona }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label" for="deposito">Depósito</label>
                <select id="deposito" name="deposito" class="select">
                    <option value="">Todos</option>
                    @foreach ($depositos as $deposito)
                        <option value="{{ $deposito }}" @selected(request('deposito') === $deposito)>{{ $deposito }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label" for="completo">Estado</label>
                <select id="completo" name="completo" class="select">
                    <option value="">Todos</option>
                    <option value="Si" @selected(request('completo') === 'Si')>Completo</option>
                    <option value="No" @selected(request('completo') === 'No')>Pendiente</option>
                </select>
            </div>

            <div>
                <label class="label" for="desde">Desde</label>
                <input id="desde" name="desde" type="date" value="{{ request('desde') }}" class="input">
            </div>

            <div>
                <label class="label" for="hasta">Hasta</label>
                <input id="hasta" name="hasta" type="date" value="{{ request('hasta') }}" class="input">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary">
                    <x-icon name="funnel" class="h-4 w-4" /> Filtrar
                </button>
                <a href="{{ route('altas.index') }}" class="btn-secondary">Limpiar</a>
            </div>
        </form>
    </x-card>

    {{-- =================================== KPIs =================================== --}}
    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-kpi label="Altas del filtro" :value="number_format($kpis['total'], 0, ',', '.')" icon="signal" tone="brand"
               :hint="$kpis['zonas'] . ' zonas distintas'" />
        <x-kpi label="Altas del mes" :value="number_format($kpis['mesActual'], 0, ',', '.')" icon="chart" tone="sky"
               :hint="now()->locale('es')->isoFormat('MMMM YYYY')" />
        <x-kpi label="Instalaciones completas" :value="number_format($kpis['pctCompletas'], 1, ',', '.') . '%'"
               icon="check" tone="emerald"
               :hint="number_format($kpis['completas'], 0, ',', '.') . ' de ' . number_format($kpis['total'], 0, ',', '.')" />
        <x-kpi label="Zonas alcanzadas" :value="number_format($kpis['zonas'], 0, ',', '.')" icon="map-pin" tone="amber"
               hint="según los filtros aplicados" />
    </div>

    {{-- ================================== listado ================================= --}}
    <x-card class="mt-4" title="Listado de altas"
            :sub="number_format($kpis['total'], 0, ',', '.') . ' registros · página ' . $altas->currentPage() . ' de ' . $altas->lastPage()"
            :pad="false">
        @if ($altas->isEmpty())
            <x-empty title="Sin altas para mostrar"
                     sub="Probá ajustar la búsqueda, el rango de fechas o limpiar los filtros.">
                <a href="{{ route('altas.index') }}" class="btn-secondary">Limpiar filtros</a>
            </x-empty>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>OT</th>
                            <th>Cliente</th>
                            <th>Localidad</th>
                            <th>Zona</th>
                            <th>Técnico</th>
                            <th>Depósito</th>
                            <th>Fecha</th>
                            <th>Completo</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($altas as $a)
                            <tr class="cursor-pointer" onclick="window.location='{{ route('altas.show', $a) }}'">
                                <td class="font-mono text-xs">{{ $a->ot ?: '—' }}</td>
                                <td class="max-w-[16rem] truncate">{{ $a->cliente ?: '—' }}</td>
                                <td class="text-slate-500 dark:text-slate-400">{{ $a->localidad ?: '—' }}</td>
                                <td>
                                    <x-badge tone="slate">{{ $a->zona ?: 'sin zona' }}</x-badge>
                                </td>
                                <td class="font-mono text-xs text-slate-500 dark:text-slate-400">{{ $a->tecnico ?: '—' }}</td>
                                <td class="text-slate-500 dark:text-slate-400">{{ $a->deposito ?: '—' }}</td>
                                <td class="tnum whitespace-nowrap text-slate-500 dark:text-slate-400">
                                    {{ $a->calendario ? \Illuminate\Support\Carbon::parse($a->calendario)->locale('es')->isoFormat('D MMM YYYY') : '—' }}
                                </td>
                                <td>
                                    @if ($a->completo === 'Si')
                                        <x-badge tone="emerald">Completo</x-badge>
                                    @else
                                        <x-badge tone="amber">Pendiente</x-badge>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('altas.show', $a) }}" title="Ver detalle"
                                       class="inline-flex rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-brand-600 dark:hover:bg-slate-800 dark:hover:text-brand-400">
                                        <x-icon name="chevron-right" class="h-4 w-4" />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($altas->hasPages())
                <div class="border-t border-slate-100 px-5 py-4 dark:border-slate-800">
                    {{ $altas->links() }}
                </div>
            @endif
        @endif
    </x-card>

    {{-- ============================== gráfico y zonas ============================= --}}
    <div class="mt-4 grid gap-4 xl:grid-cols-3">
        <x-card class="xl:col-span-2" title="Altas por mes"
                sub="Últimos 12 meses, según los filtros aplicados">
            <div class="h-72"><canvas id="chartAltasMes"></canvas></div>
        </x-card>

        <x-card title="Top zonas" sub="Hasta 8 zonas con más altas" :pad="false">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Zona</th>
                            <th class="text-right">Altas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($porZona as $fila)
                            <tr>
                                <td>{{ $fila->zona }}</td>
                                <td class="tnum text-right">{{ number_format((int) $fila->t, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-slate-500">Sin datos de zona para el filtro actual.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    <script type="module">
        const etiquetas = @json($etiquetas);
        const datos = @json($porMes);

        const gris = document.documentElement.classList.contains('dark') ? '#475569' : '#94a3b8';
        const linea = document.documentElement.classList.contains('dark') ? 'rgba(148,163,184,0.12)' : 'rgba(100,116,139,0.12)';

        Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
        Chart.defaults.font.size = 11;
        Chart.defaults.color = gris;
        Chart.defaults.borderColor = linea;
        Chart.defaults.plugins.tooltip.padding = 10;
        Chart.defaults.plugins.tooltip.cornerRadius = 8;
        Chart.defaults.plugins.tooltip.titleFont = { weight: '600' };
        Chart.defaults.maintainAspectRatio = false;

        new Chart(document.getElementById('chartAltasMes'), {
            type: 'bar',
            data: {
                labels: etiquetas,
                datasets: [{
                    label: 'Altas',
                    data: datos,
                    backgroundColor: 'rgba(79,70,229,0.85)',
                    borderRadius: 5,
                    maxBarThickness: 26,
                }],
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, border: { display: false }, grid: { color: linea, drawTicks: false }, ticks: { padding: 8, maxTicksLimit: 5 } },
                    x: { border: { display: false }, grid: { display: false }, ticks: { padding: 6 } },
                },
            },
        });
    </script>
@endsection
