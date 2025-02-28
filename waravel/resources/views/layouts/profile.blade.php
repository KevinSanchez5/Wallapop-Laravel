@php use App\Models\Valoracion; @endphp
@extends('layouts.app')

@section('title', "Perfil de {$cliente->nombre}")

@section('content')
    <x-header />

    <div class="container mx-auto py-6 px-6 flex flex-col md:flex-row gap-6">

        <div style="min-height: 600px" class="md:w-1/4">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">
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

                @php
                    $promedio = Valoracion::where('clienteValorado_id', $cliente->id)->avg('puntuacion') ?? 0;
                    $estrellasLlenas = round($promedio);
                    $estrellasVacias = 5 - $estrellasLlenas;
                @endphp

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
                            class="rounded-lg shadow-md"
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
            </div>

        <div class="w-full md:w-3/4 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            @yield('section', 'Valoraciones')
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
@endsection
