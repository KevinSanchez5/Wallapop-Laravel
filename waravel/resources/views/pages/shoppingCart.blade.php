@extends('layouts.app')

@section('title', 'Carrito')

@section('content')
    <script>
        async function removeFromCart(productId) {
            await fetch("{{route('carrito.remove')}}", {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                body: JSON.stringify({
                    productId: productId
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 200) {
                        deleteLineaCarrito(productId, data.itemAmount);
                        updateTotal(data.precioTotal);
                        updateCartLogo(data.itemAmount);
                    } else {
                        console.warn("Failed to update cart");
                    }
                })
                .catch(error => {
                    console.log('Error:', error);
                });
        }

        async function removeOneOrDeleteIt(productId) {
            disableButtons(productId);
            showSpinner(productId);
            await fetch("{{route('carrito.removeOne')}}", {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                body: JSON.stringify({
                    productId: productId
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 200) {
                        if (data.deletedTheItem){
                            deleteLineaCarrito(productId, data.itemAmount);
                        }else{
                            removeOneFromTheCounter(productId);
                            updateCurrentLineaPrice(data.lineaPrice, productId)
                        }
                        updateTotal(data.precioTotal);
                        updateCartLogo(data.itemAmount);
                    } else {
                        console.warn("Failed to update cart:", data);
                    }
                })
                .catch(error => {
                    console.log('Error:', error);
                });
            hideSpinner(productId);
            enableButtons(productId);
        }

        async function addOne(productId) {
            disableButtons(productId);
            showSpinner(productId);
            await fetch("{{route('carrito.addOne')}}", {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                body: JSON.stringify({
                    productId: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 200) {
                    updateTotal(data.precioTotal);
                    updateCartLogo(data.itemAmount);
                    addOneToTheCounter(productId);
                    updateCurrentLineaPrice(data.lineaPrice, productId)
                } else {
                    console.warn("Failed to update cart:", data);
                }
            })
            .catch(error => {
                console.log('Error:', error);
            })
            hideSpinner(productId);
            enableButtons(productId);
        }

        function showSpinner(productId) {
            const input = document.getElementById(`amount_of_items_for_${productId}`);
            const spinner = document.getElementById(`spinner_${productId}`);

            if(spinner!= null && input != null){
                spinner.classList.remove("hidden");
                input.classList.add("hidden");
            }
        }

        function hideSpinner(productId) {
            const input = document.getElementById(`amount_of_items_for_${productId}`);
            const spinner = document.getElementById(`spinner_${productId}`);

            if(spinner!= null && input != null){
                input.classList.remove("hidden");
                spinner.classList.add("hidden");
            }
        }

        function disableButtons(productId) {
            const addOneButton = document.getElementById(`increment-button-for-${productId}`);
            const removeOneButton = document.getElementById(`decrement-button-for-${productId}`);

            if(addOneButton != null && removeOneButton != null){
                addOneButton.disabled = true;
                removeOneButton.disabled = true;
            }
        }

        function enableButtons(productId) {
            const addOneButton = document.getElementById(`increment-button-for-${productId}`);
            const removeOneButton = document.getElementById(`decrement-button-for-${productId}`);

            if(addOneButton != null && removeOneButton != null){
                addOneButton.disabled = false;
                removeOneButton.disabled = false;
            }

        }
        function deleteLineaCarrito(productId, itemsLeft) {
            const linea = document.getElementById(`linea-${productId}`);
            const parent = linea.parentNode;
            parent.removeChild(linea);
            if (itemsLeft === 0) {
                parent.innerHTML = "<p class='text-base font-normal text-gray-500 dark:text-gray-400 text-center' style='height: 10rem; line-height: 10rem'>No hay productos en el carrito </p>"
            }
        }

        function updateTotal(newTotal) {
            const totalPrice = document.getElementById('totalPrice');
            const finalTotal = document.getElementById('finalTotal');

            totalPrice.innerHTML = newTotal + " €";
            finalTotal.innerHTML = newTotal + " €";
        }

        function updateCartLogo(amountOfItems) {
            const cartLogo = document.getElementById('itemCount');
            cartLogo.innerHTML = amountOfItems;
        }

        function addOneToTheCounter(productId){
            const input = document.getElementById("amount_of_items_for_" + productId);
            const currentValue = parseInt(input.value);
            input.value = currentValue + 1;
        }

        function removeOneFromTheCounter(productId){
            const input = document.getElementById("amount_of_items_for_" + productId);
            const currentValue = parseInt(input.value);
            if(currentValue > 0){
                input.value = currentValue - 1;
            }
        }

        function updateCurrentLineaPrice(newPrice, productId){
            const priceElement = document.getElementById(productId + '_total');
            priceElement.innerHTML = newPrice + " €";
        }
    </script>

    <x-header />
    <div class="container mx-auto pt-2 min-h-screen">
        <!-- Linea de venta -->
        <section class=" py-8 antialiased">
            <div class="mx-auto px-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl transition-all duration-300">Carrito de compra</h2>

                <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
                    <div class="mx-auto w-full flex-none lg:max-w-2xl xl:max-w-4xl bg-white dark:bg-gray-800 rounded-lg shadow-lg transition-all duration-300">
                        <div class="space-y-0">
                            @forelse($cart->lineasCarrito as $linea)
                                <div id="linea-{{$linea->producto->guid}}" class="rounded-lg p-4 md:p-6">
                                    <div class="space-y-4 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0">
                                        <a href="{{ route('producto.show', $linea->producto->guid) }}" class="shrink-0 md:order-1">
                                            <img class="h-20 w-20" style="border-radius: 0.5rem" src=" {{asset('storage/' . $linea->producto->imagenes[0])}}" alt="image" />
                                        </a>

                                        <label for="counter-input" class="sr-only">Elegir cantidad:</label>
                                        <div class="flex items-center justify-between md:order-3 md:justify-end">
                                            <div class="flex items-center">
                                                <button onclick="removeOneOrDeleteIt('{{$linea->producto->guid}}')" type="button" id="decrement-button-for-{{ $linea->producto->guid }}" data-input-counter-decrement="counter-input" class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                                    <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                    </svg>
                                                </button>
                                                <input disabled id="amount_of_items_for_{{ $linea->producto->guid }}" type="text" data-input-counter class="w-10 shrink-0 border-0 bg-transparent text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-0 dark:text-white" placeholder="" value="{{ $linea->cantidad }}" required />
                                                <div id="spinner_{{ $linea->producto->guid }}" class="hidden inset-0 flex items-center justify-center" style="margin-left: 0.75rem; margin-right: 0.75rem">
                                                    <svg class="h-4 w-4 animate-spin text-gray-500" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="12" cy="12" r="10" stroke="#111827" stroke-width="4"></circle>
                                                        <path d="M2 12a10 10 0 0110-10" stroke="#BFF205" stroke-width="4"></path>
                                                    </svg>
                                                </div>
                                                <button onclick="addOne('{{ $linea->producto->guid }}')" type="button" id="increment-button-for-{{ $linea->producto->guid }}" data-input-counter-increment="counter-input" class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                                    <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="text-end md:order-4 md:w-32">
                                                <p id="{{ $linea->producto->guid}}_total" class="text-base font-bold text-gray-900 dark:text-white">
                                                    {{ $linea->precioTotal }} €
                                                </p>
                                            </div>
                                        </div>

                                        <div class="w-full min-w-0 flex-1 space-y-4 md:order-2 md:max-w-md">
                                            <a href="{{ route('producto.show', $linea->producto->guid) }}" class="text-base font-medium text-gray-900 hover:underline dark:text-white"> {{ $linea->producto->nombre }}</a>

                                            <div class="flex items-center gap-4">
                                                <button id="delete-button-for-{{$linea->producto->guid}}" onclick="removeFromCart('{{ $linea->producto->guid }}')" type="button" class="inline-flex items-center text-sm font-medium text-red-600 hover:underline dark:text-red-500">
                                                    <svg class="me-1.5 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                                                    </svg>
                                                    Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @if(!$loop->last)
                                <hr class="my-12 h-0.5 border-t-0 border-t-0 border-gray-200 dark:border-gray-700" style="margin-left: 1.5rem; width: calc(100% - 3rem); margin-top: 0"/>
                            @endif
                            @empty
                                <p class="text-base font-normal text-gray-500 dark:text-gray-400 text-center" style="height: 10rem; line-height: 10rem">
                                    No hay productos en el carrito
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
                                        <dd id="totalPrice" class="text-base font-medium text-gray-500 dark:text-gray-400">{{ $cart->precioTotal }} €</dd>
                                    </dl>
                                </div>

                                <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                                    <dt class="text-base font-bold text-gray-900 dark:text-white">Total</dt>
                                    <dd id="finalTotal" class="text-base font-bold text-gray-900 dark:text-white"> {{ $cart->precioTotal }} €</dd>
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
