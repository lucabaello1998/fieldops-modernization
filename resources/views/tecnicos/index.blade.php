@extends('layouts.app')

@section('titulo', 'Tecnicos')
@section('subtitulo', 'Plantel, zonas y desempeno')

@section('contenido')
    {{-- ================================== filtros ================================== --}}
    <x-card title="Filtros" sub="Buscá por nombre, apellido, handle o DNI y acotá por zona, tipo, estado o depósito">
        <form method="GET" action="{{ route('tecnicos.index') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="sm:col-span-2">
                <label class="label" for="q">Búsqueda</label>
                <input id="q" name="q" type="search" value="{{ request('q') }}" class="input"
                       placeholder="Nombre, apellido, handle o DNI">
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
                <label class="label" for="tipo">Tipo</label>
                <select id="tipo" name="tipo" class="select">
                    <option value="">Todos</option>
                    @foreach ($tipos as $tipo)
                        <option value="{{ $tipo }}" @selected(request('tipo') === $tipo)>{{ $tipo }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label" for="activo">Estado</label>
                <select id="activo" name="activo" class="select">
                    <option value="">Todos</option>
                    <option value="Si" @selected(request('activo') === 'Si')>Activos</option>
                    <option value="No" @selected(request('activo') === 'No')>Inactivos</option>
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

            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary">
                    <x-icon name="funnel" class="h-4 w-4" /> Filtrar
                </button>
                <a href="{{ route('tecnicos.index') }}" class="btn-secondary">Limpiar</a>
            </div>
        </form>
    </x-card>

    {{-- =================================== KPIs =================================== --}}
    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-kpi label="Tecnicos" :value="number_format($kpis['total'], 0, ',', '.')" icon="users" tone="brand"
               hint="plantel completo" />
        <x-kpi label="Activos" :value="number_format($kpis['activos'], 0, ',', '.')" icon="check" tone="emerald"
               :hint="number_format($kpis['total'] - $kpis['activos'], 0, ',', '.') . ' inactivos'" />
        <x-kpi label="Zonas cubiertas" :value="number_format($kpis['zonas'], 0, ',', '.')" icon="map-pin" tone="sky"
               hint="según la zona asignada" />
        <x-kpi label="Instalaciones (30d)" :value="number_format($kpis['instalaciones30'], 0, ',', '.')" icon="signal"
               tone="amber" hint="dos y tres play del plantel" />
    </div>

    {{-- ================================== plantel ================================= --}}
    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($tecnicos as $t)
            @php
                $iniciales = mb_strtoupper(mb_substr((string) $t->nombre, 0, 1) . mb_substr((string) $t->apellido, 0, 1));
                $met = $desempeno[$t->tecnico] ?? null;
            @endphp

            <a href="{{ route('tecnicos.show', $t) }}"
               class="card card-pad block transition duration-150 ease-out hover:-translate-y-px hover:shadow-md">
                <div class="flex items-start gap-3">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full text-sm font-semibold text-white {{ $t->activo === 'Si' ? 'bg-brand-600' : 'bg-slate-200 dark:bg-slate-700' }}">
                        {{ $iniciales ?: '—' }}
                    </span>

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold tracking-tight text-slate-900 dark:text-slate-100">
                            {{ trim($t->nombre . ' ' . $t->apellido) ?: 'Sin nombre' }}
                        </p>
                        <p class="truncate font-mono text-xs text-slate-500 dark:text-slate-400">{{ $t->tecnico ?: '—' }}</p>
                    </div>

                    <x-badge :tone="$t->activo === 'Si' ? 'emerald' : 'slate'">
                        {{ $t->activo === 'Si' ? 'Activo' : 'Inactivo' }}
                    </x-badge>
                </div>

                <div class="mt-3 flex flex-wrap gap-1.5">
                    <x-badge tone="brand">{{ $t->zona ?: 'Sin zona' }}</x-badge>
                    <x-badge tone="sky">{{ $t->tipo ?: 'Sin tipo' }}</x-badge>
                    <x-badge tone="slate">{{ $t->deposito ?: 'Sin depósito' }}</x-badge>
                </div>

                <div class="mt-4 grid grid-cols-3 gap-2 border-t border-slate-100 pt-3 dark:border-slate-800">
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Instal.</p>
                        <p class="tnum mt-0.5 text-sm font-semibold">{{ number_format((int) ($met->instalaciones ?? 0), 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Mtto.</p>
                        <p class="tnum mt-0.5 text-sm font-semibold">{{ number_format((int) ($met->mantenimiento ?? 0), 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Bajas</p>
                        <p class="tnum mt-0.5 text-sm font-semibold text-slate-500 dark:text-slate-400">{{ number_format((int) ($met->bajas ?? 0), 0, ',', '.') }}</p>
                    </div>
                </div>
            </a>
        @empty
            <div class="sm:col-span-2 xl:col-span-3">
                <x-card>
                    <x-empty icon="users" title="Sin técnicos para mostrar"
                             sub="Probá ajustar la búsqueda o limpiar los filtros.">
                        <a href="{{ route('tecnicos.index') }}" class="btn-secondary">Limpiar filtros</a>
                    </x-empty>
                </x-card>
            </div>
        @endforelse
    </div>

    @if ($tecnicos->hasPages())
        <div class="mt-4">
            {{ $tecnicos->links() }}
        </div>
    @endif
@endsection
