@extends('layouts.app')

@section('titulo', 'Tablero')
@section('subtitulo', 'Resumen operativo · ' . now()->locale('es')->isoFormat('MMMM YYYY'))

@section('contenido')
    {{-- ================================== KPIs ================================== --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-kpi label="Altas del mes" :value="number_format($kpis['altas'], 0, ',', '.')" :delta="$kpis['altasDelta']"
               icon="signal" tone="brand" :hint="'mes anterior: ' . number_format($kpis['altasPrevio'], 0, ',', '.')" />
        <x-kpi label="Tareas activas" :value="number_format($kpis['tareas'], 0, ',', '.')"
               icon="clipboard" tone="sky" :hint="$kpis['enRevision'] . ' en revision'" />
        <x-kpi label="Stock critico" :value="number_format($kpis['stockCritico'], 0, ',', '.')"
               icon="cube" tone="rose" :hint="'de ' . number_format($kpis['materiales'], 0, ',', '.') . ' lineas'" />
        <x-kpi label="Tecnicos activos" :value="number_format($kpis['tecnicos'], 0, ',', '.')"
               icon="users" tone="emerald" :hint="$kpis['zonas'] . ' zonas'" />
    </div>

    <div class="mt-4 grid gap-4 sm:grid-cols-3">
        <x-kpi label="Reclamos abiertos" :value="number_format($kpis['reclamos'], 0, ',', '.')" icon="wrench" tone="amber" />
        <x-kpi label="Ordenes sin cerrar (30d)" :value="number_format($kpis['sinCerrar'], 0, ',', '.')" icon="clock" tone="sky" />
        <x-kpi label="No conformidades (90d)" :value="number_format($kpis['noConformidades'], 0, ',', '.')" icon="alert" tone="rose" />
    </div>

    {{-- ================================ graficos =============================== --}}
    <div class="mt-6 grid gap-4 xl:grid-cols-3">
        <x-card class="xl:col-span-2" title="Producción mensual"
                sub="Instalaciones, mantenimiento y bajas ejecutadas por los tecnicos">
            <div class="h-72"><canvas id="chartProduccion"></canvas></div>
        </x-card>

        <x-card title="Tareas por estado" sub="Tablero Kanban">
            <div class="h-72"><canvas id="chartTareas"></canvas></div>
        </x-card>
    </div>

    <div class="mt-4 grid gap-4 xl:grid-cols-3">
        <x-card class="xl:col-span-2" title="Altas vs bajas" sub="Altas y bajas registradas por mes">
            <div class="h-64"><canvas id="chartAltasBajas"></canvas></div>
        </x-card>

        <x-card title="Stock por depósito" sub="Unidades disponibles">
            <div class="h-64"><canvas id="chartStock"></canvas></div>
        </x-card>
    </div>

    {{-- ================================ tablas =============================== --}}
    <div class="mt-4 grid gap-4 xl:grid-cols-2">
        <x-card title="Top técnicos" sub="Ultimos 30 dias" :pad="false">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Técnico</th>
                            <th class="text-right">Instalaciones</th>
                            <th class="text-right">Mantenimiento</th>
                            <th class="text-right">Bajas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topTecnicos as $t)
                            <tr>
                                <td class="font-medium">{{ $t->tecnico }}</td>
                                <td class="tnum text-right">{{ number_format((int) $t->instalaciones, 0, ',', '.') }}</td>
                                <td class="tnum text-right">{{ number_format((int) $t->mtto, 0, ',', '.') }}</td>
                                <td class="tnum text-right text-slate-500">{{ number_format((int) $t->bajas, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-slate-500">Sin producción en los últimos 30 días.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card title="Últimas altas" sub="Instalaciones cerradas recientemente" :pad="false">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>OT</th>
                            <th>Cliente</th>
                            <th>Técnico</th>
                            <th class="text-right">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ultimasAltas as $a)
                            <tr>
                                <td class="font-mono text-xs">{{ $a->ot }}</td>
                                <td class="max-w-[14rem] truncate">{{ $a->cliente }}</td>
                                <td class="text-slate-500">{{ $a->tecnico }}</td>
                                <td class="tnum whitespace-nowrap text-right text-slate-500">
                                    {{ \Illuminate\Support\Carbon::parse($a->cuando)->locale('es')->isoFormat('D MMM HH:mm') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-slate-500">Sin altas registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    <script type="module">
        const etiquetas = @json($etiquetas);
        const series = @json($series);
        const tareas = @json($tareasPorEstado);
        const stock = @json($stockPorDeposito);

        const gris = document.documentElement.classList.contains('dark') ? '#475569' : '#94a3b8';
        const linea = document.documentElement.classList.contains('dark') ? 'rgba(148,163,184,0.12)' : 'rgba(100,116,139,0.12)';

        Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
        Chart.defaults.font.size = 11;
        Chart.defaults.color = gris;
        Chart.defaults.borderColor = linea;
        Chart.defaults.plugins.legend.labels.usePointStyle = true;
        Chart.defaults.plugins.legend.labels.boxWidth = 8;
        Chart.defaults.plugins.legend.labels.padding = 14;
        Chart.defaults.plugins.tooltip.padding = 10;
        Chart.defaults.plugins.tooltip.cornerRadius = 8;
        Chart.defaults.plugins.tooltip.titleFont = { weight: '600' };
        Chart.defaults.maintainAspectRatio = false;

        const ejeY = { beginAtZero: true, border: { display: false }, grid: { color: linea, drawTicks: false }, ticks: { padding: 8, maxTicksLimit: 5 } };
        const ejeX = { border: { display: false }, grid: { display: false }, ticks: { padding: 6 } };

        const area = (ctx, color) => {
            const g = ctx.createLinearGradient(0, 0, 0, 260);
            g.addColorStop(0, color.replace('rgb', 'rgba').replace(')', ',0.28)'));
            g.addColorStop(1, color.replace('rgb', 'rgba').replace(')', ',0.02)'));
            return g;
        };

        new Chart(document.getElementById('chartProduccion'), {
            type: 'line',
            data: {
                labels: etiquetas,
                datasets: [
                    { label: 'Instalaciones', data: series.instalaciones, borderColor: 'rgb(79,70,229)', backgroundColor: (c) => area(c.chart.ctx, 'rgb(79,70,229)'), fill: true, tension: 0.4, borderWidth: 2, pointRadius: 0, pointHoverRadius: 4 },
                    { label: 'Mantenimiento', data: series.mtto, borderColor: 'rgb(14,165,233)', backgroundColor: 'transparent', fill: false, tension: 0.4, borderWidth: 2, pointRadius: 0, pointHoverRadius: 4, borderDash: [4, 3] },
                    { label: 'Bajas', data: series.bajasProd, borderColor: 'rgb(244,63,94)', backgroundColor: 'transparent', fill: false, tension: 0.4, borderWidth: 2, pointRadius: 0, pointHoverRadius: 4 },
                ],
            },
            options: { scales: { y: ejeY, x: ejeX }, interaction: { mode: 'index', intersect: false } },
        });

        new Chart(document.getElementById('chartTareas'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(tareas),
                datasets: [{
                    data: Object.values(tareas),
                    backgroundColor: ['#94a3b8', '#4f46e5', '#0ea5e9', '#10b981'],
                    borderWidth: 0,
                    hoverOffset: 6,
                }],
            },
            options: {
                cutout: '68%',
                plugins: { legend: { position: 'bottom' } },
                layout: { padding: 4 },
            },
        });

        new Chart(document.getElementById('chartAltasBajas'), {
            type: 'bar',
            data: {
                labels: etiquetas,
                datasets: [
                    { label: 'Altas', data: series.altas, backgroundColor: 'rgba(79,70,229,0.85)', borderRadius: 5, maxBarThickness: 18 },
                    { label: 'Bajas', data: series.bajas, backgroundColor: 'rgba(244,63,94,0.75)', borderRadius: 5, maxBarThickness: 18 },
                ],
            },
            options: {
                scales: { y: ejeY, x: { ...ejeX, stacked: false, grid: { display: false } } },
                plugins: { legend: { position: 'bottom' } },
            },
        });

        new Chart(document.getElementById('chartStock'), {
            type: 'bar',
            data: {
                labels: Object.keys(stock),
                datasets: [{
                    label: 'Unidades',
                    data: Object.values(stock),
                    backgroundColor: 'rgba(16,185,129,0.8)',
                    borderRadius: 5,
                    maxBarThickness: 14,
                }],
            },
            options: {
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, border: { display: false }, grid: { color: linea, drawTicks: false }, ticks: { padding: 6, maxTicksLimit: 5 } },
                    y: { border: { display: false }, grid: { display: false } },
                },
            },
        });
    </script>
@endsection
