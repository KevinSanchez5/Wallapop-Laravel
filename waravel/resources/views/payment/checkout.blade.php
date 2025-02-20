@extends('layouts.app')

@section('title', 'Su carrito')

@section('content')
    <x-header />
        <section class="bg-white dark:bg-gray-700 rounded-lg shadow-lg mt-12 p-6 max-w-md w-full mx-auto">
            <div class="product flex flex-col items-center text-center">
                <img src="https://i.imgur.com/EHyR2nP.png" alt="Producto" class="w-48 h-48 object-cover rounded-md shadow-md">
                <div class="description mt-4">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Ejemplo de producto</h3>
                    <h5 class="text-lg text-gray-600 dark:text-gray-300">10,00€</h5>
                </div>
            </div>
            <form action="{{ url('/api/crear-sesion-pago') }}" method="POST" class="mt-6">
                @csrf
                <input type="hidden" name="price_id" value="tu_price_id_de_stripe">
                <button type="submit" id="checkout-button"
                        class="w-full bg-black text-white py-3 rounded-md font-semibold hover:bg-gray-800 transition-colors dark:bg-gray-800 dark:hover:bg-gray-600">
                    Pagar
                </button>
                <a href="{{ route('pages.home') }}"
                   class="mt-4 block text-center w-full px-6 py-3 text-lg font-semibold text-gray-900 bg-[#BFF205] rounded-md hover:bg-[#A8D004] transition-colors dark:bg-[#BFF205] dark:hover:bg-[#A8D004]">
                    Volver a la página de inicio
                </a>
            </form>
        </section>
    <x-footer />
@endsection
