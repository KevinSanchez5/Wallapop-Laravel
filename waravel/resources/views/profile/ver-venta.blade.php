@extends('layouts.app')

@section('title', 'Detalles de pedido')

@section('content')
    <x-header />
    <div class="container mx-auto min-h-screen px-4 pt-2">
        <section class="flex flex-col gap-6 p-4 py-8 antialiased md:flex-row">
            <div class="h-auto w-full md:w-[45%]">
                <h2 class="mb-2 text-xl font-semibold text-gray-900 transition-all duration-300 sm:text-2xl dark:text-white">Dirección de envío</h2>
                <div class="rounded-lg bg-white p-6 shadow-lg transition-all duration-300 dark:bg-gray-800">
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
                <h2 class="mb-2 text-xl font-semibold text-gray-900 transition-all duration-300 sm:text-2xl dark:text-white">Detalles del pedido</h2>
                <div class="w-full rounded-lg bg-white shadow-lg transition-all duration-300 dark:bg-gray-800">
                    @if(!($venta->estado == 'Cancelado' || $venta->estado == 'Devuelto'))
                    <div class="p-6 pt-8">
                        <div class="w-full max-w-3xl px-4">
                            <div class="flex items-center justify-between">
                                <div class="relative w-full flex items-center">
                                    <!-- Steps -->
                                    <div class="w-full flex justify-between relative z-20">
                                        <div class="flex flex-col items-center">
                                            <div class="w-10 h-10 flex items-center justify-center rounded-full bg-[#BFF205] dark:text-black text-white z-20">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" />
                                                </svg>
                                            </div>
                                            <span class="text-sm mt-2 text-gray-500 dark:text-gray-400">Pendiente</span>
                                        </div>
                                        <div class="flex flex-col items-center">
                                            @if($venta->estado == 'Entregado' || $venta->estado == 'Enviado' || $venta->estado == 'Procesando')
                                                <div class="w-10 h-10 flex items-center justify-center rounded-full bg-[#BFF205] dark:text-black text-white z-20">
                                            @else
                                                <div class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-300 dark:text-gray-500 text-white z-20">
                                            @endif
                                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="currentColor" version="1.1" id="Capa_1" viewBox="0 0 442 442" xml:space="preserve">
                                                    <g>
                                                        <path d="M412.08,115.326c-0.006-0.322-0.027-0.643-0.064-0.961c-0.011-0.1-0.02-0.201-0.035-0.3   c-0.057-0.388-0.131-0.773-0.232-1.151c-0.013-0.05-0.032-0.097-0.046-0.146c-0.094-0.33-0.206-0.654-0.333-0.973   c-0.041-0.102-0.085-0.203-0.129-0.304c-0.126-0.289-0.266-0.571-0.42-0.848c-0.039-0.069-0.073-0.141-0.113-0.209   c-0.203-0.346-0.426-0.682-0.672-1.004c-0.019-0.025-0.042-0.049-0.061-0.074c-0.222-0.285-0.463-0.558-0.718-0.82   c-0.07-0.072-0.143-0.142-0.215-0.212c-0.225-0.217-0.461-0.424-0.709-0.622c-0.077-0.062-0.15-0.126-0.229-0.185   c-0.311-0.234-0.634-0.457-0.979-0.657L226.034,1.359c-3.111-1.813-6.956-1.813-10.067,0l-181.092,105.5   c-0.345,0.201-0.668,0.423-0.979,0.657c-0.079,0.059-0.152,0.124-0.229,0.185c-0.248,0.198-0.484,0.405-0.709,0.622   c-0.073,0.07-0.145,0.14-0.215,0.212c-0.255,0.262-0.496,0.535-0.718,0.82c-0.02,0.025-0.042,0.049-0.061,0.074   c-0.246,0.322-0.468,0.658-0.672,1.004c-0.04,0.068-0.075,0.14-0.113,0.209c-0.154,0.277-0.294,0.559-0.42,0.848   c-0.044,0.101-0.088,0.202-0.129,0.304c-0.127,0.319-0.239,0.643-0.333,0.973c-0.014,0.049-0.033,0.097-0.046,0.146   c-0.101,0.378-0.175,0.763-0.232,1.151c-0.014,0.099-0.023,0.2-0.035,0.3c-0.037,0.319-0.058,0.639-0.064,0.961   c-0.001,0.058-0.012,0.115-0.012,0.174v211c0,3.559,1.891,6.849,4.966,8.641l181.092,105.5c0.029,0.017,0.059,0.027,0.088,0.043   c0.357,0.204,0.725,0.391,1.108,0.55c0.004,0.002,0.009,0.003,0.014,0.005c0.362,0.15,0.736,0.273,1.118,0.38   c0.097,0.027,0.193,0.051,0.291,0.075c0.299,0.074,0.603,0.134,0.912,0.181c0.103,0.016,0.205,0.035,0.308,0.047   c0.393,0.047,0.79,0.078,1.195,0.078s0.803-0.031,1.195-0.078c0.103-0.012,0.205-0.031,0.308-0.047   c0.309-0.047,0.613-0.107,0.912-0.181c0.097-0.024,0.194-0.047,0.291-0.075c0.382-0.107,0.756-0.23,1.118-0.38   c0.004-0.002,0.009-0.003,0.014-0.005c0.383-0.159,0.751-0.346,1.108-0.55c0.029-0.016,0.059-0.027,0.088-0.043l181.092-105.5   c3.075-1.792,4.966-5.082,4.966-8.641v-211C412.092,115.441,412.081,115.385,412.08,115.326z M221,209.427l-70.68-41.177   l161.226-93.927l70.681,41.177L221,209.427z M221,21.573l70.68,41.177l-161.226,93.927L59.774,115.5L221,21.573z M392.092,320.752   L231,414.601V374c0-5.523-4.477-10-10-10s-10,4.477-10,10v40.601L49.908,320.752V132.899l75.626,44.058   c0.005,0.003,0.011,0.006,0.016,0.01L211,226.747V334c0,5.523,4.477,10,10,10s10-4.477,10-10V226.747l161.092-93.848V320.752z"/>
                                                        <path d="M284.613,247.88c1.858,3.189,5.208,4.968,8.65,4.968c1.709,0,3.441-0.438,5.024-1.361l36.584-21.313   c4.772-2.78,6.387-8.902,3.607-13.674c-2.78-4.772-8.903-6.386-13.674-3.607l-36.584,21.313   C283.448,236.986,281.833,243.108,284.613,247.88z"/>
                                                    </g>
                                                </svg>

                                            </div>
                                            <span class="text-sm mt-2 text-gray-500 dark:text-gray-400">Procesando</span>
                                        </div>
                                        <div class="flex flex-col items-center">
                                            @if($venta->estado == 'Entregado' || $venta->estado == 'Enviado')
                                                <div class="w-10 h-10 flex items-center justify-center rounded-full bg-[#BFF205] dark:text-black text-white z-20">
                                            @else
                                                <div class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-300 dark:text-gray-500 text-white z-20">
                                            @endif
                                                <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                                                </svg>
                                            </div>
                                            <span class="text-sm mt-2 text-gray-500 dark:text-gray-400">En reparto</span>
                                        </div>
                                        <div class="flex flex-col items-center">
                                            @if($venta->estado == 'Entregado')
                                                <div class="w-10 h-10 flex items-center justify-center rounded-full bg-[#BFF205] dark:text-black text-white z-20">
                                            @else
                                                <div class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-300 dark:text-gray-500 text-white z-20">
                                            @endif
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                            <span class="text-sm mt-2 text-gray-500 dark:text-gray-400">Entregado</span>
                                        </div>
                                    </div>
                                    <!-- Progress Bar -->
                                    <div class="absolute top-5 left-0 right-0 h-1 bg-gray-300 w-full z-0"></div>
                                    @if($venta->estado == 'Pendiente')
                                        <div class="absolute top-5 left-0 h-1 bg-[#BFF205] w-1/4 z-10"></div>
                                    @elseif($venta->estado == 'Procesando')
                                        <div class="absolute top-5 left-0 h-1 bg-[#BFF205] w-2/4 z-10"></div>
                                    @elseif($venta->estado == 'Enviado')
                                        <div class="absolute top-5 left-0 h-1 bg-[#BFF205] w-3/4 z-10"></div>
                                    @elseif($venta->estado == 'Entregado')
                                        <div class="absolute top-5 left-0 h-1 bg-[#BFF205] w-full z-10"></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="row p-6">
                        <div class="grid grid-flow-col gap-4">
                            @if($venta->estado == 'Cancelado' || $venta->estado == 'Devuelto')
                                <div class="min-w-1/3">
                            @else
                                <div class="min-w-1/2">
                            @endif
                                <p class="text-base font-normal text-gray-500 dark:text-gray-400">Nº pedido</p>
                                <h3 class="text-xl font-normal break-all text-gray-900 transition-all duration-300 sm:text-xl dark:text-white"> {{ $venta->guid }}</h3>
                            </div>
                            @if($venta->estado == 'Cancelado' || $venta->estado == 'Devuelto')
                                <div class="min-w-1/3">
                            @else
                                <div class="min-w-1/2">
                            @endif
                                <p class="text-base font-normal text-gray-500 dark:text-gray-400">Fecha</p>
                                <h3 class="text-xl font-normal text-gray-900 transition-all duration-300 sm:text-xl dark:text-white"> {{ \Carbon\Carbon::parse($venta->created_at)->format('d-m-y') }}</h3>
                            </div>
                            @if($venta->estado == 'Cancelado')
                                <div class="min-w-1/3">
                                    <dd class="text-red-800 dark:text-red-300 flex-1 items-center rounded bg-red-200 px-3 py-1 text-sm font-medium break-all dark:bg-red-600">
                                        <svg class="me-2 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                                        </svg>
                                        Cancelado
                                    </dd>
                                </div>
                            @elseif($venta->estado == 'Devuelto')
                                <div class="min-w-1/3">
                                    <dd class="text-purple-800 dark:text-purple-300 flex-1 items-center rounded bg-purple-200 px-3 py-1 text-sm font-medium break-all dark:bg-purple-600">
                                        <svg class="me-2 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5L3 12l6 7M21 12H3"/>
                                        </svg>
                                        Devuelto
                                    </dd>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg transition-all duration-300 mt-4 px-6 py-2">
                    <table class="w-full text-left font-medium text-gray-900 dark:text-white md:table-fixed">
                        <tbody>
                        @forelse($venta->lineaVentas as $linea)
                            @if($linea['vendedor']['guid'] != $vendedor->guid)
                                <tr class="{{ $loop->last ? 'mt-10' : 'mb-2 border-b border-gray-300 dark:border-gray-700' }}">
                                    <td class="py-4" style="width: 60%;">
                                        <div class="flex items-center gap-4">
                                            <div class="flex items-center justify-center w-20 h-20 bg-gray-300 rounded-md dark:bg-gray-700">
                                                <svg class="w-10 h-10 text-gray-200 dark:text-gray-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                                                    <path d="M18 0H2a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2Zm-5.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm4.376 10.481A1 1 0 0 1 16 15H4a1 1 0 0 1-.895-1.447l3.5-7A1 1 0 0 1 7.468 6a.965.965 0 0 1 .9.5l2.775 4.757 1.546-1.887a1 1 0 0 1 1.618.1l2.541 4a1 1 0 0 1 .028 1.011Z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-600 w-24 mb-2.5"></div>
                                                <div class="w-32 h-2 bg-gray-200 rounded-full dark:bg-gray-700"></div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="p-4">
                                        <div class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-700 w-12"></div>
                                    </td>

                                    <td class="p-4 text-right">
                                        <div class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-700 w-full"></div>
                                    </td>
                                </tr>
                            @else
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
                            @endif
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
            </div>
        </section>
    </div>
    <x-footer />
@endsection
