<header class="bg-[#BFF205] py-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center px-6">

        <a routerLink="/inicio" class="text-3xl font-bold text-gray-800">
            Waravel
        </a>

        <div class="flex items-center space-x-6">

            <!-- Esta parte varia segun si la sesion esta iniciada o no -->
            <a href="{{ route('login') }}"
               class="bg-gray-800 text-white px-5 py-2 rounded-lg shadow-md hover:bg-gray-900 transition-all duration-300">
                Iniciar Sesión
            </a>

            <button id="modoOscuroBtn" class="p-2 rounded-lg bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 transition">
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
