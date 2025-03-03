@extends('layouts.app')

@section('title', 'Error en el Pago')

@section('content')
    <x-header />

    <div class="container mx-auto py-12 px-6 flex flex-col items-center text-center">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-8 max-w-xl w-full">
            <h2 class="text-2xl font-semibold text-red-600 dark:text-red-400">¡Algo salió mal con tu compra!</h2>

            @foreach (['error' => 'red', 'message' => 'yellow'] as $key => $color)
                @if(session($key))
                    @php
                        $bgColor = $color == 'red' ? 'bg-red-200' : 'bg-yellow-200';
                        $textColor = $color == 'red' ? 'text-red-600' : 'text-yellow-600';
                    @endphp

                    <div class="mt-4 p-4 {{ $bgColor }} {{ $textColor }} rounded-md">
                        <p>{{ session($key) }}</p>
                    </div>
                @endif
            @endforeach

            <p class="text-gray-700 dark:text-gray-300 mt-4">Por favor, verifica los detalles de tu compra e intenta nuevamente.</p>

            <a href="{{ route('pages.home') }}"
               class="mt-6 inline-block px-6 py-3 text-xl font-semibold text-gray-900 bg-[#BFF205] rounded-lg hover:bg-[#A8D004] dark:bg-[#BFF205] dark:hover:bg-[#A8D004] transition-transform transform hover:scale-105">
                Volver a la página de inicio
            </a>
        </div>
    </div>

    <x-footer />
@endsection
