@extends('layouts.app')

@section('content')
    <div class="flex min-h-screen" style="margin-top: -24px">
        <div class="w-1/2 bg-cover bg-center flex items-center justify-center text-white"
             style="background-image: url({{ asset('imgs/fondo_auth.jpg') }});">
        </div>

        <div class="w-1/2 flex items-center justify-center bg-white dark:bg-gray-900 p-10 relative">
            <a class="absolute top-4 right-4 bg-gray-200 dark:bg-gray-700 p-2 rounded-full shadow-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9m0 0l9 9m-9-9v18" />
                </svg>
            </a>

            <div class="w-96">
                <div class="flex justify-center mb-4">
                    <img id="logo" src="{{ asset('imgs/logo_negro.png') }}" alt="Logo" class="w-30 dark:hidden">
                    <img id="logo_blanco" src="{{ asset('imgs/logo_blanco.png') }}" alt="Logo" class="w-30 hidden dark:block">
                </div>

                <h2 class="text-2xl font-bold text-center mb-6 dark:text-white">@yield('title')</h2>

                @yield('auth-content')

            </div>
        </div>
    </div>
@endsection
