@extends('layouts.app')

@section('title', 'Página de Inicio')

@section('content')
    <x-header />

    <div class="container mx-auto py-6">
        <x-carousel />

        <!-- Filtros y búsqueda -->
        <div class="container mx-auto mt-8 px-4">
            <div class="bg-white dark:bg-gray-800 shadow-lg dark:shadow-md rounded-xl p-6">
                @php
                    $categories = ['todos', 'Tecnologia', 'Ropa', 'Hogar', 'Coleccionismo', 'Vehiculos', 'Videojuegos', 'Musica', 'Deporte', 'Cine', 'Cocina'];
                    $selectedCategory = request('categoria', 'todos');
                @endphp

                    <!-- Menú de categorías -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <!-- Botones de categorías -->
                    <div class="flex flex-wrap gap-2">
                        @foreach ($categories as $category)
                            <form method="GET" action="{{ route('productos.search') }}">
                                <button type="submit" name="categoria" value="{{ $category }}"
                                        class="dark:hover:bg-[#BFF205] px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-300
                                            {{ $selectedCategory == $category ? 'bg-[#BFF205] text-black' : 'bg-gray-200 dark:bg-gray-700 hover:bg-[#BFF205] dark:hover:text-black text-gray-700 dark:text-gray-200  hover:text-black' }}">
                                    {{ ucfirst($category) }}
                                </button>
                            </form>
                        @endforeach
                    </div>

                    <!-- Formulario de búsqueda -->
                    <form action="{{ route('productos.search') }}" method="GET" class="relative flex items-center w-full md:w-96 mt-4 md:mt-0">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Buscar productos..."
                               class="w-full px-4 py-2 pl-12 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 outline-none focus:ring-2 focus:ring-blue-300 transition">
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

        <!-- Contenedor de productos -->
        <div id="productos-container" class="container mx-auto mt-8 px-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
            @forelse ($productos as $producto)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden flex flex-col">
                    <img src="{{ asset('storage/' . ($producto->imagenes[0] ?? 'default.jpg')) }}"
                         alt="{{ $producto->nombre }}"
                         class="w-full h-40 object-cover rounded-t-lg">

                    <div class="p-4 flex-grow">
                        <h3 class="font-semibold text-lg text-gray-900 dark:text-white">{{ $producto->nombre }}</h3>
                        <p class="text-gray-500 dark:text-gray-300 text-sm">{{ $producto->categoria }}</p>
                        <p class="text-gray-900 dark:text-gray-200 font-semibold mt-2">{{ $producto->precio }} €</p>
                    </div>

                    <div class="p-4">
                        <a href="{{ route('producto.show', $producto->guid) }}"
                           class="block w-full bg-[#BFF205] text-black text-center font-medium py-2 px-6 rounded-md transition-transform duration-300 hover:scale-105 hover:shadow-lg">
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

    <x-footer />
@endsection
