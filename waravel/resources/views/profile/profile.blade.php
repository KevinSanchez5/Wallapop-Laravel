@php
    use App\Models\Valoracion;
@endphp

@extends('layouts.app')

@section('title', "Perfil de {$cliente->nombre}")

@section('content')
    <x-header />

    <div class="container mx-auto py-6 flex gap-6">
        <!-- Información del Cliente -->
        <div class="flex-shrink-0 w-auto bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">
            <!-- Avatar con efecto hover -->
            <div class="relative w-32 h-32 mx-auto">
                <img src="{{ asset('storage/' . ($cliente->avatar ?? 'clientes/default.jpg')) }}"
                     alt="Avatar de {{ $cliente->nombre }}"
                     class="w-32 h-32 rounded-full mx-auto shadow-md transition-transform duration-300 hover:scale-105">
            </div>

            <!-- Nombre del Cliente -->
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white text-center mt-4">
                {{ $cliente->nombre }} {{ $cliente->apellido }}
            </h2>

            <!-- Teléfono -->
            <p class="text-gray-600 dark:text-gray-400 text-center mt-1">
                📞 {{ $cliente->telefono }}
            </p>

            <!-- Cálculo de estrellas -->
            @php
                $promedio = Valoracion::where('clienteValorado_id', $cliente->id)->avg('puntuacion') ?? 0;
                $estrellasLlenas = round($promedio);
                $estrellasVacias = 5 - $estrellasLlenas;
            @endphp

                <!-- Estrellas -->
            <div class="text-center mt-4 flex justify-center space-x-1">
                @for ($i = 0; $i < $estrellasLlenas; $i++)
                    <svg class="w-7 h-7 text-yellow-500 animate-fadeIn" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 17.3l-5.4 3.4 1.4-6-4.6-4 6.1-.5L12 4l2.5 6.1 6.1.5-4.6 4 1.4 6z"/>
                    </svg>
                @endfor
                @for ($i = 0; $i < $estrellasVacias; $i++)
                    <svg class="w-7 h-7 text-gray-400 animate-fadeIn opacity-80" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 17.3l-5.4 3.4 1.4-6-4.6-4 6.1-.5L12 4l2.5 6.1 6.1.5-4.6 4 1.4 6z"/>
                    </svg>
                @endfor
            </div>

            <!-- Puntuación numérica -->
            <p class="text-gray-600 dark:text-gray-400 text-sm text-center mt-2">
                {{ number_format($promedio, 1) }} / 5
            </p>

            <!-- Dirección y mapa -->
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

            <!-- Botón de Editar Perfil -->
            <div class="text-center mt-6">
                <a href="{{ route('profile.edit', $cliente->guid) }}"
                   class="px-6 py-3 rounded-lg text-gray-800 bg-[#BFF205] hover:bg-[#A0D500] focus:outline-none focus:ring-2 focus:ring-[#A0D500] transition duration-300">
                    <b>Editar perfil</b>
                </a>
            </div>
        </div>

        <div class="flex-grow bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <!-- Botones -->
            <div class="flex justify-center mb-4">
                <button onclick="mostrarSeccion('productos')"
                        class="px-4 py-2 rounded-lg text-black bg-[#BFF205] hover:bg-[#A0D500]">
                    <b>Productos</b>
                </button>
                <button onclick="mostrarSeccion('valoraciones')"
                        class="ml-2 px-4 py-2 rounded-lg text-black bg-[#BFF205] hover:bg-[#A0D500]">
                    <b>Valoraciones</b>
                </button>
            </div>

            <!-- Productos -->
            <div id="productos" class="seccion">
                <div class="animate-fadeIn">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Productos en venta</h2>
                        <a href="{{ route('producto.add') }}" class="p-2 bg-[#BFF205] text-black rounded-md hover:bg-[#A0D500] transition duration-300 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Añadir Producto
                        </a>
                    </div>
                </div>
                <ul>
                    @isset($productos)
                        @forelse ($productos as $producto)
                            <li class="p-4 border-b border-gray-300 dark:border-gray-700 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-4 w-full">
                                    <img src="{{ asset('storage/' . ($producto->imagenes[0] ?? 'default.jpg')) }}"
                                         alt="{{ $producto->nombre }}"
                                         class="w-16 h-16 rounded-md object-cover shadow-md">
                                    <div class="flex flex-col">
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $producto->nombre }}</h3>
                                        <p class="text-gray-600 dark:text-gray-400">Precio: €{{ number_format($producto->precio, 2) }}</p>
                                        <a href="{{ route('producto.show', $producto->guid) }}"
                                           class="text-blue-500 dark:text-blue-400 font-semibold hover:underline">Ver más</a>
                                    </div>
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
                    @foreach ($valoraciones as $valoracion)
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
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <x-footer />

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

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-in-out;
        }
    </style>
@endsection
