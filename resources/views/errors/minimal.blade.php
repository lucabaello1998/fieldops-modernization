@php
    // Vista de error compartida: Laravel la usa como fallback para cualquier
    // status que no tenga su propia vista en resources/views/errors.
    $status = method_exists($exception ?? null, 'getStatusCode') ? $exception->getStatusCode() : 500;

    $mensajes = [
        403 => ['Sin permisos', 'Tu rol no tiene acceso a esta sección. Si creés que es un error, hablá con un administrador.', 'lock'],
        404 => ['No encontramos esa página', 'La dirección no existe o el registro que buscabas se eliminó.', 'search'],
        419 => ['La sesión expiró', 'Pasó demasiado tiempo desde que abriste el formulario. Volvé a intentarlo.', 'clock'],
        429 => ['Demasiados intentos', 'Esperá un minuto antes de volver a probar.', 'alert'],
        500 => ['Algo falló de nuestro lado', 'El error quedó registrado. Probá de nuevo en unos segundos.', 'alert'],
        503 => ['En mantenimiento', 'Estamos actualizando el sistema. Volvemos en unos minutos.', 'wrench'],
    ];

    [$titulo, $detalle, $icono] = $mensajes[$status] ?? ['Ocurrió un error', 'No pudimos completar la operación.', 'alert'];

    $destino = auth()->check() ? route('dashboard') : route('login');
    $accion = auth()->check() ? 'Volver al tablero' : 'Ir al inicio de sesión';
@endphp
<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $status }} · {{ $titulo }} · FieldOps</title>
    <script>
        try { if (localStorage.getItem('fieldops-tema') === 'oscuro') document.documentElement.classList.add('dark'); } catch (e) {}
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grid h-full place-items-center bg-slate-50 px-6 font-sans antialiased dark:bg-slate-950">
    <div class="w-full max-w-md">
        <a href="{{ $destino }}" class="mb-8 flex items-center justify-center gap-2.5">
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-600 text-white shadow-lg shadow-brand-600/30">
                <x-icon name="signal" class="h-5 w-5" />
            </span>
            <span class="leading-tight">
                <span class="block text-sm font-semibold tracking-tight text-slate-900 dark:text-white">FieldOps</span>
                <span class="block text-[10px] uppercase tracking-widest text-slate-500">modernizado · Laravel 12</span>
            </span>
        </a>

        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <span class="mx-auto grid h-12 w-12 place-items-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                <x-icon :name="$icono" class="h-6 w-6" />
            </span>

            <p class="mt-5 font-mono text-5xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ $status }}</p>
            <h1 class="mt-2 text-base font-semibold tracking-tight text-slate-900 dark:text-white">{{ $titulo }}</h1>
            <p class="mt-2 text-sm leading-relaxed text-slate-500 dark:text-slate-400">{{ $detalle }}</p>

            <a href="{{ $destino }}" class="btn-primary mt-6 w-full justify-center">
                {{ $accion }}
                <x-icon name="chevron-right" class="h-4 w-4" />
            </a>
        </div>

        <p class="mt-6 text-center text-[11px] text-slate-400">
            FieldOps · modernización de un sistema legacy · datos de demostración
        </p>
    </div>
</body>
</html>
