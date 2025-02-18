@extends('layouts.app')

@section('title', 'Página de Inicio')

@section('content')
    <!-- Este es el header que cambia según el modo -->
    <x-header />

    <div class="container mx-auto py-6">
        <!-- Este es el carousel de la parte superior -->
        <x-carousel />

        <div class="container mx-auto mt-8 px-4">
            <div class="bg-white dark:bg-gray-800 shadow-lg dark:shadow-md rounded-xl p-6 flex flex-col md:flex-row md:justify-between md:items-center transition-all duration-300">
                @php
                    $categories = ['todos','Tecnologia','Ropa','Hogar','Coleccionismo','Vehiculos','Videojuegos','Musica','Deporte','Cine','Cocina'];
                    $selectedCategory = request('categoria', 'todos');
                @endphp

                    <!-- Contenedor principal con flexbox para alinear a izquierda y derecha -->
                <div class="flex items-center justify-between w-full flex-wrap md:flex-nowrap gap-3">
                    <!-- IZQUIERDA: Menú hamburguesa + 3 categorías principales -->
                    <div class="flex items-center gap-3">
                        <!-- Botón hamburguesa para las demás categorías -->
                        <div class="relative inline-block text-left">
                            <!-- Botón hamburguesa -->
                            <button type="button"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-300 bg-gray-100 dark:bg-gray-700 p-1 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75"
                                    id="category-menu-button">
                                <i class="fas fa-bars text-gray-700 dark:text-gray-200"></i>
                            </button>

                            <!-- Menú desplegable con las categorías restantes -->
                            <div class="origin-top-left absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none hidden"
                                 id="category-menu"
                                 role="menu"
                                 aria-orientation="vertical"
                                 aria-labelledby="category-menu-button">
                                <div class="py-1" role="none">
                                    @foreach ($categories as $category)
                                        @if (!in_array($category, ['todos', 'Tecnologia', 'Musica', 'Ropa']))
                                            <form method="GET" action="{{ route('productos.search') }}" class="inline-block w-full">
                                                <button type="submit" name="categoria" value="{{ $category }}"
                                                        class="w-full text-left px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-[#BFF205] hover:text-black dark:hover:bg-[#BFF205] dark:hover:text-black
                                               {{ $selectedCategory == $category ? 'bg-[#BFF205] text-black' : '' }}">
                                                    {{ ucfirst($category) }}
                                                </button>
                                            </form>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- 3 categorías principales: Todos, Electrónica, Ropa -->
                        <div class="flex gap-3">
                            @foreach (['todos', 'Tecnologia', 'Musica', 'Ropa'] as $category)
                                <form method="GET" action="{{ route('productos.search') }}">
                                    <button type="submit" name="categoria" value="{{ $category }}"
                                            class="px-4 py-2 rounded-lg text-gray-700 dark:text-gray-200 font-semibold transition-all duration-300
                                   hover:bg-[#BFF205] hover:text-black dark:hover:bg-[#BFF205] dark:hover:text-black
                                   {{ $selectedCategory == $category ? 'bg-[#BFF205] text-black dark:text-gray-700' : '' }}">
                                        {{ ucfirst($category) }}
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>

                    <!-- DERECHA: Barra de búsqueda -->
                    <form action="{{ route('productos.search') }}" method="GET" class="relative flex items-center w-full md:w-96 mt-3 md:mt-0">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Buscar productos..."
                               class="w-full px-4 py-2 pl-12 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 outline-none
                      focus:border-blue-500 focus:ring-2 focus:ring-blue-300 transition">
                        <svg class="absolute left-4 text-gray-500 dark:text-gray-400 w-5 h-5" xmlns="http://www.w3.org/2000/svg"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-4.35-4.35M16.5 10.5a6 6 0 11-12 0 6 6 0 0112 0z"/>
                        </svg>
                        <button type="submit"
                                class="ml-2 px-4 py-2 bg-[#BFF205] text-black font-semibold rounded-lg hover:scale-105 transition">
                            Buscar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Lista de productos -->
        <div id="productos-container" class="container mx-auto mt-8 px-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
            @forelse ($productos as $producto)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md dark:shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden h-full flex flex-col">
                    <img src="{{ asset('storage/productos/' . ($producto->imagenes[0] ?? 'default.jpg')) }}"
                         alt="Imagen del producto"
                         class="w-full h-40 object-cover rounded-t-lg">

                    <div class="p-4 flex-grow">
                        <h3 class="font-semibold text-lg text-gray-900 dark:text-white">{{ $producto->nombre }}</h3>
                        <p class="text-gray-500 dark:text-gray-300 text-sm">{{ $producto->categoria }}</p>
                        <p class="text-gray-900 dark:text-gray-200 font-semibold mt-2">{{ $producto->precio }} €</p>
                    </div>

                    <div class="p-4">
                        <a href="{{ route('producto.show', $producto->guid) }}"
                           class="block w-full bg-[#BFF205] text-black text-center font-medium py-2 px-6 rounded-md transition-transform duration-300 hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#BFF205] focus:ring-opacity-50">
                            Ver detalles
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-gray-600 dark:text-gray-400 col-span-5 text-center">No hay productos disponibles.</p>
            @endforelse
        </div>

        <!-- Paginación -->
        <div class="mt-4 text-center">
            {{ $productos->links('pagination::tailwind') }}
        </div>

    </div>

    <!-- Pie de página -->
    <x-footer />
@endsection
