<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Mi Sitio'))</title>

    <!-- Íconos y Scripts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- CSS y JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<script>
    window.userId = {{Auth::check() ? Auth::id() : 'null'}};
</script>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen m-0 p-0">

<!-- Contenido de la página -->
<main class="min-h-screen m-0 p-0">
    @yield('content')
</main>
<div id="notificaciones" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

</body>
</html>
