@extends('layouts.guest')

@section('titulo', 'Acceso')

@section('contenido')
<div class="grid min-h-full lg:grid-cols-2">

    {{-- ------------------------------ formulario ------------------------------ --}}
    <div class="flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-16">
        <div class="mx-auto w-full max-w-sm">
            <div class="flex items-center gap-2.5">
                <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-600 text-white shadow-lg shadow-brand-600/25">
                    <x-icon name="signal" class="h-5 w-5" />
                </span>
                <div class="leading-tight">
                    <p class="text-sm font-semibold tracking-tight">FieldOps</p>
                    <p class="text-[10px] uppercase tracking-widest text-slate-500">modernizado · Laravel 12</p>
                </div>
            </div>

            <h1 class="mt-10 text-2xl font-semibold tracking-tight">Iniciar sesión</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Gestión de instalaciones, stock y técnicos.
            </p>

            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5" x-data="{
                email: '{{ old('email', 'admin@fieldops.test') }}',
                password: 'password',
                usar(e, p) { this.email = e; this.password = p; }
            }">
                @csrf

                <div>
                    <label for="email" class="label">Correo</label>
                    <input id="email" name="email" type="email" x-model="email" required autofocus
                           autocomplete="username" class="input" placeholder="usuario@fieldops.test">
                </div>

                <div>
                    <label for="password" class="label">Contraseña</label>
                    <input id="password" name="password" type="password" x-model="password" required
                           autocomplete="current-password" class="input" placeholder="••••••••">
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                    <input type="checkbox" name="recordarme" value="1"
                           class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900">
                    Mantener sesión iniciada
                </label>

                <button type="submit" class="btn-primary w-full">
                    Entrar
                    <x-icon name="chevron-right" class="h-4 w-4" />
                </button>
            </form>

            <div class="mt-8 rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900/60">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Usuarios de demo</p>
                <div class="mt-2 grid gap-1.5 text-xs">
                    @foreach ([
                        ['Administrador', 'admin@fieldops.test'],
                        ['Supervisor', 'supervisor@fieldops.test'],
                        ['Tecnico', 'tecnico@fieldops.test'],
                        ['Deposito', 'deposito@fieldops.test'],
                    ] as [$rol, $mail])
                        <button type="button" @click="usar('{{ $mail }}', 'password')"
                                class="flex items-center justify-between rounded-lg px-2.5 py-1.5 text-left transition hover:bg-white dark:hover:bg-slate-800">
                            <span class="text-slate-600 dark:text-slate-300">{{ $rol }}</span>
                            <span class="font-mono text-[11px] text-slate-400">{{ $mail }}</span>
                        </button>
                    @endforeach
                </div>
                <p class="mt-2 px-2.5 text-[11px] text-slate-400">Contraseña: <span class="font-mono">password</span></p>
            </div>
        </div>
    </div>

    {{-- -------------------------------- panel --------------------------------- --}}
    <div class="relative hidden overflow-hidden bg-slate-900 lg:block">
        <div class="absolute inset-0 bg-[radial-gradient(80%_60%_at_70%_10%,rgba(79,70,229,0.35),transparent_60%)]"></div>
        <div class="absolute -right-16 top-1/3 h-72 w-72 rounded-full bg-brand-600/20 blur-3xl"></div>

        <div class="relative flex h-full flex-col justify-between p-14">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-400">Migración completada</p>
                <h2 class="mt-4 max-w-md text-3xl font-semibold leading-tight tracking-tight text-white">
                    De un sistema legacy en PHP 7.4 a una aplicación moderna y segura.
                </h2>
                <p class="mt-4 max-w-md text-sm leading-relaxed text-slate-400">
                    Reescritura sobre Laravel con seguridad por diseño: consultas preparadas,
                    contraseñas hasheadas y autorización por rol en cada endpoint.
                </p>
            </div>

            <div class="grid grid-cols-3 gap-6 border-t border-slate-800 pt-8">
                @foreach ([
                    ['378', 'archivos legacy'],
                    ['76', 'tablas migradas'],
                    ['0', 'SQL concatenado'],
                ] as [$valor, $label])
                    <div>
                        <p class="tnum text-2xl font-semibold text-white">{{ $valor }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
