@extends('layouts.app')

@section('titulo', 'Tareas')
@section('subtitulo', 'Tablero Kanban · arrastra las tarjetas para cambiar de estado')

@php
    // Estado interno (sin acento) => etiqueta + estilo de la columna
    $meta = [
        'Pendiente' => ['label' => 'Pendiente', 'tone' => 'slate', 'bar' => 'bg-slate-400'],
        'En curso' => ['label' => 'En curso', 'tone' => 'brand', 'bar' => 'bg-brand-500'],
        'En revision' => ['label' => 'En revision', 'tone' => 'sky', 'bar' => 'bg-sky-500'],
        'Finalizado' => ['label' => 'Finalizado', 'tone' => 'emerald', 'bar' => 'bg-emerald-500'],
    ];
    $prio = [1 => ['Alta', 'rose'], 2 => ['Media', 'amber'], 3 => ['Baja', 'slate']];
@endphp

@section('contenido')
<div x-data="{
        abierto: false,
        nueva: false,
        cargando: false,
        t: {},
        async ver(id) {
            this.abierto = true;
            this.cargando = true;
            this.t = {};
            try {
                const r = await fetch(`/tareas/${id}`, { headers: { 'Accept': 'application/json' } });
                this.t = await r.json();
            } finally {
                this.cargando = false;
            }
        }
     }">

    {{-- ================================= KPIs ================================= --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-kpi label="Tareas en el tablero" :value="number_format($kpis['total'], 0, ',', '.')" icon="clipboard" tone="brand" />
        <x-kpi label="Activas" :value="number_format($kpis['activas'], 0, ',', '.')" icon="clock" tone="sky" hint="sin finalizar" />
        <x-kpi label="Vencidas" :value="number_format($kpis['vencidas'], 0, ',', '.')" icon="alert" tone="rose" hint="fecha limite pasada" />
        <x-kpi label="Finalizadas este mes" :value="number_format($kpis['finalizadasMes'], 0, ',', '.')" icon="check" tone="emerald" />
    </div>

    {{-- ================================ filtros =============================== --}}
    <div class="card card-pad mt-6">
        <form method="GET" action="{{ route('tareas.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="min-w-[16rem] flex-1">
                <label class="label" for="q">Buscar</label>
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input id="q" name="q" value="{{ $filtros['q'] ?? '' }}" class="input pl-9" placeholder="Título, cliente o descripción">
                </div>
            </div>

            <div class="w-full sm:w-56">
                <label class="label" for="tecnico">Técnico asignado</label>
                <select id="tecnico" name="tecnico" class="select">
                    <option value="">Todos</option>
                    @foreach ($tecnicos as $t)
                        <option value="{{ $t }}" @selected(($filtros['tecnico'] ?? '') === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-40">
                <label class="label" for="prioridad">Prioridad</label>
                <select id="prioridad" name="prioridad" class="select">
                    <option value="">Todas</option>
                    @foreach ($prio as $n => $metaPrio)
                        <option value="{{ $n }}" @selected((string) ($filtros['prioridad'] ?? '') === (string) $n)>{{ $metaPrio[0] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary">
                    <x-icon name="funnel" class="h-4 w-4" /> Filtrar
                </button>
                <a href="{{ route('tareas.index') }}" class="btn-ghost">Limpiar</a>
            </div>
        </form>
    </div>

    {{-- ============================== nueva tarea ============================== --}}
    <div class="mt-4 flex items-center justify-between gap-3">
        <p class="text-xs text-slate-500 dark:text-slate-400">
            Movimiento de tarjetas registrado con <span class="font-mono">PATCH /tareas/{id}/estado</span> · sesión validada y CSRF
        </p>
        <button type="button" @click="nueva = true" class="btn-primary shrink-0">
            <x-icon name="plus" class="h-4 w-4" /> Nueva tarea
        </button>
    </div>

    {{-- ================================ tablero =============================== --}}
    <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($columnas as $estado => $items)
            @php $m = $meta[$estado]; @endphp
            <section class="flex flex-col rounded-xl border border-slate-200 bg-slate-100/50 dark:border-slate-800 dark:bg-slate-900/40">
                <header class="flex items-center justify-between gap-2 px-3.5 py-3">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full {{ $m['bar'] }}"></span>
                        <h2 class="text-sm font-semibold tracking-tight">{{ $m['label'] }}</h2>
                    </div>
                    <span class="tnum rounded-full bg-white px-2 py-0.5 text-xs font-medium text-slate-500 shadow-sm dark:bg-slate-800 dark:text-slate-400">
                        {{ $items->count() }}
                    </span>
                </header>

                <div data-lista="{{ $estado }}"
                     class="scroll-thin flex min-h-[8rem] flex-1 flex-col gap-2.5 px-3 pb-3">
                    @forelse ($items as $t)
                        @php [$pLabel, $pTone] = $prio[$t->prioridad] ?? $prio[2]; @endphp
                        @php $vencida = $t->fin && \Illuminate\Support\Carbon::parse($t->fin)->lt(now()->startOfDay()) && $estado !== 'Finalizado'; @endphp
                        <article data-id="{{ $t->id }}" @click="ver({{ $t->id }})"
                                 class="card cursor-grab p-3.5 transition hover:shadow-md active:cursor-grabbing">
                            <div class="flex items-center justify-between gap-2">
                                <x-badge :tone="$pTone">{{ $pLabel }}</x-badge>
                                <span class="tnum text-[11px] text-slate-400">#{{ $t->id }}</span>
                            </div>

                            <h3 class="mt-2.5 text-[13px] font-medium leading-snug text-slate-900 dark:text-slate-100">
                                {{ $t->titulo }}
                            </h3>

                            <p class="mt-1.5 flex items-center gap-1.5 truncate text-xs text-slate-500 dark:text-slate-400">
                                <x-icon name="users" class="h-3.5 w-3.5 shrink-0" />
                                <span class="truncate">{{ $t->a_quien }}</span>
                            </p>

                            <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-2.5 text-[11px] dark:border-slate-800">
                                <span class="flex items-center gap-1.5 {{ $vencida ? 'font-medium text-rose-600 dark:text-rose-400' : 'text-slate-400' }}">
                                    <x-icon name="clock" class="h-3.5 w-3.5" />
                                    {{ $t->fin ? \Illuminate\Support\Carbon::parse($t->fin)->format('d/m') : 'sin fecha' }}
                                </span>
                                @if ($t->sub_tarea)
                                    <span class="text-slate-400" title="{{ $t->sub_tarea }}">subtarea</span>
                                @endif
                            </div>
                        </article>
                    @empty
                        <p class="px-1 py-6 text-center text-xs text-slate-400">Sin tareas</p>
                    @endforelse
                </div>
            </section>
        @endforeach
    </div>

    {{-- ================================ modal ================================ --}}
    <div x-cloak x-show="abierto" class="fixed inset-0 z-[70] flex items-end justify-center sm:items-center">
        <div x-show="abierto" x-transition.opacity @click="abierto = false"
             class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

        <div x-show="abierto" x-transition.scale.origin.bottom
             class="relative z-10 max-h-[88vh] w-full max-w-2xl overflow-y-auto rounded-t-2xl bg-white p-6 shadow-2xl sm:rounded-2xl dark:bg-slate-900">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        Tarea <span class="tnum" x-text="'#' + t.id"></span>
                    </p>
                    <h2 class="mt-1 text-lg font-semibold leading-snug tracking-tight" x-text="t.titulo"></h2>
                </div>
                <button type="button" @click="abierto = false" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                    <x-icon name="x" class="h-5 w-5" />
                </button>
            </div>

            <p x-show="cargando" class="mt-6 text-sm text-slate-500">Cargando...</p>

            <div x-show="!cargando" class="mt-5 space-y-5">
                <div class="flex flex-wrap gap-2">
                    <x-badge tone="brand"><span x-text="t.estado"></span></x-badge>
                    <x-badge tone="slate"><span x-text="'Prioridad ' + t.prioridad"></span></x-badge>
                    <x-badge tone="slate"><span x-text="'Asignada a ' + t.a_quien"></span></x-badge>
                </div>

                <div>
                    <p class="label">Descripción</p>
                    <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-300" x-text="t.descripcion"></p>
                </div>

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div>
                        <p class="label">Inicio</p>
                        <p class="text-sm" x-text="t.inicio || '—'"></p>
                    </div>
                    <div>
                        <p class="label">Vencimiento</p>
                        <p class="text-sm" x-text="t.fin || '—'"></p>
                    </div>
                    <div>
                        <p class="label">Creada</p>
                        <p class="text-sm" x-text="t.cuando || '—'"></p>
                    </div>
                    <div>
                        <p class="label">Origen</p>
                        <p class="text-sm" x-text="t.quien || '—'"></p>
                    </div>
                </div>

                <div x-show="t.sub_tarea">
                    <p class="label">Subtarea</p>
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        <span x-text="t.sub_tarea"></span>
                        <span class="ml-1 text-xs text-slate-400" x-text="'(' + (t.sub_estado || '') + ')'"></span>
                    </p>
                </div>

                <div>
                    <p class="label">Bitacora</p>
                    <pre class="scroll-thin max-h-40 overflow-auto whitespace-pre-wrap rounded-lg border border-slate-200 bg-slate-50 p-3 font-mono text-xs leading-relaxed text-slate-600 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-400" x-text="t.mensaje || 'Sin movimientos registrados.'"></pre>
                </div>

                <form :action="'/tareas/' + t.id + '/nota'" method="POST" class="flex items-start gap-2 border-t border-slate-100 pt-4 dark:border-slate-800">
                    @csrf
                    <input name="mensaje" required maxlength="1000" class="input" placeholder="Agregar una nota a la tarea...">
                    <button type="submit" class="btn-primary shrink-0">Agregar</button>
                </form>

                @if (auth()->user()->puedeEditar())
                    <form :action="'/tareas/' + t.id" method="POST"
                          onsubmit="return confirm('¿Eliminar esta tarea? La accion queda registrada en la bitacora.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-ghost text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-500/10">
                            <x-icon name="trash" class="h-4 w-4" /> Eliminar tarea
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- =========================== modal nueva tarea =========================== --}}
    <div x-cloak x-show="nueva" class="fixed inset-0 z-[70] flex items-end justify-center sm:items-center">
        <div x-show="nueva" x-transition.opacity @click="nueva = false"
             class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

        <form x-show="nueva" x-transition.scale.origin.bottom id="form-nueva-tarea" method="POST" action="{{ route('tareas.store') }}"
              class="relative z-10 max-h-[88vh] w-full max-w-xl space-y-4 overflow-y-auto rounded-t-2xl bg-white p-6 shadow-2xl sm:rounded-2xl dark:bg-slate-900">
            @csrf

            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold tracking-tight">Nueva tarea</h2>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                        Se crea en <span class="font-medium">Pendiente</span> y queda registrada con tu usuario.
                    </p>
                </div>
                <button type="button" @click="nueva = false" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                    <x-icon name="x" class="h-5 w-5" />
                </button>
            </div>

            <div>
                <label class="label" for="titulo">Título</label>
                <input id="titulo" name="titulo" required maxlength="255" class="input" placeholder="Reparar acometida en Av. Rivadavia 1234">
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label" for="tarea">Tipo de tarea</label>
                    <input id="tarea" name="tarea" required maxlength="255" list="tipos-tarea" class="input" placeholder="Instalación">
                    <datalist id="tipos-tarea">
                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div>
                    <label class="label" for="prioridad">Prioridad</label>
                    <select id="prioridad" name="prioridad" class="select">
                        @foreach ($prio as $n => $metaPrio)
                            <option value="{{ $n }}" @selected($n === 2)>{{ $metaPrio[0] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="label" for="a_quien">Asignar a</label>
                <input id="a_quien" name="a_quien" required maxlength="255" list="tecnicos-tarea" class="input" placeholder="técnico">
                <datalist id="tecnicos-tarea">
                    @foreach ($tecnicos as $t)
                        <option value="{{ $t }}"></option>
                    @endforeach
                </datalist>
                <p class="mt-1 text-[11px] text-slate-400">Es el mismo handle que usa el sistema legacy (<span class="font-mono">a_quien</span>).</p>
            </div>

            <div>
                <label class="label" for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="3" maxlength="2000" class="input" placeholder="Detalle del trabajo a realizar..."></textarea>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label" for="inicio">Inicio</label>
                    <input id="inicio" name="inicio" type="date" value="{{ now()->toDateString() }}" class="input">
                </div>
                <div>
                    <label class="label" for="fin">Vencimiento</label>
                    <input id="fin" name="fin" type="date" value="{{ now()->addDays(3)->toDateString() }}" class="input">
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4 dark:border-slate-800">
                <button type="button" @click="nueva = false" class="btn-secondary">Cancelar</button>
                <button type="submit" class="btn-primary">
                    <x-icon name="plus" class="h-4 w-4" /> Crear tarea
                </button>
            </div>
        </form>
    </div>
</div>

<script type="module">
    // Drag and drop -> PATCH /tareas/{id}/estado  (con CSRF y sesion validada)
    const token = document.querySelector('meta[name="csrf-token"]').content;

    document.querySelectorAll('[data-lista]').forEach((lista) => {
        Sortable.create(lista, {
            group: 'kanban',
            animation: 150,
            ghostClass: 'opacity-40',
            dragClass: 'rotate-1',
            onEnd: async (evt) => {
                const id = evt.item.dataset.id;
                const estado = evt.to.dataset.lista;
                if (evt.from === evt.to) return;

                try {
                    const r = await fetch(`/tareas/${id}/estado`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token,
                        },
                        body: JSON.stringify({ estado }),
                    });
                    if (!r.ok) throw new Error('respuesta ' + r.status);
                    actualizarContadores();
                } catch (e) {
                    evt.from.insertBefore(evt.item, evt.from.children[evt.oldIndex] || null);
                    alert('No se pudo mover la tarea. Recargá la página.');
                }
            },
        });
    });

    function actualizarContadores() {
        document.querySelectorAll('[data-lista]').forEach((lista) => {
            const contador = lista.parentElement.querySelector('header span.tnum');
            if (contador) contador.textContent = lista.querySelectorAll('article').length;
        });
    }
</script>
@endsection
