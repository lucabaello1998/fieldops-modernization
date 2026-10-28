@extends('layouts.app')

@section('titulo', 'Buscar')
@section('subtitulo', 'Búsqueda global sobre tareas, altas, técnicos, materiales y reclamos')

@php
    /**
     * Resalta el termino buscado.
     *
     * SEGURIDAD: se escapa el HTML con e() ANTES de insertar el <mark>, de modo
     * que el resultado es seguro y se puede imprimir con {!! !!}. Nunca se
     * concatena el termino crudo.
     */
    $resaltar = function (?string $texto) use ($q) {
        $texto = (string) $texto;

        if ($q === '') {
            return e($texto);
        }

        $seguro = e($texto); // primero se neutraliza cualquier HTML de la base
        $aguja = preg_quote(e($q), '/');

        return preg_replace(
            '/(' . $aguja . ')/iu',
            '<mark class="rounded bg-amber-200/60 px-0.5 text-slate-900 dark:bg-amber-400/30 dark:text-amber-100">$1</mark>',
            $seguro
        );
    };
@endphp

@section('contenido')
    {{-- ============================== encabezado ============================== --}}
    <x-card>
        <form method="GET" action="{{ route('buscar') }}" class="flex flex-wrap items-end gap-3">
            <div class="min-w-[16rem] flex-1">
                <label for="q" class="label">Término de búsqueda</label>
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input id="q" name="q" type="search" value="{{ $q }}" minlength="2" autofocus
                           placeholder="OT, cliente, técnico, material, reclamo..." class="input pl-9">
                </div>
            </div>
            <button type="submit" class="btn-primary">
                <x-icon name="search" class="h-4 w-4" /> Buscar
            </button>
        </form>

        @if ($suficiente)
            <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
                {{ number_format($total, 0, ',', '.') }} resultado(s) para
                <span class="font-medium text-slate-900 dark:text-slate-100">«{{ $q }}»</span>
                <span class="text-slate-400"> · máximo 8 por grupo</span>
            </p>
        @endif
    </x-card>

    @if (! $suficiente)
        {{-- ============================ estado inicial ========================== --}}
        <x-card class="mt-4">
            <x-empty icon="search"
                     title="Escribí al menos 2 caracteres"
                     sub="La búsqueda es parametrizada: el término viaja como parámetro enlazado y nunca se concatena en SQL." />
        </x-card>
    @elseif ($total === 0)
        <x-card class="mt-4">
            <x-empty icon="search" title="Sin resultados"
                     sub="No encontramos coincidencias para «{{ $q }}». Probá con otro término." />
        </x-card>
    @else
        <div class="mt-4 grid gap-4 xl:grid-cols-2">

            {{-- ================================ tareas ============================= --}}
            @if ($tareas->isNotEmpty())
                <x-card title="Tareas" :sub="$tareas->count() . ' coincidencia(s)'" :pad="false">
                    <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($tareas as $t)
                            <li>
                                <a href="{{ route('tareas.index') }}"
                                   class="flex items-start gap-3 px-5 py-3 transition hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                    <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                                        <x-icon name="clipboard" class="h-4 w-4" />
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-medium text-slate-900 dark:text-slate-100">{!! $resaltar($t->titulo) !!}</span>
                                        <span class="block truncate text-xs text-slate-500 dark:text-slate-400">
                                            {!! $resaltar($t->a_quien) !!} · {{ $t->estado ?: 'Sin estado' }}
                                        </span>
                                    </span>
                                    <x-icon name="chevron-right" class="mt-1 h-4 w-4 shrink-0 text-slate-300 dark:text-slate-600" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-card>
            @endif

            {{-- ================================= altas ============================= --}}
            @if ($altas->isNotEmpty())
                <x-card title="Altas" :sub="$altas->count() . ' coincidencia(s)'" :pad="false">
                    <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($altas as $a)
                            <li>
                                <a href="{{ route('altas.show', $a->id) }}"
                                   class="flex items-start gap-3 px-5 py-3 transition hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                    <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400">
                                        <x-icon name="signal" class="h-4 w-4" />
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-medium text-slate-900 dark:text-slate-100">{!! $resaltar($a->cliente) !!}</span>
                                        <span class="block truncate text-xs text-slate-500 dark:text-slate-400">
                                            OT <span class="font-mono">{!! $resaltar($a->ot !== null ? (string) $a->ot : '') !!}</span>
                                            @if ($a->nim) · NIM <span class="font-mono">{!! $resaltar($a->nim) !!}</span>@endif
                                            @if ($a->direccion) · {!! $resaltar($a->direccion) !!}@endif
                                        </span>
                                    </span>
                                    <x-icon name="chevron-right" class="mt-1 h-4 w-4 shrink-0 text-slate-300 dark:text-slate-600" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-card>
            @endif

            {{-- =============================== técnicos =========================== --}}
            @if ($tecnicos->isNotEmpty())
                <x-card title="Técnicos" :sub="$tecnicos->count() . ' coincidencia(s)'" :pad="false">
                    <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($tecnicos as $t)
                            <li>
                                <a href="{{ route('tecnicos.show', $t->id) }}"
                                   class="flex items-start gap-3 px-5 py-3 transition hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                    <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                        <x-icon name="users" class="h-4 w-4" />
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-medium text-slate-900 dark:text-slate-100">
                                            {!! $resaltar(trim(($t->apellido ?? '') . ', ' . ($t->nombre ?? ''), ', ')) !!}
                                        </span>
                                        <span class="block truncate text-xs text-slate-500 dark:text-slate-400">
                                            {!! $resaltar($t->tecnico) !!}
                                            @if ($t->dni) · DNI <span class="font-mono">{!! $resaltar((string) $t->dni) !!}</span>@endif
                                            @if ($t->zona) · {{ $t->zona }}@endif
                                        </span>
                                    </span>
                                    <x-icon name="chevron-right" class="mt-1 h-4 w-4 shrink-0 text-slate-300 dark:text-slate-600" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-card>
            @endif

            {{-- ============================== materiales ========================== --}}
            @if ($materiales->isNotEmpty())
                <x-card title="Materiales" :sub="$materiales->count() . ' coincidencia(s)'" :pad="false">
                    <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($materiales as $m)
                            <li>
                                <a href="{{ route('stock.index') }}"
                                   class="flex items-start gap-3 px-5 py-3 transition hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                    <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                                        <x-icon name="cube" class="h-4 w-4" />
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-medium text-slate-900 dark:text-slate-100">{!! $resaltar($m->material) !!}</span>
                                        <span class="block truncate text-xs text-slate-500 dark:text-slate-400">
                                            Depósito: {!! $resaltar($m->centro) !!} · <span class="tnum">{{ number_format((int) $m->cantidad, 0, ',', '.') }}</span> u.
                                        </span>
                                    </span>
                                    <x-icon name="chevron-right" class="mt-1 h-4 w-4 shrink-0 text-slate-300 dark:text-slate-600" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-card>
            @endif

            {{-- =============================== reclamos =========================== --}}
            @if ($reclamos->isNotEmpty())
                <x-card title="Reclamos" :sub="$reclamos->count() . ' coincidencia(s)'" :pad="false">
                    <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($reclamos as $r)
                            <li>
                                <a href="{{ route('dashboard') }}"
                                   class="flex items-start gap-3 px-5 py-3 transition hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                    <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                                        <x-icon name="wrench" class="h-4 w-4" />
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-medium text-slate-900 dark:text-slate-100">{!! $resaltar($r->problema ?: $r->rf) !!}</span>
                                        <span class="block truncate text-xs text-slate-500 dark:text-slate-400">
                                            @if ($r->rf) RF <span class="font-mono">{!! $resaltar($r->rf) !!}</span> · @endif
                                            OT <span class="font-mono">{!! $resaltar($r->ot !== null ? (string) $r->ot : '') !!}</span>
                                        </span>
                                    </span>
                                    <x-icon name="chevron-right" class="mt-1 h-4 w-4 shrink-0 text-slate-300 dark:text-slate-600" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-card>
            @endif
        </div>
    @endif
@endsection
