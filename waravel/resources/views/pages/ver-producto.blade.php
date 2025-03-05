@php use App\Models\Carrito; @endphp
@extends('layouts.app')

@section('title', 'Detalles del Producto')

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <x-header/>

    <!-- Notificación -->
    <div id="toast-success"
         class="opacity-0 hidden flex items-center w-full max-w-xs p-4 mb-4 text-gray-800 bg-[#BFF205] transition-opacity ease-in-out duration-700 shadow-sm"
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
        <div class="ms-3 text-md font-normal ml-5">Artículo añadido al carrito.</div>
    </div>

    <div class="container mx-auto px-4 py-10">
        <!-- Contenedor principal con fondo claro/oscuro -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-5xl mx-auto">

            <!-- Carrusel  arriba -->
            <div x-data="{ open: false, imgSrc: '' }">
                <div class="swiper w-full h-96 mb-6">

                    <div class="swiper-wrapper">
                        @foreach ($producto->imagenes as $imagen)
                            <div class="swiper-slide">
                                <img src="{{ asset('storage/' . $imagen) }}"
                                     alt="Imagen del producto"
                                     class="w-full h-full object-cover rounded-lg shadow-lg transition-transform transform hover:scale-105 duration-300 cursor-pointer"
                                     @click="open = true; imgSrc = '{{ asset('storage/' . $imagen) }}'">
                            </div>
                        @endforeach

                        @if (empty($producto->imagenes) || count($producto->imagenes) === 0)
                            <div class="swiper-slide">
                                <div
                                    class="w-full h-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center rounded-lg shadow-lg">
                                    <span class="text-white text-lg font-semibold">Sin Imagen</span>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>

                <!-- Modal de imagen ampliada -->
                <div x-show="open" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
                     @click.away="open = false">
                    <img :src="imgSrc" class="max-w-4xl max-h-screen rounded-lg shadow-lg">
                    <button class="absolute top-5 right-5 text-white text-3xl font-bold" @click="open = false">&times;
                    </button>
                </div>
            </div>

            <!-- Contenido del producto debajo del carrusel -->
            <h3 class="font-semibold text-2xl text-gray-800 dark:text-white text-center md:text-left">
                {{ $producto->nombre }}
            </h3>
            <p class="text-gray-500 dark:text-gray-300 text-sm text-center md:text-left">
                {{ $producto->categoria }}
            </p>
            <div class="mt-6 text-center md:text-left flex">
                <p class="text-gray-600 dark:text-gray-400 text-sm"><b>Vendido por:&nbsp;</b></p>
                <a href="{{ route('cliente.ver', $producto->vendedor->guid) }}"
                   class="text-blue-500 dark:text-blue-400 font-semibold hover:underline">
                    {{ $producto->vendedor->nombre }} {{ $producto->vendedor->apellido }}
                </a>
            </div>
            <p class="text-gray-700 dark:text-gray-200 text-base mt-4 text-center md:text-left">
                {{ $producto->descripcion }}
            </p>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-2 text-center md:text-left">
                Estado: {{ $producto->estadoFisico }}
            </p>

            @if(auth()->guest() || auth()->user()->role === 'cliente')
                <p class="text-black dark:text-white text-base mt-4 text-center md:text-left">
                    Cantidad:
                </p>
                <div class="flex items-center">
                    <button onclick="removeOne()" type="button" id="decrement-button"
                            data-input-counter-decrement="counter-input"
                            class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                        <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white" aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M1 1h16"/>
                        </svg>
                    </button>
                    <input disabled id="cantidad" type="text" data-input-counter
                           class="w-12 shrink-0 border-0 bg-transparent text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-0 dark:text-white"
                           placeholder="1" value="1" required/>
                    <button onclick="addOne()" type="button" id="increment-button"
                            data-input-counter-increment="counter-input"
                            class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                        <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white" aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 1v16M1 9h16"/>
                        </svg>
                    </button>
                </div>

                <p class="text-gray-900 dark:text-gray-100 font-semibold text-xl mt-4 text-center md:text-left">
                    {{ $producto->precio }} €
                </p>
            @endif

            @if(isset($producto->vendedor->direccion->codigoPostal))
                <div class="mt-4">
                    <p class="text-gray-600 dark:text-gray-400 text-sm text-center md:text-left">
                        📍 Ubicación del vendedor (Código Postal: {{ $producto->vendedor->direccion->codigoPostal }})
                    </p>
                    <iframe
                        width="100%"
                        height="250"
                        style="border:0; border-radius: 10px; margin-top: 8px;"
                        loading="lazy"
                        allowfullscreen
                        referrerpolicy="no-referrer-when-downgrade"
                        src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBlxJ4a_HfUSAVljwVgN7NkwtBk4IGTX_A&q={{ trim($producto->vendedor->direccion->codigoPostal) }},ES">
                    </iframe>
                </div>
            @endif

            <!-- Botones -->
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-4 w-full">
                <!-- Botón Agregar a Cesta (Solo para invitados o clientes) -->
                @if(auth()->guest() || auth()->user()->role === 'cliente')
                    <a id="addLink" href="#" onclick="addToCart('{{ $producto->guid }}'); return false"
                       class="bg-[#BFF205] text-gray-800 font-semibold py-3 px-6 rounded-md hover:bg-[#A8D403]
                  transition duration-300 transform hover:scale-105 w-full sm:w-auto text-center flex items-center justify-center gap-2">
                        <i class="fa fa-shopping-cart"></i> Agregar a Cesta
                    </a>
                @endif

                <!-- Spinner de carga -->
                <div id="spinner" class="hidden flex items-center justify-center w-full sm:w-auto">
                    <svg class="h-5 w-5 animate-spin text-gray-500" viewBox="0 0 24 24" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="#111827" stroke-width="4"></circle>
                        <path d="M2 12a10 10 0 0110-10" stroke="#BFF205" stroke-width="4"></path>
                    </svg>
                </div>

                <!-- Botón Añadir a Favoritos (Solo para clientes) -->
                @if(auth()->check() && auth()->user()->role === 'cliente')
                    <a href="#" class="bg-white text-gray-800 font-semibold py-3 px-6 rounded-md
                          hover:bg-gray-100 dark:bg-black dark:text-white dark:hover:bg-gray-800
                          border-2 border-black dark:border-gray-600 transition duration-300
                          transform hover:scale-105 flex items-center justify-center gap-2 w-full sm:w-auto text-center">
                        <i class="fa fa-heart"></i>
                        <span id="favorite-text-{{ $producto->guid }}">
                            {{ $productoFavorito ? 'Eliminar a Favoritos' : 'Añadir a Favoritos' }}
                        </span>
                    </a>
                @endif

                <!-- Botón Banear/Rehabilitar (Solo para administradores) -->
                @if(auth()->check() && auth()->user()->role === 'admin')
                    <form action="{{ route('admin.ban.product', $producto->guid) }}" method="POST" class="w-full sm:w-auto">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="p-3 text-black rounded-md bg-[#BFF205] hover:bg-[#96bf03]
                                       font-semibold w-full sm:w-auto text-center flex items-center justify-center gap-2">
                            @if($producto->estado === 'Baneado')
                                REHABILITAR
                            @else
                                <i class="fa fa-ban"></i> BANNEAR <i class="fa fa-ban"></i>
                            @endif
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </div>
    <x-footer/>
