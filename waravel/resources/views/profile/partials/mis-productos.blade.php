@extends('layouts.profile')

@section('title', 'Mis Productos')

@section('section')
    <section>
        <div class="flex flex-wrap justify-center gap-4 mb-4">
            <button onclick="window.location.href='{{ route('profile.products') }}'"
                    class="px-4 py-2 rounded-lg text-black bg-[#BFF205] hover:bg-[#A0D500]">
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

                            <span class="inline-flex items-center justify-center px-4 py-2 rounded-full text-white
                                    @if($producto->estado == 'Disponible') bg-green-500
                                    @elseif($producto->estado == 'Vendido') bg-red-500
                                    @elseif($producto->estado == 'Desactivado') bg-gray-500
                                    @elseif($producto->estado == 'Baneado') bg-purple-500
                                    @endif w-32 text-center mr-25">
                                    {{ $producto->estado }}
                                </span>

                            <div class="flex items-center gap-2">
                                //si el estado del producto es baneado no sale lo siguiente
                                @if($producto->estado == 'Baneado')
                                    <span class="inline-flex items-center justify-center px-4 py-2 rounded-full text-white bg-gray-500 w-32 text-center mr-25">
                                        Baneado
                                    </span>
                                @else
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
