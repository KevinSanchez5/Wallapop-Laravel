@php use App\Models\Valoracion; @endphp
@extends('layouts.app')

@section('title', "Perfil de {$cliente->nombre}")

@section('content')
    <div style="margin-top: -25px">
        <x-header />

        <div class="container mx-auto py-6 flex gap-6">
            <!-- Información del Cliente -->
            <div  style="min-height: 600px" class="flex-shrink-0 w-auto bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                <img src="{{ asset('storage/avatares/' . ($cliente->avatar ?? 'default.jpg')) }}"
                     alt="Avatar de {{ $cliente->nombre }}"
                     class="w-32 h-32 rounded-full mx-auto">

                <h2 class="text-xl font-semibold text-gray-800 dark:text-white text-center mt-4">
                    {{ $cliente->nombre }} {{ $cliente->apellido }}
                </h2>

                <p class="text-gray-600 dark:text-gray-400 text-center">
                    📞 {{ $cliente->telefono }}
                </p><br>

                @php
                    $promedio = Valoracion::where('clienteValorado_id', $cliente->id)->avg('puntuacion') ?? 0;
                    $estrellas = str_repeat('⭐', round($promedio));
                @endphp

                <div class="text-center mt-2">
                    <span class="text-yellow-500">{{ $estrellas }}</span>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        {{ number_format($promedio, 1) }} / 5
                    </p>
                </div><br>

                @if(isset($cliente->direccion->codigoPostal))
                    <p class="text-gray-600 dark:text-gray-400 text-center mt-2">
                        📍 Código Postal: {{ $cliente->direccion->codigoPostal }}
                    </p>
                    <div class="mt-4">
                        <iframe
                            width="100%"
                            height="250"
                            class="rounded-lg shadow-lg"
                            style="border:0;"
                            loading="lazy"
                            allowfullscreen
                            referrerpolicy="no-referrer-when-downgrade"
                            src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBlxJ4a_HfUSAVljwVgN7NkwtBk4IGTX_A&q={{ urlencode($cliente->direccion->codigoPostal) }},ES">
                        </iframe>
                    </div>
                @endif
            </div>

            <div class="flex-grow bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                <!-- Botones -->
                <div class="flex justify-center mb-4">
                    <button onclick="mostrarSeccion('productos')"
                            class="px-4 py-2 rounded-lg text-white bg-[#BFF205] hover:bg-[#A0D500]">
                        Productos
                    </button>
                    <button onclick="mostrarSeccion('valoraciones')"
                            class="ml-2 px-4 py-2 rounded-lg text-white bg-[#BFF205] hover:bg-[#A0D500]">
                        Valoraciones
                    </button>
                </div>

                <!-- Productos -->
                <div id="productos" class="seccion">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Productos en venta</h2>
                    <ul>
                        @isset($productos)
                            @forelse ($productos as $producto)
                                <li class="p-4 border-b border-gray-300 dark:border-gray-700 flex items-center gap-4">
                                    <img src="{{ asset('storage/productos/' . ($producto->imagenes[0] ?? 'default.jpg')) }}"
                                         alt="{{ $producto->nombre }}"
                                         class="w-16 h-16 rounded-md object-cover shadow-md">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $producto->nombre }}</h3>
                                        <p class="text-gray-600 dark:text-gray-400">Precio: €{{ number_format($producto->precio, 2) }}</p>
                                        <a href="{{ route('producto.show', $producto->guid) }}"
                                           class="text-blue-500 dark:text-blue-400 font-semibold hover:underline">Ver más</a>
                                    </div>
                                </li>
                            @empty
                                <li class="text-gray-500 dark:text-gray-400">No hay productos en venta.</li>
                            @endforelse
                        @endisset
                    </ul>
                </div>

                <!-- Valoraciones -->
                <div id="valoraciones" class="seccion hidden">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Valoraciones</h2>
                    <ul>
                        @php
                            // Cargar las valoraciones con el creador de la valoración (autor)
                            $valoraciones = Valoracion::with('creador')->where('clienteValorado_id', $cliente->id)->latest()->get();
                        @endphp
                        @forelse ($valoraciones as $valoracion)
                            <li class="p-4 border-b border-gray-300 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('cliente.ver', $valoracion->creador->guid) }}"
                                       class="text-blue-500 dark:text-blue-400 font-semibold hover:underline">
                                        {{ optional($valoracion->creador)->nombre ?? 'Usuario eliminado' }}
                                        {{ optional($valoracion->creador)->apellido ?? '' }}
                                    </a>
                                    <span class="text-yellow-500">{{ str_repeat('⭐', $valoracion->puntuacion) }}</span>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400">{{ $valoracion->comentario }}</p>
                                <p class="text-gray-500 text-sm mt-1">
                                    📅 {{ $valoracion->created_at->format('d/m/Y') }}
                                </p>
                            </li>
                        @empty
                            <li class="text-gray-500 dark:text-gray-400">No hay valoraciones.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <x-footer />
    </div>

    <script>
        function mostrarSeccion(seccion) {
            document.getElementById('productos').classList.add('hidden');
            document.getElementById('valoraciones').classList.add('hidden');

            document.getElementById(seccion).classList.remove('hidden');
        }
    </script>

    <style>
        .hidden {
            display: none;
        }
    </style>
@endsection
