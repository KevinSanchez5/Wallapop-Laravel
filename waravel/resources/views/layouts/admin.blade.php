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
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen m-0 p-0">

<div class="flex min-h-screen">

    <!-- Barra Lateral -->
    <div id="sidebar" class="w-64 bg-[#BFF205] text-black p-6 flex flex-col h-screen transform-translate-x-full transition-transform duration-300 z-20">
        <h4 class="text-center text-xl font-bold mb-6">Administración</h4>

        <!-- Enlaces de Navegación -->
        <nav class="flex-1">
            <a href="{{ route('admin.dashboard') }}" class="block py-2 px-4 rounded-lg hover:bg-black hover:text-white">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="{{ route('clients.list') }}" class="block py-2 px-4 rounded-lg hover:bg-black hover:text-white">
                <i class="fas fa-users"></i> Gestionar Usuarios
            </a>
            <a href="{{ route('products.list') }}" class="block py-2 px-4 rounded-lg hover:bg-black hover:text-white">
                <i class="fas fa-box"></i> Gestionar Productos
            </a>
            <a href="#" class="block py-2 px-4 rounded-lg hover:bg-black hover:text-white">
                <i class="fas fa-star"></i> Ver Valoraciones
            </a>
            <a href="{{ route('admins.list') }}" class="block py-2 px-4 rounded-lg hover:bg-black hover:text-white">
                <i class="fas fa-user-plus"></i> Añadir Administradores
            </a>
            <a href="{{ route('admin.backup') }}" class="block py-2 px-4 rounded-lg hover:bg-black hover:text-white">
                <i class="fas fa-database"></i> Copia de Seguridad
            </a>
        </nav>

        <!-- Botón Cerrar Sesión -->
        <form action="{{ route('logout') }}" method="POST" class="mt-auto">
            @csrf
            <button type="submit" class="w-full bg-black text-white py-2 rounded-lg hover:bg-gray-800 hover:text-white">
                Cerrar Sesión
            </button>
        </form>
    </div>

    <!-- Contenido Principal -->
    <main class="p-6 bg-gray-100 flex-1 min-h-screen">
        @yield('content')
    </main>

</div>
</body>
</html>

