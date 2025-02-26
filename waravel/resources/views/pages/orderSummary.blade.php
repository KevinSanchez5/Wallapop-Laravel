@extends('layouts.app')

@section('title', 'Carrito')

@section('content')
    <x-header />
    <div class="container mx-auto min-h-screen pt-2 px-4">
        <section class="py-8 antialiased flex flex-col md:flex-row gap-6 p-4">
            <div class="w-full md:w-[45%] h-auto">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl transition-all duration-300 mb-2">Dirección de envío</h2>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg transition-all duration-300 p-6">
                    <div class="mb-6">
                        <p class="text-base font-normal text-gray-500 dark:text-gray-400">Nombre:</p>
                        <h3 class="text-md font-normal text-gray-900 transition-all duration-300 sm:text-xl dark:text-white">Some Name</h3>
                    </div>

                    <div class="mb-6">
                        <p class="text-base font-normal text-gray-500 dark:text-gray-400">Email:</p>
                        <h3 class="text-md font-normal text-gray-900 transition-all duration-300 sm:text-xl dark:text-white">defaul@gmail.com</h3>
                    </div>

                    <div class="mb-6">
                        <p class="text-base font-normal text-gray-500 dark:text-gray-400">Número de teléfono:</p>
                        <h3 class="text-md font-normal text-gray-900 transition-all duration-300 sm:text-xl dark:text-white">344374744</h3>
                    </div>

                    <div class="mb-6">
                        <p class="text-base font-normal text-gray-500 dark:text-gray-400">Calle:</p>
                        <h3 class="text-md font-normal text-gray-900 transition-all duration-300 sm:text-xl dark:text-white">123 Internet Street, Disney</h3>
                    </div>

                    <hr class="bottomLine border-t border-gray-200 dark:border-gray-700" style="margin-left: 1.5rem; width: calc(100% - 3rem); margin-top: 0"/>

                    <div class="space-y-2 mt-4">
                        <dl class="flex items-center justify-between gap-4">
                            <dt class="text-xl font-normal text-gray-500 dark:text-gray-400">Precio</dt>
                            <dd id="totalPrice" class="text-xl font-normal text-gray-500 dark:text-gray-400"> 445456 €</dd>
                        </dl>
                    </div>

                    <div class="space-y-2 mb-4">
                        <dl class="flex items-center justify-between gap-4">
                            <dt class="text-xl font-normal text-gray-500 dark:text-gray-400">Iva</dt>
                            <dd id="totalPrice" class="text-xl font-normal text-gray-500 dark:text-gray-400">566 €</dd>
                        </dl>
                    </div>

                    <hr class="bottomLine border-t border-gray-200 dark:border-gray-700" style="margin-left: 1.5rem; width: calc(100% - 3rem)"/>
                    <dl class="flex items-center justify-between gap-4 mt-4">
                        <dt class="text-xl font-bold text-gray-900 text-gray-500 dark:text-gray-400">Total</dt>
                        <dd id="finalTotal" class="text-xl font-bold text-gray-900 dark:text-white"> 557576 €</dd>
                    </dl>

                    <a href="#" class="block w-full mt-4 bg-[#BFF205] text-black text-center font-medium py-2 px-6 rounded-md transition-transform duration-300 hover:scale-105 hover:shadow-lg">Continuar</a>
                    <div class="flex items-start sm:items-center">
                        <input id="terms-checkbox-2" type="checkbox" value="" class="h-4 w-4 mr-2 mt-2 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-primary-600" />
                        <label for="terms-checkbox-2" class="ms-2 mt-2 text-sm font-medium text-gray-900 dark:text-gray-300"> Acepto los <a href="#" title="" class="text-primary-700 underline hover:no-underline dark:text-[#BFF205]">Términos y Condiciones</a></label>
                    </div>
                </div>

            </div>

            <div class="w-full md:w-[55%]">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl transition-all duration-300 mb-2">Detalles de la compra</h2>
                <div class="w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg transition-all duration-300">
                    <div class="row p-6">
                        <div class="grid grid-flow-col gap-4">
                            <div style="min-width: 50%">
                                <p class="text-base font-normal text-gray-500 dark:text-gray-400">Nº pedido</p>
                                <h3 class="text-xl font-normal text-gray-900 transition-all duration-300 sm:text-xl dark:text-white">7647-3444-4443-3446</h3>
                            </div>
                            <div style="min-width: 50%">
                                <p class="text-base font-normal text-gray-500 dark:text-gray-400">Fecha</p>
                                <h3 class="text-xl font-normal text-gray-900 transition-all duration-300 sm:text-xl dark:text-white">20-12-2025</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg transition-all duration-300 mt-4 p-6">
                    <table class="w-full text-left font-medium text-gray-900 dark:text-white md:table-fixed">
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        <tr>
                            <td class="whitespace-nowrap py-4" style="width: 60%">
                                <div class="flex items-center gap-4">
                                    <a href="#" class="flex items-center aspect-square w-10 h-10 shrink-0">
                                        <img class="h-auto w-full max-h-full dark:hidden" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front.svg" alt="imac image" />
                                        <img class="hidden h-auto w-full max-h-full dark:block" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front-dark.svg" alt="imac image" />
                                    </a>
                                    <a href="#" class="hover:underline">Apple iMac 27”</a>
                                </div>
                            </td>

                            <td class="p-4 text-base font-normal text-gray-900 dark:text-white">x1</td>

                            <td class="p-4 text-right text-base font-bold text-gray-900 dark:text-white">$1,499</td>
                        </tr>

                        <tr>
                            <td class="whitespace-nowrap py-4" style="width: 60%">
                                <div class="flex items-center gap-4">
                                    <a href="#" class="flex items-center aspect-square w-10 h-10 shrink-0">
                                        <img class="h-auto w-full max-h-full dark:hidden" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/iphone-light.svg" alt="imac image" />
                                        <img class="hidden h-auto w-full max-h-full dark:block" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/iphone-dark.svg" alt="imac image" />
                                    </a>
                                    <a href="#" class="hover:underline">Apple iPhone 14</a>
                                </div>
                            </td>

                            <td class="p-4 text-base font-normal text-gray-900 dark:text-white">x2</td>

                            <td class="p-4 text-right text-base font-bold text-gray-900 dark:text-white">$1,998</td>
                        </tr>

                        <tr>
                            <td class="whitespace-nowrap py-4" style="width: 60%">
                                <div class="flex items-center gap-4">
                                    <a href="#" class="flex items-center aspect-square w-10 h-10 shrink-0">
                                        <img class="h-auto w-full max-h-full dark:hidden" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/ipad-light.svg" alt="ipad image" />
                                        <img class="hidden h-auto w-full max-h-full dark:block" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/ipad-dark.svg" alt="ipad image" />
                                    </a>
                                        <a href="#" class="hover:underline">Apple iPad Air</a>
                                </div>
                            </td>

                            <td class="p-4 text-base font-normal text-gray-900 dark:text-white">x1</td>

                            <td class="p-4 text-right text-base font-bold text-gray-900 dark:text-white">$898</td>
                        </tr>

                        <tr>
                            <td class="whitespace-nowrap py-4" style="width: 60%">
                                <div class="flex items-center gap-4">
                                    <a href="#" class="flex items-center aspect-square w-10 h-10 shrink-0">
                                        <img class="h-auto w-full max-h-full dark:hidden" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/xbox-light.svg" alt="xbox image" />
                                        <img class="hidden h-auto w-full max-h-full dark:block" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/xbox-dark.svg" alt="xbox image" />
                                    </a>
                                        <a href="#" class="hover:underline">Xbox Series X</a>
                                </div>
                            </td>

                            <td class="p-4 text-base font-normal text-gray-900 dark:text-white">x4</td>

                            <td class="p-4 text-right text-base font-bold text-gray-900 dark:text-white">$4,499</td>
                        </tr>

                        <tr>
                            <td class="whitespace-nowrap py-4" style="width: 60%">
                                <div class="flex items-center gap-4">
                                    <a href="#" class="flex items-center aspect-square w-10 h-10 shrink-0">
                                        <img class="h-auto w-full max-h-full dark:hidden" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/ps5-light.svg" alt="playstation image" />
                                        <img class="hidden h-auto w-full max-h-full dark:block" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/ps5-dark.svg" alt="playstation image" />
                                    </a>
                                        <a href="#" class="hover:underline">PlayStation 5</a>
                                </div>
                            </td>

                            <td class="p-4 text-base font-normal text-gray-900 dark:text-white">x1</td>

                            <td class="p-4 text-right text-base font-bold text-gray-900 dark:text-white">$499</td>
                        </tr>

                        <tr>
                            <td class="whitespace-nowrap py-4" style="width: 60%">
                                <div class="flex items-center gap-4">
                                    <a href="#" class="flex items-center aspect-square w-10 h-10 shrink-0">
                                        <img class="h-auto w-full max-h-full dark:hidden" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/macbook-pro-light.svg" alt="macbook image" />
                                        <img class="hidden h-auto w-full max-h-full dark:block" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/macbook-pro-dark.svg" alt="macbook image" />
                                    </a>
                                        <a href="#" class="hover:underline">MacBook Pro 16"</a>
                                </div>
                            </td>

                            <td class="p-4 text-base font-normal text-gray-900 dark:text-white">x1</td>

                            <td class="p-4 text-right text-base font-bold text-gray-900 dark:text-white">$499</td>
                        </tr>

                        <tr>
                            <td class="whitespace-nowrap py-4" style="width: 60%">
                                <div class="flex items-center gap-4">
                                    <a href="#" class="flex items-center aspect-square w-10 h-10 shrink-0">
                                        <img class="h-auto w-full max-h-full dark:hidden" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/apple-watch-light.svg" alt="watch image" />
                                        <img class="hidden h-auto w-full max-h-full dark:block" src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/apple-watch-dark.svg" alt="watch image" />
                                    </a>
                                        <a href="#" class="hover:underline">Apple Watch SE</a>
                                </div>
                            </td>

                            <td class="p-4 text-base font-normal text-gray-900 dark:text-white">x2</td>

                            <td class="p-4 text-right text-base font-bold text-gray-900 dark:text-white">$799</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
    <x-footer />
@endsection