@endsection
<script>
    let maxStock = {{ $producto->stock }};
    let currentStock = 1;

    function addOne() {
        if (currentStock < maxStock) {
            currentStock++;
            updateStockDisplay();
        }
    }

    function removeOne() {
        if (currentStock > 1) {
            currentStock--;
        }
        updateStockDisplay();
    }

    function updateStockDisplay() {
        const stockInput = document.getElementById('cantidad');
        stockInput.value = currentStock;
    }

    async function addToCart(productId) {
        showSpinner();
        const amount = parseInt(document.getElementById('cantidad').value);
        await fetch("{{ route('carrito.add') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": " {{ csrf_token() }}",
            },
            body: JSON.stringify({
                productId: productId,
                amount: amount,
            }),
        }).then(response => response.json())
            .then(data => {
                if (data.status === 200) {
                    updateCartLogo(data.itemAmount);
                    showNotification();
                }
            })
            .catch(error => {
                console.error(error);
            })
        hideSpinner();
    }

    function showSpinner() {
        const link = document.getElementById(`addLink`);
        const spinner = document.getElementById(`spinner`);

        if (spinner != null && link != null) {
            spinner.classList.remove("hidden");
            link.classList.add("hidden");
        }
    }

    function hideSpinner() {
        const link = document.getElementById(`addLink`);
        const spinner = document.getElementById(`spinner`);

        if (spinner != null && link != null) {
            link.classList.remove("hidden");
            spinner.classList.add("hidden");
        }
    }

    function showNotification() {
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

    function updateCartLogo(amountOfItems) {
        const cartLogo = document.getElementById('itemCount');
        cartLogo.innerHTML = amountOfItems;
    }

    function hideToastOnScroll() {
        const toast = document.getElementById("toast-success");
        toast.classList.add("hidden");
    }

    window.addEventListener('scroll', hideToastOnScroll);
</script>

