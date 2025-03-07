@extends('layouts.profile')

@section('title', 'Mis Ventas')

@section('section')
    <section>
        <div class="flex flex-wrap justify-center gap-4 mb-4">
            <button onclick="window.location.href='{{ route('profile.products') }}'"
                    class="px-4 py-2 rounded-lg text-black bg-[#A0D500] hover:bg-[#BFF205]">
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
                    class="px-4 py-2 rounded-lg text-black bg-[#BFF205] hover:bg-[#A0D500]">
                <b>Mis ventas</b>
            </button>
            <button onclick="window.location.href='{{ route('profile.favorites') }}'"
                    class="px-4 py-2 rounded-lg text-black bg-[#A0D500] hover:bg-[#BFF205]">
                <b>Favoritos</b>
            </button>
        </div>
        <div id="ventas" class="seccion">
            <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
                <div class="mx-auto max-w-5xl">
                    <div class="gap-4 sm:flex sm:items-center sm:justify-between">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Mis pedidos</h2>

                        <div class="mt-6 gap-4 space-y-4 sm:mt-0 sm:flex sm:items-center sm:justify-end sm:space-y-0">
                            <div>
                                <label for="estado" class="sr-only mb-2 block text-sm font-medium text-gray-900 dark:text-white">Select order type</label>
                                @php
                                    $orderStatuses = [
                                        'Todos' => 'Todos',
                                        'Pendiente' => 'Pendiente',
                                        'Procesando' => 'Procesando',
                                        'Enviado' => 'En reparto',
                                        'Entregado' => 'Entregado',
                                        'Devuelto' => 'Devuelto',
                                        'Cancelado' => 'Cancelado'
                                    ];
                                    $selectedStatus = request('estado', 'todos');
                                @endphp

                                <form method="GET" action="{{ route('profile.sales.search') }}">
                                    <select name="estado" id="estado" onchange="this.form.submit()"
                                            class="block w-full min-w-[8rem] rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500">
                                        @foreach ($orderStatuses as $value => $label)
                                            <option value="{{ $value }}" {{ $selectedStatus == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2 flow-root sm:mt-2">
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($ventas as $venta)
                            @if(!$loop->last)
                                <div class="flex flex-wrap items-center border-b border-gray-300 dark:border-gray-700 gap-y-6 py-4">
                            @else
                                <div class="flex flex-wrap items-center gap-y-6 py-4">
                            @endif
                                <dl class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1">
                                    <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Número:</dt>
                                    <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">
                                        <a href="#" class="hover:underline">#{{ $venta->guid }}</a>
                                    </dd>
                                </dl>

                                <dl class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1">
                                    <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Fecha:</dt>
                                    <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">{{ $venta->created_at->format('d-m-Y') }}
                                    </dd>
                                </dl>

                                <dl class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1">
                                    <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Estado:</dt>
                                    @if($venta->estado == "Pendiente")
                                        <dd class="me-2 mt-1.5 inline-flex items-center rounded bg-primary-100 px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            <svg class="me-1 h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.5 4h-13m13 16h-13M8 20v-3.333a2 2 0 0 1 .4-1.2L10 12.6a1 1 0 0 0 0-1.2L8.4 8.533a2 2 0 0 1-.4-1.2V4h8v3.333a2 2 0 0 1-.4 1.2L13.957 11.4a1 1 0 0 0 0 1.2l1.643 2.867a2 2 0 0 1 .4 1.2V20H8Z"></path>
                                            </svg>
                                            Pendiente
                                        </dd>
                                    @elseif($venta->estado == "Procesando")
                                        <dd class="me-2 mt-1.5 inline-flex items-center rounded bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-800 bg-blue-200 dark:bg-blue-600 dark:text-primary-300">
                                            <svg class="me-1 h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5" />
                                            </svg>
                                            Procesado
                                        </dd>
                                    @elseif($venta->estado == "Enviado")
                                        <dd class="me-2 mt-1.5 inline-flex items-center rounded bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                            <svg class="me-1 h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                                            </svg>
                                            En reparto
                                        </dd>
                                    @elseif($venta->estado == "Entregado")
                                        <dd class="me-2 mt-1.5 inline-flex items-center rounded bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-300">
                                            <svg class="me-1 h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5" />
                                            </svg>
                                            Entregado
                                        </dd>
                                    @elseif($venta->estado == "Cancelado")
                                        <dd class="me-2 mt-1.5 inline-flex items-center rounded bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-300">
                                            <svg class="me-1 h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                                            </svg>
                                            Cancelado
                                        </dd>
                                    @elseif($venta->estado == "Devuelto")
                                        <dd class="me-2 mt-1.5 inline-flex items-center rounded bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-purple-800 bg-purple-100 dark:bg-purple-900 dark:text-purple-300">
                                            <svg class="me-1 h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 5L3 12l6 7M21 12H3"/>
                                            </svg>
                                            Devuelto
                                        </dd>
                                    @endif
                                </dl>

                                <div class="flex-1 text-right py-2">
                                    <a href="{{ route('sale.detail', $venta->guid) }}" class="w-full inline-flex justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700 lg:w-auto">
                                        Ver detalles
                                    </a>
                                </div>
                            </div>
                                @empty
                                <div class="flex justify-center items-center h-screen">
                                    <p class="mt-4 text-xl text-gray-900 dark:text-gray-400">No tienes pedidos</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Paginación -->
                    @if ($ventas->hasPages())
                        <div class="mt-4 mx-auto text-center">
                            {{ $ventas->links('pagination::tailwind') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
