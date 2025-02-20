@extends('layouts.app')

@section('title', 'Pago Cancelado')

@section('content')
    <x-header />
        <div class="container mx-auto py-12 px-6 flex flex-col items-center text-center">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-8 max-w-xl w-full">
                <p class="text-gray-700 dark:text-gray-300">¿Olvidaste algo en tu carrito? Sigue buscando y vuelve al pago si lo deseas.</p>
                <a href="{{ route('pages.home') }}"
                   class="mt-6 inline-block px-6 py-3 text-xl font-semibold text-gray-900 bg-[#BFF205] rounded-lg hover:bg-[#A8D004] dark:bg-[#BFF205] dark:hover:bg-[#A8D004] transition-transform transform hover:scale-105">
                    Volver a la página de inicio
                </a>
            </div>
        </div>
    <x-footer />
@endsection
