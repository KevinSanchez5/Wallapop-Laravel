<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mi Sitio')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen m-0 p-0">
    <!-- Este es el contenido general que varía en cada página -->
    <main class="min-h-screen m-0 p-0">
        @yield('content')
    </main>
</body>
</html>
