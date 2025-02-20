<header class="bg-[#BFF205] py-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center px-6">

        <!-- Logo -->
        <a href="{{ route('pages.home') }}" class="text-3xl font-bold text-gray-800">
            <b>Waravel</b>
        </a>

        <!-- Navegación -->
        <div class="flex items-center space-x-4">
            @auth
                <!-- Dropdown de usuario -->
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="h-10 flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-black bg-white transition-all duration-300 ease-in-out hover:bg-black hover:text-white hover:border-white dark:text-white dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:border-gray-500">
                            <img src="{{ asset('storage/' . (Auth::user()->avatar ?? 'clientes/avatar.png')) }}"
                                 alt="Avatar de {{ Auth::user()->name }}"
                                 class="w-8 h-8 rounded-full object-cover mr-2">
                            <span><b>{{ Auth::user()->name }}</b></span>
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
                        <x-dropdown-link :href="route('profile')">
                            {{ __('Perfil') }}
                        </x-dropdown-link>
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
                <!-- Botón de Iniciar sesión -->
                <a href="{{ route('login') }}"
                   class="h-10 px-6 flex items-center justify-center text-base font-extrabold tracking-wide text-white bg-black rounded-lg transition-all duration-300 hover:bg-white hover:text-black border-2 border-transparent hover:border-black shadow-md">
                    Iniciar sesión
                </a>
            @endauth

            <!-- Modo oscuro -->
            <button id="modoOscuroBtn" class="p-2 rounded-lg bg-white dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 transition" aria-label="Cambiar modo de tema">
                <svg id="modoOscuroIconLuz" class="hidden w-6 h-6 text-gray-800 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                <svg id="modoOscuroIconNoche" class="w-6 h-6 text-gray-800 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
            </button>

            <!-- Carrito -->
            <a href="/" class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                     stroke="currentColor" class="w-8 h-8 text-gray-800 hover:text-gray-900 transition-all duration-300">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.25 3h2.59a1.5 1.5 0 011.36.92l.83 2.08m0 0l3.06 7.64a1.5 1.5 0 001.36.92h6.78a1.5 1.5 0 001.36-.92l3.06-7.64m-16.8 0h16.8M9 18.75a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zm10.5 0a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                </svg>

                <!-- el numero de carrito cambia según el numero de lineas que tengamos, por defecto es 0 -->
                <span class="absolute -top-2 -right-2 bg-black text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full shadow-md">
                {{ $lineasCarrito ?? 0 }}
            </span>
            </a>
        </div>
    </div>
</header>
