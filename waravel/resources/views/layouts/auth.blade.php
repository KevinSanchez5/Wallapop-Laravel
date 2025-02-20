@extends('layouts.app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="flex min-h-screen">
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
                <div class="flex justify-center mb-4 text-black dark:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 34.45 30.81" style="height: 130px; width: 130px" fill="currentColor">
                        <circle cx="8.18" cy="6.32" r="6.32"/>
                        <circle cx="26.27" cy="6.32" r="6.32"/>
                        <path d="M14.91 30.81a1.44 1.44 0 0 0 1.44-1.44V15.9a1.44 1.44 0 0 0-1.44-1.44H1.44A1.44 1.44 0 0 0 0 15.9c0 7.1 7.6 14.91 14.91 14.91Z"/>
                        <path d="M19.54 30.81a1.44 1.44 0 0 1-1.44-1.44V15.9a1.44 1.44 0 0 1 1.44-1.44h13.47a1.44 1.44 0 0 1 1.44 1.44c0 7.1-7.6 14.91-14.91 14.91Z"/>
                    </svg>
                </div>

                <h2 class="text-2xl font-bold text-center mb-6 dark:text-white">@yield('title')</h2>

                @yield('auth-content')

            </div>
        </div>
    </div>
@endsection
