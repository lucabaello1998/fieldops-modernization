@extends('layouts.app')

@section('titulo', 'Stock')
@section('subtitulo', 'Materiales por deposito y movimientos')

@section('contenido')
    @php
        // Umbrales del modulo (mismos que usa el controlador).
        $minimo = \App\Http\Controllers\StockController::STOCK_MINIMO;
        $atencion = \App\Http\Controllers\StockController::STOCK_ATENCION;
    @endphp

    {{-- ================================ filtros ================================ --}}
    <x-card title="Filtros" sub="Acota el listado por material, deposito y nivel de stock">
        <form method="GET" action="{{ route('stock.index') }}"
              class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div>
                <label for="q" class="label">Material</label>
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input id="q" name="q" type="search" value="{{ $filtros['q'] }}"
                           placeholder="Buscar material..." class="input pl-9">
                </div>
            </div>

            <div>
                <label for="centro" class="label">Depósito</label>
                <select id="centro" name="centro" class="select">
                    <option value="">Todos</option>
                    @foreach ($depositos as $deposito)
                        <option value="{{ $deposito }}" @selected($filtros['centro'] === $deposito)>{{ $deposito }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="nivel" class="label">Nivel de stock</label>
                <select id="nivel" name="nivel" class="select">
                    <option value="">Todos</option>
                    <option value="critico" @selected($filtros['nivel'] === 'critico')>Crítico (&lt; {{ $minimo }} u.)</option>
                    <option value="atencion" @selected($filtros['nivel'] === 'atencion')>Atención (&lt; {{ $atencion }} u.)</option>
                    <option value="ok" @selected($filtros['nivel'] === 'ok')>OK (≥ {{ $atencion }} u.)</option>
                </select>
            </div>

            <div>
                <label for="orden" class="label">Orden</label>
                <select id="orden" name="orden" class="select">
                    <option value="cantidad_asc" @selected($filtros['orden'] === 'cantidad_asc')>Cantidad (menor a mayor)</option>
                    <option value="material" @selected($filtros['orden'] === 'material')>Material (A-Z)</option>
                    <option value="centro" @selected($filtros['orden'] === 'centro')>Depósito</option>
                </select>
            </div>

            <div class="flex items-center gap-2 sm:col-span-2 xl:col-span-4">
                <button type="submit" class="btn-primary">
                    <x-icon name="funnel" class="h-4 w-4" /> Filtrar
                </button>
                <a href="{{ route('stock.index') }}" class="btn-ghost">Limpiar</a>
            </div>
        </form>
    </x-card>

    {{-- ================================== KPIs ================================== --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <x-kpi label="Unidades en stock" :value="number_format($kpis['unidades'], 0, ',', '.')"
               icon="cube" tone="brand"
               :hint="number_format($kpis['lineas'], 0, ',', '.') . ' líneas'" />
        <x-kpi label="Líneas críticas" :value="number_format($kpis['criticas'], 0, ',', '.')"
               icon="alert" tone="rose" :hint="'menos de ' . $minimo . ' u.'" />
        <x-kpi label="Líneas totales" :value="number_format($kpis['lineas'], 0, ',', '.')"
               icon="clipboard" tone="sky" hint="material + deposito" />
        <x-kpi label="Depositos" :value="number_format($kpis['depositos'], 0, ',', '.')"
               icon="map-pin" tone="emerald" hint="con stock registrado" />
        <x-kpi label="Deposito más cargado" :value="number_format($kpis['unidadesTop'], 0, ',', '.')"
               icon="chart" tone="amber" :hint="$kpis['depositoTop']" />
    </div>

    {{-- ========================= tabla + actividad reciente ========================= --}}
    <div class="mt-6 grid gap-4 xl:grid-cols-3">
        <x-card class="xl:col-span-2" title="Materiales en stock"
                sub="Unidades disponibles por deposito" :pad="false">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th>Depósito</th>
                            <th class="text-right">Cantidad</th>
                            <th>Nivel</th>
                            <th class="text-right">Última fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($filas as $fila)
                            @php $cantidadFila = (int) $fila->cantidad; @endphp
                            <tr>
                                <td class="max-w-[18rem] truncate font-medium">{{ $fila->material }}</td>
                                <td><x-badge tone="slate">{{ $fila->centro ?: '—' }}</x-badge></td>
                                <td class="tnum text-right">{{ number_format($cantidadFila, 0, ',', '.') }}</td>
                                <td>
                                    @if ($cantidadFila < $minimo)
                                        <x-badge tone="rose">Crítico</x-badge>
                                    @elseif ($cantidadFila < $atencion)
                                        <x-badge tone="amber">Atención</x-badge>
                                    @else
                                        <x-badge tone="emerald">OK</x-badge>
                                    @endif
                                </td>
                                <td class="tnum whitespace-nowrap text-right text-slate-500">
                                    {{ $fila->fecha ? $fila->fecha->locale('es')->isoFormat('D MMM YYYY') : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <x-empty title="Sin materiales"
                                             sub="No hay líneas de stock que coincidan con los filtros aplicados."
                                             icon="cube" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($filas->hasPages())
                <div class="border-t border-slate-100 px-4 py-3 dark:border-slate-800">
                    {{ $filas->links() }}
                </div>
            @endif
        </x-card>

        <div class="space-y-4">
            <x-card title="Movimientos recientes" sub="Últimos 12 registros internos" :pad="false">
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($movimientos as $mov)
                        <div class="flex items-start justify-between gap-3 px-5 py-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-slate-900 dark:text-slate-100">{{ $mov->movimiento }}</p>
                                <p class="mt-0.5 truncate text-xs text-slate-500 dark:text-slate-400">
                                    {{ $mov->obs ?: 'Sin observaciones' }}
                                </p>
                            </div>
                            <div class="shrink-0 text-right">
                                <x-badge tone="slate">{{ $mov->zona ?: '—' }}</x-badge>
                                <p class="tnum mt-1 text-[11px] text-slate-400">
                                    {{ $mov->cuando ? $mov->cuando->locale('es')->isoFormat('D MMM HH:mm') : '—' }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <x-empty title="Sin movimientos" sub="Todavia no se registraron movimientos internos." icon="truck" />
                    @endforelse
                </div>
            </x-card>

            {{-- ------------------------- registrar movimiento ------------------------- --}}
            <x-card title="Registrar movimiento" sub="Alta, baja, transferencia o ajuste">
                <form method="POST" action="{{ route('stock.movimiento') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="centro-mov" class="label">Depósito</label>
                        <select id="centro-mov" name="centro" class="select" required>
                            @foreach ($depositos as $deposito)
                                <option value="{{ $deposito }}" @selected(old('centro') === $deposito)>{{ $deposito }}</option>
                            @endforeach
                        </select>
                        @error('centro') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="tipo-mov" class="label">Tipo de movimiento</label>
                        <select id="tipo-mov" name="tipo" class="select" required>
                            @foreach ($tipos as $tipo)
                                <option value="{{ $tipo }}" @selected(old('tipo') === $tipo)>{{ $tipo }}</option>
                            @endforeach
                        </select>
                        @error('tipo') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="material-mov" class="label">Material</label>
                        <input id="material-mov" name="material" type="text" list="lista-materiales"
                               value="{{ old('material') }}" required maxlength="255"
                               placeholder="Descripción del material" class="input">
                        <datalist id="lista-materiales">
                            @foreach ($materiales as $material)
                                <option value="{{ $material }}"></option>
                            @endforeach
                        </datalist>
                        @error('material') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="cantidad-mov" class="label">Cantidad</label>
                        <input id="cantidad-mov" name="cantidad" type="number" min="1" max="1000" step="1"
                               value="{{ old('cantidad', 1) }}" required class="input">
                        @error('cantidad') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="obs-mov" class="label">Observaciones</label>
                        <textarea id="obs-mov" name="obs" rows="2" maxlength="500"
                                  placeholder="Opcional" class="input">{{ old('obs') }}</textarea>
                        @error('obs') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="btn-primary w-full">
                        <x-icon name="plus" class="h-4 w-4" /> Registrar
                    </button>
                </form>
            </x-card>
        </div>
    </div>

    {{-- ================================== grafico ================================== --}}
    <div class="mt-4">
        <x-card title="Unidades por depósito" sub="Suma de unidades disponibles en cada depósito">
            <div class="h-72"><canvas id="chartUnidades"></canvas></div>
        </x-card>
    </div>

    {{-- ============================ entregas recientes ============================ --}}
    <div class="mt-4">
        <x-card title="Entregas recientes" sub="Últimas 10 salidas de material registradas" :pad="false">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th>Depósito</th>
                            <th>Técnico</th>
                            <th class="text-right">Cantidad</th>
                            <th class="text-right">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($entregasRecientes as $entrega)
                            <tr>
                                <td class="max-w-[20rem] truncate">{{ $entrega->material ?: '—' }}</td>
                                <td><x-badge tone="slate">{{ $entrega->centro ?: '—' }}</x-badge></td>
                                <td class="text-slate-500">{{ $entrega->tecnico ?: '—' }}</td>
                                <td class="tnum text-right">{{ number_format((int) $entrega->cantidad, 0, ',', '.') }}</td>
                                <td class="tnum whitespace-nowrap text-right text-slate-500">
                                    {{ $entrega->fechaegre ? $entrega->fechaegre->locale('es')->isoFormat('D MMM YYYY') : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <x-empty title="Sin entregas" sub="No hay entregas de material registradas." icon="truck" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    <script type="module">
        const porCentro = @json($porCentro);

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

        new Chart(document.getElementById('chartUnidades'), {
            type: 'bar',
            data: {
                labels: Object.keys(porCentro),
                datasets: [{
                    label: 'Unidades',
                    data: Object.values(porCentro).map((v) => Number(v)),
                    backgroundColor: 'rgba(79,70,229,0.85)',
                    borderRadius: 5,
                    maxBarThickness: 18,
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
