<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Acceso') · FieldOps</title>
    <script>
        try { if (localStorage.getItem('fieldops-tema') === 'oscuro') document.documentElement.classList.add('dark'); } catch (e) {}
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased">
    @yield('contenido')
</body>
</html>
