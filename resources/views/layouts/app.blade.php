<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Panel') · FieldOps</title>

    {{-- Evita el flash de tema al cargar --}}
    <script>
        try { if (localStorage.getItem('fieldops-tema') === 'oscuro') document.documentElement.classList.add('dark'); } catch (e) {}
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased">
@php
    $nav = [
        'Operación' => [
            ['route' => 'dashboard', 'label' => 'Tablero', 'icon' => 'home'],
            ['route' => 'tareas.index', 'label' => 'Tareas', 'icon' => 'clipboard', 'match' => 'tareas.*'],
            ['route' => 'altas.index', 'label' => 'Altas', 'icon' => 'signal', 'match' => 'altas.*'],
            ['route' => 'stock.index', 'label' => 'Stock', 'icon' => 'cube', 'match' => 'stock.*'],
            ['route' => 'tecnicos.index', 'label' => 'Técnicos', 'icon' => 'users', 'match' => 'tecnicos.*'],
        ],
        'Administración' => array_values(array_filter([
            auth()->user()?->isAdmin() ? ['route' => 'usuarios.index', 'label' => 'Usuarios', 'icon' => 'key', 'match' => 'usuarios.*'] : null,
        ])),
    ];
@endphp

<div x-data="{ movil: false, menu: false }" class="min-h-full">

    {{-- ============================ barra lateral ============================ --}}
    <div x-cloak x-show="movil" x-transition.opacity @click="movil = false"
         class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"></div>

    <aside x-cloak
           :class="movil ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-slate-900 transition-transform duration-200 ease-out lg:translate-x-0">

        <div class="flex h-16 shrink-0 items-center gap-2.5 px-5">
            <span class="grid h-8 w-8 place-items-center rounded-lg bg-brand-600 text-white shadow-lg shadow-brand-600/30">
                <x-icon name="signal" class="h-4.5 w-4.5" />
            </span>
            <div class="leading-tight">
                <p class="text-sm font-semibold tracking-tight text-white">FieldOps</p>
                <p class="text-[10px] uppercase tracking-widest text-slate-500">modernizado · Laravel 12</p>
            </div>
        </div>

        <nav class="scroll-thin flex-1 overflow-y-auto px-3 pb-4">
            @foreach (array_filter($nav) as $grupo => $items)
                <p class="nav-label">{{ $grupo }}</p>
                @foreach ($items as $item)
                    @php $activo = request()->routeIs($item['match'] ?? $item['route']); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="nav-item {{ $activo ? 'nav-item-active' : '' }}">
                        <x-icon :name="$item['icon']" class="h-[18px] w-[18px] shrink-0" />
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            @endforeach
        </nav>

        <div class="shrink-0 border-t border-slate-800 p-3">
            <div class="flex items-center gap-3 rounded-lg px-2 py-2">
                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-slate-800 text-xs font-semibold text-slate-200">
                    {{ auth()->user()?->iniciales() }}
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-medium text-slate-200">{{ auth()->user()?->name }}</p>
                    <p class="truncate text-[11px] text-slate-500">{{ auth()->user()?->role }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Cerrar sesión"
                            class="rounded-md p-1.5 text-slate-500 transition hover:bg-slate-800 hover:text-slate-200">
                        <x-icon name="logout" class="h-4 w-4" />
                    </button>
                </form>
            </div>
            <p class="px-2 pt-1 text-[10px] text-slate-600">Demo · datos sinteticos</p>
        </div>
    </aside>

    {{-- ============================== contenido ============================== --}}
    <div class="lg:pl-64">
        <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/80 backdrop-blur-md dark:border-slate-800 dark:bg-slate-950/80">
            <div class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8">
                <button type="button" @click="movil = true"
                        class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden dark:hover:bg-slate-800">
                    <x-icon name="queue" class="h-5 w-5" />
                </button>

                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-[15px] font-semibold tracking-tight text-slate-900 dark:text-slate-100">
                        @yield('titulo', 'Tablero')
                    </h1>
                    @hasSection('subtitulo')
                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">@yield('subtitulo')</p>
                    @endif
                </div>

                <form method="GET" action="{{ route('buscar') }}" class="hidden md:block">
                    <div class="relative">
                        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar OT, cliente, técnico, material..."
                               class="input w-72 pl-9 lg:w-80">
                    </div>
                </form>

                <button type="button" onclick="toggleTema()" title="Cambiar tema"
                        class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800">
                    <x-icon name="sun" class="h-5 w-5 dark:hidden" />
                    <x-icon name="moon" class="hidden h-5 w-5 dark:block" />
                </button>

                <div x-data="{ abierto: false }" class="relative">
                    <button type="button" @click="abierto = !abierto"
                            class="flex items-center gap-2 rounded-lg p-1 pr-2 transition hover:bg-slate-100 dark:hover:bg-slate-800">
                        <span class="grid h-7 w-7 place-items-center rounded-full bg-brand-600 text-[11px] font-semibold text-white">
                            {{ auth()->user()?->iniciales() }}
                        </span>
                        <x-icon name="chevron-down" class="h-3.5 w-3.5 text-slate-400" />
                    </button>

                    <div x-cloak x-show="abierto" @click.outside="abierto = false" x-transition.origin.top.right
                         class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg dark:border-slate-800 dark:bg-slate-900">
                        <div class="border-b border-slate-100 px-4 py-3 dark:border-slate-800">
                            <p class="truncate text-sm font-medium text-slate-900 dark:text-slate-100">{{ auth()->user()?->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ auth()->user()?->email }}</p>
                        </div>
                        <div class="flex items-center justify-between px-4 py-2.5 text-xs text-slate-500">
                            <span>Rol</span>
                            <x-badge tone="brand">{{ auth()->user()?->role }}</x-badge>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 dark:border-slate-800">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 px-4 py-2.5 text-left text-sm text-slate-600 transition hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800">
                                <x-icon name="logout" class="h-4 w-4" /> Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="px-4 py-6 sm:px-6 lg:px-8">
            @yield('contenido')
        </main>

        <footer class="px-4 pb-8 sm:px-6 lg:px-8">
            <p class="text-center text-xs text-slate-400 dark:text-slate-600">
                FieldOps · modernización de un sistema legacy · datos de demostración
            </p>
        </footer>
    </div>

    {{-- ============================== avisos ============================== --}}
    <div class="pointer-events-none fixed bottom-4 right-4 z-[60] flex flex-col gap-2">
        @if (session('ok'))
            <div data-toast class="pointer-events-auto flex items-center gap-2 rounded-xl border border-emerald-200 bg-white px-4 py-3 text-sm text-emerald-700 shadow-lg transition-opacity duration-300 dark:border-emerald-500/30 dark:bg-slate-900 dark:text-emerald-400">
                <x-icon name="check" class="h-4 w-4" /> {{ session('ok') }}
            </div>
        @endif
        @if (session('error'))
            <div data-toast class="pointer-events-auto flex items-center gap-2 rounded-xl border border-rose-200 bg-white px-4 py-3 text-sm text-rose-700 shadow-lg transition-opacity duration-300 dark:border-rose-500/30 dark:bg-slate-900 dark:text-rose-400">
                <x-icon name="alert" class="h-4 w-4" /> {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div data-toast class="pointer-events-auto max-w-sm rounded-xl border border-rose-200 bg-white px-4 py-3 text-sm text-rose-700 shadow-lg dark:border-rose-500/30 dark:bg-slate-900 dark:text-rose-400">
                <div class="flex items-start gap-2">
                    <x-icon name="alert" class="mt-0.5 h-4 w-4 shrink-0" />
                    <ul class="space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>
</body>
</html>
