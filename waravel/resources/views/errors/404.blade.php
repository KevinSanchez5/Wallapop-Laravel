@extends('layouts.app')

@section('title', 'Página no encontrada')

@section('content')
    <div class="relative flex flex-col min-h-screen" style="background-image: url({{ asset('imgs/fondo_auth.jpg') }}); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="relative flex-grow flex flex-col items-center justify-center text-center px-6">
            <div style="background-color:#cbd5e0" class="p-10 rounded-xl shadow-lg">
                <h1 class="text-[12rem] font-extrabold text-gray-900 dark:text-white">404</h1>
                <p class="text-6xl text-gray-600 dark:text-gray-300 mt-6">¡Ups! La página que buscas no existe.</p>
                <p class="text-4xl text-gray-500 dark:text-gray-400 mt-4">Es posible que haya sido eliminada o que la URL sea incorrecta.</p>
                <a href="{{ route('pages.home') }}" class="mt-6 inline-block px-6 py-3 text-2xl font-semibold text-gray-900 bg-[#BFF205] rounded-lg hover:bg-[#A8D004] dark:bg-[#BFF205] dark:hover:bg-[#A8D004]">Volver a la página de inicio</a>
            </div>
        </div>
    </div>
@endsection
