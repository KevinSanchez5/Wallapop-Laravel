@extends('layouts.profile')

@section('title', 'Mis Productos')

@section('section')
    <section>
        <div class="flex flex-wrap justify-center gap-4 mb-4">
            <button onclick="window.location.href='{{ route('profile.products') }}'"
                    class="px-4 py-2 rounded-lg text-black bg-[#BFF205] hover:bg-[#A0D500] border border-black dark:border-white">
                <b>Productos</b>
            </button>
            <button onclick="window.location.href='{{ route('profile.reviews') }}'"
                    class="px-4 py-2 rounded-lg text-black bg-[#A0D500] hover:bg-[#BFF205]">
                <b>Valoraciones</b>
            </button>
            <button onclick="window.location.href='{{ route('profile.orders') }}'"
                    class="px-4 py-2 rounded-lg text-black bg-[#A0D500] hover:bg-[#BFF205]">
                <b>Mis pedidos</b>
            </button>
            <button onclick="window.location.href='{{ route('profile.sales') }}'"
                    class="px-4 py-2 rounded-lg text-black bg-[#A0D500] hover:bg-[#BFF205]">
                <b>Mis ventas</b>
            </button>
            <button onclick="window.location.href='{{ route('profile.favorites') }}'"
                    class="px-4 py-2 rounded-lg text-black bg-[#A0D500] hover:bg-[#BFF205]">
                <b>Favoritos</b>
            </button>
        </div>
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

                            @php
                                $estadoClasses = [
                                    'Disponible' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'Vendido' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                    'Desactivado' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    'Baneado' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300'
                                ];

                                $iconPaths = [
                                    'Disponible' => '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />',
                                    'Vendido' => '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />',
                                    'Desactivado' => '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 5H7v14h3V5zm7 0h-3v14h3V5z" />',
                                    'Baneado' => '<circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" /><path stroke="currentColor" stroke-width="2" d="M4.93 4.93l14.14 14.14" />'
                                ];
                            @endphp

                            <dd class="me-2 mt-1.5 inline-flex items-center rounded px-2.5 py-0.5 text-xs font-medium {{ $estadoClasses[$producto->estado] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                <svg class="me-1 h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    {!! $iconPaths[$producto->estado] ?? '' !!}
                                </svg>
                                {{ $producto->estado }}
                            </dd>

                            <div class="flex items-center gap-2">
                                @if($producto->estado != 'Baneado')
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
                                @endif

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
        <!-- Paginación -->
        @if ($productos->hasPages())
            <div class="mt-4 mx-auto text-center">
                {{ $productos->links('pagination::tailwind') }}
            </div>
        @endif
    </section>
@endsection
