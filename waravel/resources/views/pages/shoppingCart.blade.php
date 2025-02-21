@extends('layouts.app')

@section('title', 'Carrito')

@section('content')
    <!-- Este es el header que cambia según el modo -->
    <script>
        function removeFromCart(productId) {
            fetch("{{route('carrito.remove')}}", {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                body: JSON.stringify({
                    productId: productId,
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        console.log('Error removing product');
                    }
                })
                .catch(error => {
                    console.log('Error:', error);
                });
        }
    </script>

    <x-header />
    <div class="container mx-auto pt-2 min-h-screen">
        <!-- Linea de venta -->
        <section class=" py-8 antialiased">
            <div class="mx-auto px-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl transition-all duration-300">Carrito de compra</h2>

                <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
                    <div class="mx-auto w-full flex-none lg:max-w-2xl xl:max-w-4xl bg-white dark:bg-gray-800 rounded-lg shadow-lg transition-all duration-300">
                        <div class="space-y-3">
                            @forelse($cart->lineasCarrito as $linea)
                                <div class="rounded-lg p-4 md:p-6">
                                    <div class="space-y-4 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0">
                                        <a href="{{ route('producto.show', $linea->producto->guid) }}" class="shrink-0 md:order-1">
                                            <img class="h-20 w-20" style="border-radius: 0.5rem" src=" {{asset('storage/productos/' . $linea->producto->imagenes[0])}}" alt="image" />
                                        </a>

                                        <label for="counter-input" class="sr-only">Elegir cantidad:</label>
                                        <div class="flex items-center justify-between md:order-3 md:justify-end">
                                            <div class="flex items-center">
                                                <button type="button" id="decrement-button" data-input-counter-decrement="counter-input" class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                                    <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                    </svg>
                                                </button>
                                                <input type="text" id="counter-input" data-input-counter class="w-10 shrink-0 border-0 bg-transparent text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-0 dark:text-white" placeholder="" value=" {{ $linea->cantidad }}" required />
                                                <button type="button" id="increment-button" data-input-counter-increment="counter-input" class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                                    <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="text-end md:order-4 md:w-32">
                                                <p class="text-base font-bold text-gray-900 dark:text-white">
                                                    {{ $linea->precioTotal }} €
                                                </p>
                                            </div>
                                        </div>

                                        <div class="w-full min-w-0 flex-1 space-y-4 md:order-2 md:max-w-md">
                                            <a href="{{ route('producto.show', $linea->producto->guid) }}" class="text-base font-medium text-gray-900 hover:underline dark:text-white"> {{ $linea->producto->nombre }}</a>

                                            <div class="flex items-center gap-4">
                                                <button onclick="removeFromCart({{ $linea->producto->id }})" type="button" class="inline-flex items-center text-sm font-medium text-red-600 hover:underline dark:text-red-500">
                                                    <svg class="me-1.5 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                                                    </svg>
                                                    Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-base font-normal text-gray-500 dark:text-gray-400 text-center" style="height: 10rem; line-height: 10rem">
                                    No hay elementos en el carrito
                                </p>
                            @endforelse
                        </div>
                    </div>

                    <div class="mx-auto mt-6 max-w-4xl flex-1 space-y-6 lg:mt-0 lg:w-full">
                        <div class="space-y-4 rounded-lg bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 sm:p-6 transition-all duration-300">
                            <p class="text-xl font-semibold text-gray-900 dark:text-white">Pedido</p>

                            <div class="space-y-4">
                                <div class="space-y-2">
                                    <dl class="flex items-center justify-between gap-4">
                                        <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Precio</dt>
                                        <dd class="text-base font-medium text-gray-900 dark:text-white">{{ $cart->precioTotal }} €</dd>
                                    </dl>
                                </div>

                                <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                                    <dt class="text-base font-bold text-gray-900 dark:text-white">Total</dt>
                                    <dd class="text-base font-bold text-gray-900 dark:text-white"> {{ $cart->precioTotal }} €</dd>
                                </dl>
                            </div>

                            <a href="#" class="block w-full bg-[#BFF205] text-black text-center font-medium py-2 px-6 rounded-md transition-transform duration-300 hover:scale-105 hover:shadow-lg">Continuar</a>

                            <div class="flex items-center justify-center gap-2">
                                <span class="text-sm font-normal text-gray-500 dark:text-gray-400"> o </span>
                                <a href="" title="" class="inline-flex items-center gap-2 text-sm font-medium text-primary-700 underline hover:no-underline dark:text-primary-500 dark:text-white">
                                    Continuar explorando
                                    <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        </div>
    <!-- Pie de página -->
    <x-footer />
@endsection
