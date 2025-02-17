@extends('layouts.app')

@section('title', 'Página de Inicio')

@section('content')
    <!-- Este es el carousel de la parte superior -->
    <x-carousel />

    <div class="container mx-auto mt-8 px-4">
        <div class="bg-white shadow-lg rounded-xl p-6 flex flex-col md:flex-row md:justify-between md:items-center transition-all duration-300">

            <div class="flex flex-wrap gap-3">
                <!-- Aquí poner las categorias que tengamos -->
                @php
                    $categories = ['Electrónica', 'Ropa', 'Hogar', 'Juguetes', 'Automóviles'];
                @endphp
                @foreach ($categories as $category)
                    <button class="relative overflow-hidden bg-gray-100 px-5 py-2 rounded-lg text-gray-700 font-semibold
                        transition-all duration-300 hover:text-white group">
                        <span class="absolute inset-0 bg-[#BFF205] scale-x-0 origin-left transition-transform duration-300 group-hover:scale-x-100"></span>
                        <span class="relative z-10">{{ $category }}</span>
                    </button>
                @endforeach
            </div>

            <!-- Aquí aremos un filter según lo que pnga el cliente actualizandose OnChange -->
            <div class="mt-4 md:mt-0 relative flex items-center w-full md:w-96">
                <input type="text" placeholder="Buscar productos..."
                       class="w-full px-4 py-2 pl-12 rounded-lg border border-gray-300 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-300 transition">
                <svg class="absolute left-4 text-gray-500 w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16.5 10.5a6 6 0 11-12 0 6 6 0 0112 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Por la lista de productos que le pasemos los mostramos en estos divs ajustando lo q queremos mostrar como la imagen el nombre categoria... -->
    <!-- Hay que paginarlo :) -->
    <div class="container mx-auto mt-8 px-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
        @for ($i = 0; $i < 10; $i++)
            <div class="h-48 bg-gray-200 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300"></div>
        @endfor
    </div>
@endsection
