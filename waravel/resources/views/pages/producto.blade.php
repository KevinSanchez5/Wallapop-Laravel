@extends('layouts.app')

@section('title', 'Detalles del Producto')

@section('content')
    <x-header />

    <div class="container mx-auto py-6">
        <!-- Detalles del producto -->
        <div class="bg-white shadow-lg rounded-xl p-6">
            <div class="flex flex-col md:flex-row">
                <div class="md:w-1/3">
                    @if (!empty($producto->imagenes[0]))
                        <img src="{{ asset('storage/' . $producto->imagenes[0]) }}" alt="Imagen del producto" class="w-full h-60 object-cover rounded-lg">
                    @else
                        <div class="w-full h-60 bg-gray-300 flex items-center justify-center rounded-lg">
                            <span class="text-white">Sin Imagen</span>
                        </div>
                    @endif
                </div>

                <div class="md:w-2/3 md:ml-8 mt-4 md:mt-0">
                    <h3 class="font-semibold text-2xl">{{ $producto->nombre }}</h3>
                    <p class="text-gray-500 text-sm">{{ $producto->categoria }}</p>
                    <p class="text-gray-700 text-base mt-2">{{ $producto->descripcion }}</p>
                    <p class="text-gray-500 text-sm mt-2">Estado: {{ $producto->estadoFisico }}</p>
                    <p class="text-gray-900 font-semibold text-xl mt-4">{{ $producto->precio }} €</p>

                    <div class="mt-6">
                        <a href="#" class="btn btn-success">Comprar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-footer />
@endsection
