@extends('layouts.app')

@section('title', 'Detalles del Producto')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <x-header/>

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
                                <div class="w-full h-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center rounded-lg shadow-lg">
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
                <div x-show="open" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50" @click.away="open = false">
                    <img :src="imgSrc" class="max-w-4xl max-h-screen rounded-lg shadow-lg">
                    <button class="absolute top-5 right-5 text-white text-3xl font-bold" @click="open = false">&times;</button>
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
            <p class="text-gray-900 dark:text-gray-100 font-semibold text-xl mt-4 text-center md:text-left">
                {{ $producto->precio }} €
            </p>

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
            <div class="mt-6 flex flex-col sm:flex-row items-center sm:justify-center md:justify-start gap-4">
                <!-- Botón Agregar a Cesta con el color personalizado -->
                <a href="#" onclick="addToCart({{ $producto }}, 1)" class="bg-[#BFF205] text-gray-800 font-semibold py-2 px-6 rounded-md
   hover:bg-[#A8D403] transition duration-300 transform hover:scale-105">
                    Agregar a Cesta
                </a>

                <!-- Botón Añadir a Favoritos con icono de corazón -->
                <a href="#" class="bg-white text-gray-800 font-semibold py-2 px-6 rounded-md
   hover:bg-gray-100 dark:bg-black dark:text-white dark:hover:bg-gray-800 transition duration-300 transform hover:scale-105 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Añadir a Favoritos
                </a>
            </div>


        </div>
    </div>
    <x-footer />
@endsection
<script>
    function addToCart(product, amount) {
        fetch("{{ route('carrito.add') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": " {{ csrf_token() }}",
            },
            body: JSON.stringify({
                producto: product,
                amount: amount,
            }),
        }).then(response => console.log(response));
    }
</script>

