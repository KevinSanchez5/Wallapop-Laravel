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

                <div class="flex flex-wrap gap-3">
                    <!-- Categorías -->
                    @php
                        $categories = ['Electrónica', 'Ropa', 'Hogar', 'Juguetes', 'Automóviles'];
                    @endphp
                    @foreach ($categories as $category)
                        <button class="relative overflow-hidden bg-gray-100 dark:bg-gray-700 px-5 py-2 rounded-lg text-gray-700 dark:text-gray-200 font-semibold
                        transition-all duration-300 hover:text-white dark:hover:text-black group">
                            <span class="absolute inset-0 bg-[#BFF205] scale-x-0 origin-left transition-transform duration-300 group-hover:scale-x-100"></span>
                            <span class="relative z-10">{{ $category }}</span>
                        </button>
                    @endforeach
                </div>

                <!-- Filtro de búsqueda -->
                <div class="mt-4 md:mt-0 relative flex items-center w-full md:w-96">
                    <input type="text" id="searchInput" placeholder="Buscar productos..."
                           class="w-full px-4 py-2 pl-12 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-300 transition">
                    <svg class="absolute left-4 text-gray-500 dark:text-gray-400 w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16.5 10.5a6 6 0 11-12 0 6 6 0 0112 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Lista de productos -->
        <div id="productos-container" class="container mx-auto mt-8 px-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
            @forelse ($productos as $producto)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md dark:shadow-lg hover:shadow-lg dark:hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ $producto->imagenes[0] ?? 'https://via.placeholder.com/150' }}" alt="Imagen del producto" class="w-full h-40 object-cover rounded-t-lg">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg text-gray-900 dark:text-white">{{ $producto->nombre }}</h3>
                        <p class="text-gray-500 dark:text-gray-300 text-sm">{{ $producto->categoria }}</p>
                        <p class="text-gray-900 dark:text-gray-200 font-semibold mt-2">{{ $producto->precio }} €</p>
                    </div>
                    <div class="p-4 flex justify-center items-center">
                        <a href="{{ route('producto.show', $producto->guid) }}" class="relative inline-block bg-[#BFF205] text-black font-semibold py-2 px-6 rounded-md transform transition-all duration-300 hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#BFF205] focus:ring-opacity-50">
                            Ver detalles
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-gray-600 dark:text-gray-400 col-span-5">No hay productos disponibles.</p>
            @endforelse
        </div>
    </div>

    <!-- Pie de página -->
    <x-footer />
@endsection
