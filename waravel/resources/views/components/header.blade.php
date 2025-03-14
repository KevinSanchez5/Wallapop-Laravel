<style>
    @media (max-width: 768px) { /* Para dispositivos móviles */
        .btn-mobile {
            justify-content: center; /* Centrar contenido */
            width: 40px; /* Ajustar ancho para que solo ocupe lo necesario */
            height: 40px; /* Hacerlo cuadrado */
            padding: 0; /* Quitar padding extra */
        }

        .btn-mobile span,
        .btn-mobile svg {
            display: none;
        }

        .btn-mobile img {
            margin: 0; /* Eliminar márgenes */
        }
    }
</style>

<header class="bg-[#BFF205] py-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center px-6">

        <!-- Logo -->
        <a href="{{ route('pages.home') }}" class="hover:text-white text-3xl font-extrabold text-gray-800" style="font-family: 'Inter', sans-serif;">
            Waravel
        </a>

        <!-- Navegación -->
        <div class="flex items-center space-x-4">
            @auth
                <!-- Dropdown de usuario -->
                <x-dropdown align="right" width="56" class="dropdown-menu">
                    <x-slot name="trigger">
                        <button class="btn-mobile h-10 flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md transition-all duration-300 bg-opacity-75 dark:bg-opacity-75 ease-in-out bg-white hover:text-white hover:bg-black dark:hover:text-black dark:hover:bg-white dark:bg-black dark:text-white">
                            <img src="{{ asset('storage/' . (Auth::user()->avatar ?? 'clientes/avatar.png')) }}"
                                 alt="Avatar de {{ Auth::user()->name }}"
                                 class="w-8 h-8 rounded-full object-cover mr-2">
                            <span data-test-id="logged-user"><b>{{ Auth::user()->name }}</b></span>
                            <svg class="ml-2 w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 text-gray-800 dark:text-white text-sm">
                            <p class="font-semibold">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                        </div>
                        <hr class="border-gray-300 dark:border-gray-600">

                        @if (Auth::user()->role === 'admin')
                            <x-dropdown-link :href="route('admin.dashboard')">
                                {{ __('Dashboard') }}
                            </x-dropdown-link>
                        @else
                            <x-dropdown-link :href="route('profile')">
                                {{ __('Perfil') }}
                            </x-dropdown-link>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                             onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Cerrar sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            @else
                <a href="{{ route('login') }}"
                   class="h-10 flex items-center justify-center text-base font-extrabold tracking-wide bg-opacity-75 dark:bg-opacity-75 bg-white text-black hover:text-white hover:bg-black dark:bg-black dark:text-white dark:hover:bg-white dark:hover:text-black rounded-lg transition-all border-2 border-transparent shadow-md px-6 sm:px-3">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6 transition-colors duration-300">
                        <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2" fill="none"/>
                        <path d="M4 20c0-4 4-6 8-6s8 2 8 6" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>
                    </svg>
                </a>
            @endauth

                <!-- Modo oscuro -->
                <button id="modoOscuroBtn" class="relative flex items-center justify-center w-10 h-10 rounded-full bg-white bg-opacity-75 backdrop-blur-md hover:bg-opacity-100 hover:text-white hover:bg-black dark:bg-black dark:bg-opacity-75 dark:text-white dark:hover:bg-white dark:hover:text-black transition text-sm transition-all duration-300" aria-label="Cambiar modo de tema">
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

            <!-- Carrito -->
            @if(auth()->guest() || auth()->user()->role === 'cliente')
                <a id="cartButton" href="{{ route('carrito') }}" class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                         stroke="currentColor" class="w-8 h-8 text-gray-800 hover:text-gray-900 transition-all duration-300">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.25 3h2.59a1.5 1.5 0 011.36.92l.83 2.08m0 0l3.06 7.64a1.5 1.5 0 001.36.92h6.78a1.5 1.5 0 001.36-.92l3.06-7.64m-16.8 0h16.8M9 18.75a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zm10.5 0a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                    </svg>

                    <!-- el numero de carrito cambia según el numero de lineas que tengamos, por defecto es 0 -->
                    <span id="itemCount" class="absolute -top-2 -right-2 bg-black text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full shadow-md">
                    {{ optional(session('carrito'))->itemAmount ?? 0 }}
                </span>
                </a>
            @endif
        </div>
    </div>
</header>

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
