<header class="bg-[#BFF205] py-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center px-6">

        <h1 class="text-3xl font-bold text-gray-800">Waravel</h1>

        <div class="flex items-center space-x-6">

            <!-- Esta parte varia segun si la sesion esta iniciada o no -->
            <a href="/"
               class="bg-gray-800 text-white px-5 py-2 rounded-lg shadow-md hover:bg-gray-900 transition-all duration-300">
                Iniciar Sesión
            </a>

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
