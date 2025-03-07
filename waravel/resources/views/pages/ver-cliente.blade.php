@extends('layouts.app')

@section('title', "Perfil de {$cliente->nombre}")

@section('content')
    <x-header />

    <!-- Notificación -->
    <div id="toast-success"
         class="opacity-0 hidden flex items-center w-full max-w-xs p-4 mb-4 text-gray-800 bg-[#BFF205] transition-opacity ease-in-out duration-700 shadow-sm border border-black"
         role="alert"
         style="position: fixed; top: 2rem; left: 50%; transform: translateX(-50%); border-radius: 20rem; z-index: 9999">
        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8">
            <svg class="w-10 h-10" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                 viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
            </svg>
            <span class="sr-only">Check icon</span>
        </div>
        <div class="ms-3 text-md font-normal ml-5">Artículo añadido a favoritos</div>
    </div>

    <div id="toast-failure"
         class="opacity-0 hidden flex items-center w-full max-w-xs p-4 mb-4 text-gray-800 bg-[#BFF205] transition-opacity ease-in-out duration-700 shadow-sm border border-black"
         role="alert"
         style="position: fixed; top: 2rem; left: 50%; transform: translateX(-50%); border-radius: 20rem; z-index: 9999">
        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8">
            <svg class="w-10 h-10" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.536 12.036a1 1 0 0 1-1.414 1.414L10 11.914l-2.122 2.036a1 1 0 0 1-1.414-1.414L8.586 10 6.464 7.878a1 1 0 0 1 1.414-1.414L10 8.586l2.122-2.122a1 1 0 1 1 1.414 1.414L11.414 10l2.122 2.036Z"/>
            </svg>
            <span class="sr-only">Error icon</span>
        </div>
        <div class="ms-3 text-md font-normal ml-5">Artículo eliminado de favoritos</div>
    </div>

    <div class="container mx-auto py-6 flex flex-col md:flex-row gap-6">
        <!-- Información del Cliente -->
        <div style="min-height: 600px" class="w-full md:w-1/4 bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">
            <div class="relative w-32 h-32 mx-auto">
                <img src="{{ asset('storage/' . ($cliente->avatar ?? 'clientes/default.jpg')) }}"
                     alt="Avatar de {{ $cliente->nombre }}"
                     class="w-32 h-32 rounded-full mx-auto shadow-md transition-transform duration-300 hover:scale-105">
            </div>

            <h2 class="text-2xl font-bold text-gray-800 dark:text-white text-center mt-4">
                {{ $cliente->nombre }} {{ $cliente->apellido }}
            </h2>

            <p class="text-gray-600 dark:text-gray-400 text-center mt-1">
                📞 {{ $cliente->telefono }}
            </p>

            <!-- Valoración -->
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

            <p class="text-gray-600 dark:text-gray-400 text-sm text-center mt-2">
                {{ number_format($promedio, 1) }} / 5
            </p>

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
                        src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBlxJ4a_HfUSAVljwVgN7NkwtBk4IGTX_A&q={{ urlencode($cliente->direccion->codigoPostal) }}">
                    </iframe>
                </div>
            @endif

            <br>
            <!-- Miembro de la comunidad -->
            <div class="flex items-center justify-center space-x-2 mt-4 animate-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <p class="text-gray-600 dark:text-gray-400 text-center font-bold">
                    Miembro de nuestra comunidad desde:
                    <span class="font-normal">{{ $cliente->created_at->format('d/m/Y') }}</span>
                </p>
            </div>

        </div>

        <!-- Sección de Productos y Valoraciones -->
        <div class="w-full md:w-3/4 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
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

            <!-- Sección de Productos -->
            <div id="productos" class="seccion">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Productos en venta</h2>
                <ul>
                    @forelse ($productos as $producto)
                        <li class="p-4 border-b border-gray-300 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <img src="{{ asset('storage/' . ($producto->imagenes[0] ?? 'default.jpg')) }}"
                                     alt="{{ $producto->nombre }}"
                                     class="w-16 h-16 rounded-md object-cover shadow-md">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $producto->nombre }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400">Precio: €{{ number_format($producto->precio, 2) }}</p>
                                    <a href="{{ route('producto.show', $producto->guid) }}"
                                       class="text-blue-500 dark:text-blue-400 font-semibold hover:underline">Ver más</a>
                                </div>
                            </div>
                                @if(auth()->check() && auth()->user()->role === 'cliente')
                                    @php $isFavorite = false; @endphp
                                    @if($productosFavoritos)
                                        @foreach($productosFavoritos as $favorito)
                                            @if($favorito == $producto->id)
                                                @php $isFavorite = true; @endphp
                                            @endif
                                        @endforeach
                                    @endif
                                    <a href="#" onclick="toggleFavorite('{{ $producto->guid }}', '{{ auth()->user()->id }}'); return false"
                                       class="bg-white text-gray-800 font-semibold py-2 px-4 rounded-md hover:bg-gray-100 dark:bg-black dark:text-white dark:hover:bg-gray-900 transition duration-300 transform hover:scale-105 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5">
                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span id="favorite-text-{{ $producto->guid }}">
                                            {{ $isFavorite ? 'Eliminar de Favoritos' : 'Añadir a Favoritos' }}
                                        </span>
                                    </a>
                                @endif
                            </li>
                        @empty
                            <li class="text-gray-500 dark:text-gray-400">No hay productos en venta.</li>
                        @endforelse
                    @endisset
                </ul>

                <!-- Paginación de productos -->
                <div class="mt-4">
                    {{ $productos->links() }}
                </div>
            </div>

            <!-- Sección de Valoraciones -->
            <div id="valoraciones" class="seccion hidden">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Valoraciones</h2>
                <ul>
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

                <!-- Paginación de valoraciones -->
                <div class="mt-4">
                    {{ $valoraciones->links() }}
                </div>
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

        async function toggleFavorite(productoGuid, userId) {
            const textElement = document.getElementById(`favorite-text-${productoGuid}`);
            const isFavorite = textElement.textContent.trim() === "Eliminar de Favoritos";
            const route = isFavorite ? "{{ route('favorito.eliminar') }}" : "{{ route('favorito.añadir') }}";
            const method = isFavorite ? "DELETE" : "POST";

            try {
                let response = await fetch(route, {
                    method: method,
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    body: JSON.stringify({ productoGuid, userId })
                });

                let data = await response.json();

                if (data.status === 200) {
                    textElement.textContent = isFavorite ? "Añadir a Favoritos" : "Eliminar de Favoritos";

                    if (isFavorite) {
                        showFailureNotification();
                    } else {
                        showSuccessNotification();
                    }
                }
            } catch (error) {
                console.error(error);
            }
        }

        function showSuccessNotification() {
            let toast = document.getElementById('toast-success');

            toast.classList.remove("hidden");
            toast.classList.remove("opacity-0");
            toast.classList.add("opacity-100");

            setTimeout(() => {
                toast.classList.remove("opacity-100");
                toast.classList.add("opacity-0");

                setTimeout(() => {
                    toast.classList.add("hidden");
                }, 500);
            }, 3000)
        }

        function showFailureNotification() {
            let toast = document.getElementById('toast-failure');

            toast.classList.remove("hidden");
            toast.classList.remove("opacity-0");
            toast.classList.add("opacity-100");

            setTimeout(() => {
                toast.classList.remove("opacity-100");
                toast.classList.add("opacity-0");

                setTimeout(() => {
                    toast.classList.add("hidden");
                }, 500);
            }, 3000)
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
