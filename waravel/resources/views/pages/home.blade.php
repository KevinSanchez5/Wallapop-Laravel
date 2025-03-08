@extends('layouts.app')

@section('title', 'Página de Inicio')

@section('content')
    <x-header />

    <div class="container mx-auto py-6">
        <x-carousel />

        <!-- Filtros y búsqueda -->
        <div class="container mx-auto mt-8 px-4">
            <div class="bg-white dark:bg-gray-800 shadow-lg dark:shadow-md rounded-xl p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <!-- Formulario de búsqueda -->
                    <form action="{{ route('productos.search') }}" method="GET" class="w-full">
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Campo de búsqueda -->
                            <div class="relative flex-grow">
                                <input type="text" name="search" value="{{ request('search') }}"
                                       placeholder="Buscar productos..."
                                       class="w-full px-4 py-2 pl-12 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 outline-none focus:ring-2 focus:ring-blue-300 transition">
                                <svg class="absolute left-4 top-3 text-gray-500 dark:text-gray-400 w-5 h-5" xmlns="http://www.w3.org/2000/svg"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-4.35-4.35M16.5 10.5a6 6 0 11-12 0 6 6 0 0112 0z"/>
                                </svg>
                            </div>

                            <!-- Categoría (Menú desplegable) -->
                            <select name="categoria"
                                    class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-200 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-300">
                                <option value="todos" {{ request('categoria') == 'todos' ? 'selected' : '' }}>Todas</option>
                                @foreach(['Tecnologia', 'Ropa', 'Hogar', 'Coleccionismo', 'Vehiculos', 'Videojuegos', 'Musica', 'Deporte', 'Cine', 'Cocina', 'Otros'] as $categoria)
                                    <option value="{{ $categoria }}" {{ request('categoria') == $categoria ? 'selected' : '' }}>
                                        {{ ucfirst($categoria) }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Min precio -->
                            <div class="relative" style="width: 150px">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 w-5 h-5"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m-4-4l4 4 4-4"/>
                                </svg>
                                <input type="number" name="precio_min" placeholder="Min" value="{{ request('precio_min', 0) }}"
                                       class="w-full pl-10 pr-8 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-200 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-300">
                                <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">€</span>
                            </div>

                            <!-- Max precio -->
                            <div class="relative" style="width: 150px">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 w-5 h-5"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21V9m-4 4l4-4 4 4"/>
                                </svg>
                                <input type="number" name="precio_max" placeholder="Max" value="{{ request('precio_max', 999999) }}"
                                       class="w-full pl-10 pr-8 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-200 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-300">
                                <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">€</span>
                            </div>

                            <!-- Botón de búsqueda -->
                            <button type="submit"
                                    class="px-4 py-2 bg-[#BFF205] text-black font-semibold rounded-lg hover:scale-105 transition">
                                Buscar
                            </button>
                        </div>
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

        <!-- Paginación --><br>
        <div class="mt-4 text-center">
            {{ $productos->links('pagination::tailwind') }}
        </div><br>

    </div>

    <x-footer />
@endsection
