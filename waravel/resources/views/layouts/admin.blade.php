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

    <!-- Modo oscuro -->
    <button id="modoOscuroBtn" class=" fixed top-4 right-4 p-2 rounded-lg bg-white hover:text-white hover:bg-black  dark:hover:text-black dark:hover:bg-white dark:bg-black dark:text-white transform" aria-label="Cambiar modo de tema">
        <svg id="modoOscuroIconLuz" class="hidden w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="5"></circle>
            <line x1="12" y1="1" x2="12" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="23"></line>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
            <line x1="1" y1="12" x2="3" y2="12"></line>
            <line x1="21" y1="12" x2="23" y2="12"></line>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
        </svg>
        <svg id="modoOscuroIconNoche" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
        </svg>
    </button>

    <!-- Barra Lateral -->
    <div id="sidebar" class="w-64 bg-[#BFF205] text-black p-6 flex flex-col min-h-screen h-full fixed top-0 left-0 z-20">
        <h2 class="text-center text-2xl font-extrabold mb-6">Administración</h2>

        <!-- Enlaces de Navegación -->
        <nav class="flex-1">
            <a href="{{ route('admin.dashboard') }}" class="block py-2 px-4 rounded-lg hover:bg-black hover:text-white">
                <i class="fas fa-tachometer-alt"></i> &nbsp; Dashboard
            </a>
            <a href="{{ route('admin.clients') }}" class="block py-2 px-4 rounded-lg hover:bg-black hover:text-white">
                <i class="fas fa-users"></i> &nbsp; Gestionar Clientes
            </a>
            <a href="{{ route('admin.products') }}" class="block py-2 px-4 rounded-lg hover:bg-black hover:text-white">
                <i class="fas fa-box"></i> &nbsp; Gestionar Productos
            </a>
            <a href="#" class="block py-2 px-4 rounded-lg hover:bg-black hover:text-white">
                <i class="fas fa-star"></i> &nbsp;Ver Valoraciones
            </a>
            <a href="" class="block py-2 px-4 rounded-lg hover:bg-black hover:text-white">
                <i class="fas fa-shopping-cart"></i> &nbsp; Gestionar Ventas
            </a>
            <a href="{{ route('admins.add') }}" class="block py-2 px-4 rounded-lg hover:bg-black hover:text-white">
                <i class="fas fa-user-plus"></i> &nbsp; Añadir Admin
            </a>
            <a href="{{ route('admin.backup') }}" class="block py-2 px-4 rounded-lg hover:bg-black hover:text-white">
                <i class="fas fa-database"></i> &nbsp; Copia de Seguridad
            </a>
        </nav>

        <!-- Botón Cerrar Sesión -->
        <form action="{{ route('logout') }}" method="POST" class="mt-auto">
            @csrf
            <button type="submit" class="w-full bg-black text-white py-2 rounded-lg hover:bg-white hover:text-black">
                <b>Cerrar Sesión</b>
            </button>
        </form>
    </div>
    <div class="w-64 h-full"></div>

    <!-- Contenido Principal -->
    <main class="p-6 bg-gray-100 dark:bg-gray-900 dark:text-white flex-1 min-h-screen">
        @yield('content')
    </main>

    <!-- Scripts -->
    <script>document
            .addEventListener('DOMContentLoaded', function () {
                const html = document.documentElement;
                const botonModoOscuro = document.getElementById('modoOscuroBtn');
                const iconoLuz = document.getElementById('modoOscuroIconLuz');
                const iconoNoche = document.getElementById('modoOscuroIconNoche');

                if (localStorage.getItem('modoOscuro') === 'true') {
                    html.classList.add('dark');
                    iconoLuz.classList.remove('hidden');
                    iconoNoche.classList.add('hidden');
                } else {
                    html.classList.remove('dark');
                    iconoLuz.classList.add('hidden');
                    iconoNoche.classList.remove('hidden');
                }

                botonModoOscuro.addEventListener('click', function () {
                    if (html.classList.contains('dark')) {
                        html.classList.remove('dark');
                        localStorage.setItem('modoOscuro', 'false');
                        iconoLuz.classList.add('hidden');
                        iconoNoche.classList.remove('hidden');
                    } else {
                        html.classList.add('dark');
                        localStorage.setItem('modoOscuro', 'true');
                        iconoLuz.classList.remove('hidden');
                        iconoNoche.classList.add('hidden');
                    }
                });
            });
    </script>

</div>
</body>
</html>

