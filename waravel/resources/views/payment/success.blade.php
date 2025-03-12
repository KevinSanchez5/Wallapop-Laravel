@extends('layouts.app')

@section('title', '¡Gracias por la compra!')

@section('content')
    <x-header />
    <div class="flex flex-col min-h-screen">
        <div class="container mx-auto py-12 px-6 flex flex-col items-center text-center flex-1">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-8 max-w-xl w-full">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">¡Gracias por tu compra!</h1>
                <p class="text-gray-700 dark:text-gray-300 mt-4">
                    Apreciamos que hayas usado nuestros servicios. Si tienes alguna pregunta, no dudes en escribirnos a:
                </p>
                <p class="mt-2">
                    <a href="mailto:waravelshop@gmail.com" class="text-blue-500 dark:text-blue-400 font-semibold">
                        waravelshop@gmail.com
                    </a>
                </p>

                <div class="mt-6">
                    <a href="{{ route('pages.home') }}"
                       class="inline-block px-6 py-3 text-xl font-semibold text-gray-900 bg-[#BFF205] rounded-lg hover:bg-[#A8D004] dark:bg-[#BFF205] dark:hover:bg-[#A8D004] transition-transform transform hover:scale-105">
                        Volver a la página de inicio
                    </a>
                </div>
            </div>
        </div>
    <x-footer />
@endsection
