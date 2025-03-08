@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <x-header />
    <div class="container mx-auto min-h-screen pt-2 px-4">
        <section class="py-8 antialiased flex flex-col md:flex-row gap-6 p-4">
            <div class="w-full md:w-[55%]">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl transition-all duration-300">Detalles de la compra</h2>
                <div class="w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg transition-all duration-300 mt-4 px-6 py-2">
                    <table class="w-full text-left font-medium text-gray-900 dark:text-white md:table-fixed">
                        <tbody>
                        @forelse($pedido->lineaVentas as $linea)
                            <tr class="{{ $loop->last ? 'mt-10' : 'mb-2 border-b border-gray-300 dark:border-gray-700' }}">
                                <td class="py-4" style="width: 60%;">
                                    <div class="flex items-center gap-4">
                                        <div class="shrink-0">
                                            <img class="h-20 w-20 object-cover rounded-md" src="{{ asset('storage/' . ($linea['producto']['imagenes'][0] ?? 'default.jpg')) }}" alt="imagen de {{ $linea['producto']['nombre'] }}" />
                                        </div>
                                        <span class="text-base font-medium text-gray-900 dark:text-white">{{ $linea['producto']['nombre'] }}</span>
                                    </div>
                                </td>

                                <td class="p-4 text-base font-normal text-gray-900 dark:text-white">x{{ $linea['cantidad'] }}</td>

                                <td class="p-4 text-right text-base font-bold text-gray-900 dark:text-white">{{ $linea['precioTotal'] }} €</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-base font-normal text-gray-500 dark:text-gray-400 text-center" style="height: 10rem; line-height: 10rem;">
                                    No hay productos en el pedido
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg transition-all duration-300 p-6 mt-4">
                    <div class="space-y-2">
                        <dl class="flex items-center justify-between gap-4">
                            <dt class="text-xl font-normal text-gray-500 dark:text-gray-400">Precio sin IVA</dt>
                            <dd id="priceWithoutIva" class="text-xl font-normal text-gray-500 dark:text-gray-400">
                                {{ number_format($pedido->precioTotal / 1.21, 2) }} €
                            </dd>
                        </dl>
                    </div>

                    <div class="space-y-2 mb-4">
                        <dl class="flex items-center justify-between gap-4">
                            <dt class="text-xl font-normal text-gray-500 dark:text-gray-400">Iva (21%)</dt>
                            <dd id="ivaAmount" class="text-xl font-normal text-gray-500 dark:text-gray-400">
                                {{ number_format($pedido->precioTotal - ($pedido->precioTotal / 1.21), 2) }} €
                            </dd>
                        </dl>
                    </div>

                    <hr class="bottomLine border-t border-gray-200 mb-4 dark:border-gray-700" style="margin-left: 1.5rem; width: calc(100% - 3rem)"/>

                    <dl class="flex items-center justify-between gap-4 mt-4">
                        <dt class="text-xl font-bold text-gray-900 dark:text-gray-400">Total</dt>
                        <dd id="finalTotal" class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($pedido->precioTotal, 2) }} €
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="w-full md:w-[45%] h-auto">
                <div class="w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg transition-all duration-100 p-6 mt-4 md:mt-12">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl transition-all duration-300 text-center mb-2">Tu reseña</h2>

                    <hr class="bottomLine border-t border-gray-200 dark:border-gray-700 mb-2" style="width: 8rem; margin-left: calc((100% - 8rem) / 2)"/>

                    <div class="text-center flex justify-center space-x-1">
                        <form class="w-full mb-0" action="{{ route('save.review', $pedido->guid) }}" method="POST">
                            @csrf

                            <div class="text-center flex justify-center space-x-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-7 h-7 cursor-pointer text-gray-400 transition-colors duration-300"
                                         viewBox="0 0 24 24" fill="currentColor"
                                         onclick="setRating({{ $i }})"
                                         id="star-{{ $i }}">
                                        <path d="M12 17.3l-5.4 3.4 1.4-6-4.6-4 6.1-.5L12 4l2.5 6.1 6.1.5-4.6 4 1.4 6z"/>
                                    </svg>
                                @endfor
                            </div>

                            <input type="hidden" id="rating" name="rating" value="0">

                            <label for="comment" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white text-left">Tu review</label>

                            <textarea name="comment" id="comment" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-[#BFF205] focus:border-[#BFF205] dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-[#BFF205] dark:focus:border-[#BFF205]" placeholder="Escribe tu experiencia aquí..."></textarea>
                            @if($errors->any())
                            <div class="text-sm text-left text-red-500 mt-2">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                            @endif

                            <button type="submit" class="mt-4 block w-full bg-[#BFF205] text-black text-center font-medium py-2 px-6 rounded-md transition-transform duration-300 hover:scale-105 hover:shadow-lg">
                                Enviar
                            </button>
                        </form>
                </div>
            </div>
        </section>
    </div>
    <x-footer />
@endsection

<script>
    function setRating(rating) {
        document.getElementById('rating').value = rating;
        for (let i = 1; i <= 5; i++) {
            document.getElementById('star-' + i).classList.remove('text-yellow-500');
            document.getElementById('star-' + i).classList.add('text-gray-400');
        }
        for (let i = 1; i <= rating; i++) {
            document.getElementById('star-' + i).classList.remove('text-gray-400');
            document.getElementById('star-' + i).classList.add('text-yellow-500');
        }
    }
</script>

