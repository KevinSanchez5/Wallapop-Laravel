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
            <!-- Avatar y Nombre -->
            <div class="text-center">
                <div class="relative w-32 h-32 mx-auto">
                    <img src="{{ asset('storage/' . ($cliente->avatar ?? 'clientes/default.jpg')) }}"
                         alt="Avatar de {{ $cliente->nombre }}"
                         class="w-32 h-32 rounded-full mx-auto shadow-md transition-transform duration-300 hover:scale-105">
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mt-4">
                    {{ $cliente->nombre }} {{ $cliente->apellido }}
                </h2>
            </div>

            <!-- Valoración -->
            <div class="mt-6">
                <div class="text-center">
                    @php
                        $promedio = Valoracion::where('clienteValorado_id', $cliente->id)->avg('puntuacion') ?? 0;
                        $estrellasLlenas = round($promedio);
                        $estrellasVacias = 5 - $estrellasLlenas;
                    @endphp

                        <!-- Estrellas -->
                    <div class="flex justify-center space-x-1">
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
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">
                        {{ number_format($promedio, 1) }} / 5
                    </p>
                </div>
            </div>

            <!-- Información del Cliente -->
            <div class="mt-6 space-y-4">
                <!-- Correo Electrónico -->
                <div class="flex items-center space-x-3">
                    <span class="text-gray-600 dark:text-gray-400">📧</span>
                    <p class="text-gray-800 dark:text-gray-200">{{ Auth::user()->email }}</p>
                </div>

                <!-- Teléfono -->
                <div class="flex items-center space-x-3">
                    <span class="text-gray-600 dark:text-gray-400">📞</span>
                    <p class="text-gray-800 dark:text-gray-200">{{ $cliente->telefono }}</p>
                </div>

                <!-- Fecha de Registro -->
                <div class="flex items-center space-x-3">
                    <span class="text-gray-600 dark:text-gray-400">🗓️</span>
                    <p class="text-gray-800 dark:text-gray-200">Se unió el {{ $cliente->created_at->format('d/m/Y') }}</p>
                </div>

                <!-- Dirección Completa -->
                @if(isset($cliente->direccion))
                    <div class="flex items-start space-x-3">
                        <span class="text-gray-600 dark:text-gray-400">📍</span>
                        <div>
                            <p class="text-gray-800 dark:text-gray-200">{{ $cliente->direccion->calle }} {{ $cliente->direccion->numero }}</p>
                            <p class="text-gray-800 dark:text-gray-200">{{ $cliente->direccion->codigoPostal }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Mapa (si hay código postal) -->
            @if(isset($cliente->direccion->codigoPostal))
                <div class="mt-6">
                    <iframe
                        width="100%"
                        height="200"
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
            <div class="mt-6 text-center">
                <a href="{{ route('profile.edit') }}" class="px-6 py-3 rounded-lg text-gray-800 bg-[#BFF205] hover:bg-[#A0D500] focus:outline-none focus:ring-2 focus:ring-[#A0D500] transition duration-300">
                    <b>Editar perfil</b>
                </a>
            </div>
        </div>

        <!-- Sección de Valoraciones y Productos -->
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

                                <span class="inline-flex items-center justify-center px-4 py-2 rounded-full text-white
                                    @if($producto->estado == 'Disponible') bg-green-500
                                    @elseif($producto->estado == 'Vendido') bg-red-500
                                    @elseif($producto->estado == 'Desactivado') bg-gray-500
                                    @elseif($producto->estado == 'Baneado') bg-purple-500
                                    @endif w-32 text-center mr-25">
                                    {{ $producto->estado }}
                                </span>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('producto.edit', $producto->guid) }}" class="p-2 bg-[#BFF205] text-black rounded-md hover:bg-[#A0D500] transition duration-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5">
                                            <path d="M12 20h9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4L16.5 3.5z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>

                                    <form action="{{ route('producto.changestatus', $producto->guid) }}" method="POST" id="deactivateForm">
                                        @csrf
                                        @method('POST')
                                        <button type="button" onclick="showToast('toast-confirm-deactivate')" class="p-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 transition duration-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5">
                                                <path d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                    </form>

                                    <form action="{{ route('producto.destroy', $producto->guid) }}" method="POST" id="deleteForm">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="showToast('toast-confirm-delete')" class="p-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition duration-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5">
                                                <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                    </form>
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

    <!-- Toast de Confirmación para Eliminar Producto -->
    <div id="toast-confirm-delete" class="opacity-0 hidden flex items-center w-full max-w-xs p-4 mb-4 text-gray-800 bg-[#BFF205] transition-opacity ease-in-out duration-700 shadow-sm" role="alert" style="position: fixed; top: 2rem; left: 50%; transform: translateX(-50%); border-radius: 20rem; z-index: 9999">
        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8">
            <span class="sr-only">Check icon</span>
        </div>
        <div class="ms-3 text-md font-normal ml-5">¿Estás seguro de eliminar este producto?</div>
        <button type="button" onclick="confirmDelete()" class="ml-4 bg-[#A0D500] text-black px-3 py-1 rounded-md">Sí</button>
        <button type="button" onclick="hideToast('toast-confirm-delete')" class="ml-2 bg-gray-300 text-black px-3 py-1 rounded-md">No</button>
    </div>

    <!-- Toast de Confirmación para Desactivar Producto -->
    <div id="toast-confirm-deactivate" class="opacity-0 hidden flex items-center w-full max-w-xs p-4 mb-4 text-gray-800 bg-[#BFF205] transition-opacity ease-in-out duration-700 shadow-sm" role="alert" style="position: fixed; top: 2rem; left: 50%; transform: translateX(-50%); border-radius: 20rem; z-index: 9999">
        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8">
            <span class="sr-only">Check icon</span>
        </div>
        <div class="ms-3 text-md font-normal ml-5">¿Estás seguro de cambiar el estado de este producto?</div>
        <button type="button" onclick="confirmDeactivate()" class="ml-4 bg-[#A0D500] text-black px-3 py-1 rounded-md">Sí</button>
        <button type="button" onclick="hideToast('toast-confirm-deactivate')" class="ml-2 bg-gray-300 text-black px-3 py-1 rounded-md">No</button>
    </div>

    <x-footer />

    <script>
        function mostrarSeccion(seccion) {
            document.getElementById('productos').classList.add('hidden');
            document.getElementById('valoraciones').classList.add('hidden');
            document.getElementById(seccion).classList.remove('hidden');
        }

        function showToast(toastId) {
            const toast = document.getElementById(toastId);
            toast.classList.remove('hidden');
            toast.classList.remove('opacity-0');
            toast.classList.add('opacity-100');
        }

        function hideToast(toastId) {
            const toast = document.getElementById(toastId);
            toast.classList.remove('opacity-100');
            toast.classList.add('opacity-0');
            setTimeout(() => toast.classList.add('hidden'), 700);
        }

        function confirmDelete() {
            hideToast('toast-confirm-delete');
            document.getElementById('deleteForm').submit();
        }

        function confirmDeactivate() {
            hideToast('toast-confirm-deactivate');
            document.getElementById('deactivateForm').submit();
        }
    </script>

    <style>
        .hidden { display: none; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
    </style>
@endsection
