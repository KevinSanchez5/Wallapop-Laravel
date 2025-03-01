@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <x-header />
    <div class="container mx-auto min-h-screen pt-2 px-4">
        <section class="py-8 antialiased flex flex-col md:flex-row gap-6 p-4">
            <div class="w-full md:w-[45%] h-auto">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl transition-all duration-300 mb-2">Dirección de envío</h2>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg transition-all duration-300 p-6">
                    <div class="mb-6">
                        <p class="text-base font-normal text-gray-500 dark:text-gray-400">Nombre:</p>
                        <h3 class="text-md font-normal text-gray-900 transition-all duration-300 sm:text-xl dark:text-white"> {{ $cliente->nombre  }} {{ $cliente->apellido }}</h3>
                    </div>

                    <div class="mb-6">
                        <p class="text-base font-normal text-gray-500 dark:text-gray-400">Email:</p>
                        <h3 class="text-md font-normal text-gray-900 transition-all duration-300 sm:text-xl dark:text-white"> {{ $usuario->email  }}</h3>
                    </div>

                    <div class="mb-6">
                        <p class="text-base font-normal text-gray-500 dark:text-gray-400">Número de teléfono:</p>
                        <h3 class="text-md font-normal text-gray-900 transition-all duration-300 sm:text-xl dark:text-white"> {{ $cliente->telefono }}</h3>
                    </div>

                    <div>
                        <p class="text-base font-normal text-gray-500 dark:text-gray-400">Calle:</p>
                        <h3 class="text-md font-normal text-gray-900 transition-all duration-300 sm:text-xl dark:text-white"> {{ $cliente->direccion->calle }}, {{ $cliente->direccion->numero }}, {{ $cliente->direccion->piso }}º{{ $cliente->direccion->letra }}, {{ $cliente->direccion->codigoPostal }}</h3>
                    </div>
                </div>

            </div>

            <div class="w-full md:w-[55%]">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl transition-all duration-300">Detalles de la compra</h2>
                <div class="w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg transition-all duration-300 mt-4 px-6 py-2">
                    <table class="w-full text-left font-medium text-gray-900 dark:text-white md:table-fixed">
                        <tbody>
                        @forelse($cart->lineasCarrito as $linea)
                            @if($loop->last)
                                <tr class="mb-2">
                            @else
                                <tr class="border-b border-gray-300 dark:border-gray-700">
                            @endif
                                <td class="py-4" style="width: 60%;">
                                    <div class="flex items-center gap-4">
                                        <div class="shrink-0">
                                            <img class="h-20 w-20 object-cover rounded-md" src="{{ asset('storage/' . $linea->producto->imagenes[0]) }}" alt="imagen de {{ $linea->producto->nombre }}" />
                                        </div>
                                        <span class="text-base font-medium text-gray-900 dark:text-white">{{ $linea->producto->nombre }}</span>
                                    </div>
                                </td>

                                <td class="p-4 text-base font-normal text-gray-900 dark:text-white">x{{ $linea->cantidad }}</td>

                                <td class="p-4 text-right text-base font-bold text-gray-900 dark:text-white">{{ $linea->precioTotal }} €</td>
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
                    <div class="space-y-2 mt-4">
                        <dl class="flex items-center justify-between gap-4">
                            <dt class="text-xl font-normal text-gray-500 dark:text-gray-400">Precio sin IVA</dt>
                            <dd id="priceWithoutIva" class="text-xl font-normal text-gray-500 dark:text-gray-400">
                                {{ number_format($cart->precioTotal / 1.21, 2) }} €
                            </dd>
                        </dl>
                    </div>

                    <div class="space-y-2 mb-4">
                        <dl class="flex items-center justify-between gap-4">
                            <dt class="text-xl font-normal text-gray-500 dark:text-gray-400">Iva (21%)</dt>
                            <dd id="ivaAmount" class="text-xl font-normal text-gray-500 dark:text-gray-400">
                                {{ number_format($cart->precioTotal - ($cart->precioTotal / 1.21), 2) }} €
                            </dd>
                        </dl>
                    </div>

                    <hr class="bottomLine border-t border-gray-200 dark:border-gray-700" style="margin-left: 1.5rem; width: calc(100% - 3rem)"/>

                    <dl class="flex items-center justify-between gap-4 mt-4 mb-4">
                        <dt class="text-xl font-bold text-gray-900 dark:text-gray-400">Total</dt>
                        <dd id="finalTotal" class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($cart->precioTotal, 2) }} €
                        </dd>
                    </dl>

                    <form action="{{ route('pagarcarrito') }}" method="POST">
                        @csrf
                        <button type="submit" class="block w-full bg-[#BFF205] text-black text-center font-medium py-2 px-6 rounded-md transition-transform duration-300 hover:scale-105 hover:shadow-lg">
                            Continuar
                        </button>
                    </form>
                    <div class="flex items-center justify-end">
                        <label for="terms-checkbox-2" class="ms-2 mt-2 text-sm font-medium text-gray-900 dark:text-gray-300"> Acepto los <a href="" title="" class="text-primary-700 underline hover:no-underline dark:text-[#BFF205]">Términos y Condiciones</a></label>
                        <input id="terms-checkbox-2" type="checkbox" value="" class="h-4 w-4 ml-2 mt-2 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-primary-600" />
                    </div>
                </div>
            </div>
        </section>
    </div>
    <x-footer />
@endsection
