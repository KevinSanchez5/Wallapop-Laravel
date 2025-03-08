@extends('layouts.profile')

@section('title', 'Mis Favoritos')

@section('section')
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

    <section>
        <div class="flex flex-wrap justify-center gap-4 mb-4">
            <button onclick="window.location.href='{{ route('profile') }}'"
                    class="px-4 py-2 rounded-lg text-black bg-[#82ae00] hover:bg-[#BFF205]">
                <b>Productos</b>
            </button>
            <button onclick="window.location.href='{{ route('profile.reviews') }}'"
                    class="px-4 py-2 rounded-lg text-black bg-[#82ae00] hover:bg-[#BFF205]">
                <b>Valoraciones</b>
            </button>
            <button onclick="window.location.href='{{ route('profile.orders') }}'"
                    class="px-4 py-2 rounded-lg text-black bg-[#82ae00] hover:bg-[#BFF205]">
                <b>Mis pedidos</b>
            </button>
            <button onclick="window.location.href='{{ route('profile.sales') }}'"
                    class="px-4 py-2 rounded-lg text-black bg-[#82ae00] hover:bg-[#BFF205]">
                <b>Mis ventas</b>
            </button>
            <button onclick="window.location.href='{{ route('profile.favorites') }}'"
                    class="px-4 py-2 rounded-lg text-black bg-[#BFF205] hover:bg-[#A0D500] border border-black dark:border-white">
                <b>Favoritos</b>
            </button>
        </div>
        <div id="favoritos" class="seccion">
            <div class="animate-fadeIn">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Productos favoritos</h2>
                </div>
            </div>
            <ul>
                @isset($productosFavoritos)
                    @forelse ($productosFavoritos as $producto)
                        @if (!$loop->last)
                            <li class="p-4 border-b border-gray-300 dark:border-gray-700 flex items-center justify-between gap-4">
                        @else
                            <li class="p-4 border-0 flex items-center justify-between gap-4">
                                @endif
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

                                <div class="flex items-center gap-2 w-full justify-end">
                                        <a href="#" onclick="toggleFavorite('{{ $producto->guid }}', '{{ auth()->user()->id }}'); return false"
                                           class="bg-white text-gray-800 font-semibold py-2 px-4 rounded-md hover:bg-gray-100 dark:bg-black dark:text-white dark:hover:bg-gray-900 transition duration-300 transform hover:scale-105 flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5">
                                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <span id="favorite-text-{{ $producto->guid }}">Eliminar de Favoritos</span>
                                        </a>
                                </div>
                            </li>
                            @empty
                                <li class="text-gray-500 dark:text-gray-400">No hay productos en venta.</li>
                            @endforelse
                        @endisset
            </ul>
        </div>
        <!-- Paginación -->
        @if ($productosFavoritos->hasPages())
            <div class="mt-4 mx-auto text-center">
                {{ $productosFavoritos->links('pagination::tailwind') }}
            </div>
        @endif
    </section>

    <script>
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
@endsection
