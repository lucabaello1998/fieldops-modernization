@extends('layouts.app')

@section('titulo', 'Usuarios')
@section('subtitulo', 'Gestión de usuarios del sistema nuevo · contraseñas hasheadas con bcrypt')

@section('contenido')
    {{-- ================================== KPIs ================================== --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-kpi label="Usuarios totales" :value="number_format($kpis['total'], 0, ',', '.')"
               icon="users" tone="brand" hint="sistema nuevo" />
        <x-kpi label="Activos" :value="number_format($kpis['activos'], 0, ',', '.')"
               icon="check" tone="emerald" :hint="$kpis['inactivos'] . ' inactivos'" />
        <x-kpi label="Administradores" :value="number_format($kpis['admins'], 0, ',', '.')"
               icon="key" tone="sky" hint="acceso total" />
        <x-kpi label="Inactivos" :value="number_format($kpis['inactivos'], 0, ',', '.')"
               icon="lock" tone="rose" hint="sin acceso al panel" />
    </div>

    {{-- ============================== filtros ================================= --}}
    <x-card class="mt-6">
        <form method="GET" action="{{ route('usuarios.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="min-w-[14rem] flex-1">
                <label for="q" class="label">Buscar</label>
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input id="q" name="q" type="search" value="{{ $filtros['q'] ?? '' }}"
                           placeholder="Nombre o correo" class="input pl-9">
                </div>
            </div>

            <div class="w-44">
                <label for="role" class="label">Rol</label>
                <select id="role" name="role" class="select">
                    <option value="">Todos</option>
                    @foreach ($roles as $rol)
                        <option value="{{ $rol }}" @selected(($filtros['role'] ?? '') === $rol)>{{ $rol }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-40">
                <label for="activo" class="label">Estado</label>
                <select id="activo" name="activo" class="select">
                    <option value="">Todos</option>
                    <option value="1" @selected(($filtros['activo'] ?? '') === '1')>Activos</option>
                    <option value="0" @selected(($filtros['activo'] ?? '') === '0')>Inactivos</option>
                </select>
            </div>

            <button type="submit" class="btn-primary">
                <x-icon name="funnel" class="h-4 w-4" /> Filtrar
            </button>
            <a href="{{ route('usuarios.index') }}" class="btn-secondary">Limpiar</a>
        </form>
    </x-card>

    {{-- ============================ alta de usuario ============================ --}}
    <div x-data="{ nuevo: false }" class="mt-4">
        <button type="button" @click="nuevo = !nuevo" class="btn-primary">
            <x-icon name="plus" class="h-4 w-4" /> Nuevo usuario
        </button>

        <x-card x-cloak x-show="nuevo" x-transition class="mt-4"
                title="Nuevo usuario" sub="La contraseña se guarda hasheada, nunca en texto plano">
            <form method="POST" action="{{ route('usuarios.store') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @csrf

                <div>
                    <label for="name" class="label">Nombre y apellido</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" maxlength="120" required class="input">
                </div>

                <div>
                    <label for="email" class="label">Correo</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" maxlength="160" required class="input">
                </div>

                <div>
                    <label for="password" class="label">Contraseña (mínimo 8 caracteres)</label>
                    <input id="password" name="password" type="password" minlength="8" required autocomplete="new-password" class="input">
                </div>

                <div>
                    <label for="role" class="label">Rol</label>
                    <select id="role" name="role" required class="select">
                        @foreach ($roles as $rol)
                            <option value="{{ $rol }}" @selected(old('role') === $rol)>{{ $rol }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="zona" class="label">Zona (opcional)</label>
                    <input id="zona" name="zona" type="text" value="{{ old('zona') }}" maxlength="100" class="input">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="btn-primary w-full">
                        <x-icon name="check" class="h-4 w-4" /> Crear usuario
                    </button>
                </div>
            </form>
        </x-card>
    </div>

    {{-- =========================== tabla de usuarios ========================== --}}
    <x-card class="mt-4" title="Usuarios del sistema" :sub="$usuarios->total() . ' registro(s)'" :pad="false">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Zona</th>
                        <th>Estado</th>
                        <th>Último acceso</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                @forelse ($usuarios as $u)
                    <tbody x-data="{ editando: false }">
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-brand-50 text-xs font-semibold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                                        {{ $u->iniciales() }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate font-medium text-slate-900 dark:text-slate-100">{{ $u->name }}</p>
                                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $u->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td><x-badge tone="brand">{{ $u->role }}</x-badge></td>
                            <td class="text-slate-500 dark:text-slate-400">{{ $u->zona ?: '—' }}</td>
                            <td>
                                @if ($u->activo)
                                    <x-badge tone="emerald">Activo</x-badge>
                                @else
                                    <x-badge tone="slate">Inactivo</x-badge>
                                @endif
                            </td>
                            <td class="tnum whitespace-nowrap text-xs text-slate-500 dark:text-slate-400">
                                {{ $u->ultimo_acceso ? $u->ultimo_acceso->locale('es')->isoFormat('D MMM YYYY HH:mm') : 'Nunca' }}
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" @click="editando = !editando" class="btn-ghost btn-sm">
                                        <x-icon name="pencil" class="h-3.5 w-3.5" /> Editar
                                    </button>
                                    <form method="POST" action="{{ route('usuarios.destroy', $u) }}"
                                          onsubmit="return confirm('¿Eliminar este usuario?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-ghost btn-sm text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-500/10">
                                            <x-icon name="trash" class="h-3.5 w-3.5" /> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- panel de edicion en linea (form PATCH propio, sin anidar) --}}
                        <tr x-cloak x-show="editando" x-transition>
                            <td colspan="6" class="bg-slate-50 dark:bg-slate-900/60">
                                <form method="POST" action="{{ route('usuarios.update', $u) }}"
                                      class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                                    @csrf
                                    @method('PATCH')

                                    <div>
                                        <label class="label" for="name-{{ $u->id }}">Nombre</label>
                                        <input id="name-{{ $u->id }}" name="name" type="text" value="{{ $u->name }}" maxlength="120" required class="input">
                                    </div>

                                    <div>
                                        <label class="label" for="email-{{ $u->id }}">Correo</label>
                                        <input id="email-{{ $u->id }}" name="email" type="email" value="{{ $u->email }}" maxlength="160" required class="input">
                                    </div>

                                    <div>
                                        <label class="label" for="role-{{ $u->id }}">Rol</label>
                                        <select id="role-{{ $u->id }}" name="role" required class="select">
                                            @foreach ($roles as $rol)
                                                <option value="{{ $rol }}" @selected($u->role === $rol)>{{ $rol }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="label" for="zona-{{ $u->id }}">Zona</label>
                                        <input id="zona-{{ $u->id }}" name="zona" type="text" value="{{ $u->zona }}" maxlength="100" class="input">
                                    </div>

                                    <div>
                                        <label class="label" for="password-{{ $u->id }}">Nueva contraseña (opcional)</label>
                                        <input id="password-{{ $u->id }}" name="password" type="password" minlength="8"
                                               placeholder="Dejar vacío para no cambiar" autocomplete="new-password" class="input">
                                    </div>

                                    <label class="flex items-center gap-2 text-sm text-slate-600 sm:col-span-2 dark:text-slate-400">
                                        <input type="checkbox" name="activo" value="1" @checked($u->activo)
                                               class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900">
                                        Usuario activo (puede iniciar sesión)
                                    </label>

                                    <div class="flex items-center gap-2 sm:col-span-2 lg:col-span-3 lg:justify-end">
                                        <button type="button" @click="editando = false" class="btn-secondary">Cancelar</button>
                                        <button type="submit" class="btn-primary">
                                            <x-icon name="check" class="h-4 w-4" /> Guardar cambios
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                @empty
                    <tbody>
                        <tr>
                            <td colspan="6">
                                <x-empty title="Sin usuarios para esos filtros"
                                         sub="Probá limpiar la búsqueda o cambiar el rol y el estado." icon="users" />
                            </td>
                        </tr>
                    </tbody>
                @endforelse
            </table>
        </div>

        @if ($usuarios->hasPages())
            <div class="border-t border-slate-100 px-5 py-4 dark:border-slate-800">
                {{ $usuarios->links() }}
            </div>
        @endif
    </x-card>

@endsection
