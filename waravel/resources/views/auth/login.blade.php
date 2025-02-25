@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('auth-content')
    <script>
        window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};

        function togglePasswordVisibility() {
            let input = document.getElementById("password");
            let showIcon = document.getElementById("show-password");
            let hideIcon = document.getElementById("hide-password");

            if (input.type === "password") {
                input.type = "text";
                showIcon.classList.add("hidden");
                hideIcon.classList.remove("hidden");
            } else {
                input.type = "password";
                showIcon.classList.remove("hidden");
                hideIcon.classList.add("hidden");
            }
        }
    </script>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="flex flex-col gap-4 mb-4">
            <label class="text-gray-700 dark:text-gray-300">Email</label>
            <div class="flex gap-2">
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            @error('email')
            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- Contraseña --}}
        <div class="flex flex-col gap-4 mb-4">
            <label class="text-gray-700 dark:text-gray-300">Contraseña</label>
            <div class="relative">
                <input id="password" type="password" name="password" required
                       class="w-full p-2 pr-10 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <button type="button" onclick="togglePasswordVisibility()"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2">
                    <img id="show-password" src="{{ asset('imgs/password-show.svg') }}" alt="Mostrar" class="h-5 w-5">
                    <img id="hide-password" src="{{ asset('imgs/password-hide.svg') }}" alt="Ocultar" class="h-5 w-5 hidden">
                </button>
            </div>
            @error('password')
            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
            @enderror
        </div>


        {{-- Recordarme --}}
        <div class="flex items-center justify-between mb-6">
            <label class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                <input type="checkbox" name="remember" class="mr-2">
                Recordarme
            </label>

            @if (Route::has('passchange'))
                <a href="{{ route('passchange') }}" class="text-sm text-blue-500 hover:underline">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        {{-- Botón --}}
        <button type="submit" class="w-full bg-black text-white p-3 rounded-md font-semibold dark:bg-gray-700 dark:text-white">
            Iniciar Sesión
        </button>
    </form>

    {{-- Enlace a Registro --}}
    <div class="text-center mt-4">
        <p class="text-gray-600 dark:text-gray-300">¿No tienes cuenta? <a href="{{ route('register') }}" class="text-blue-500 hover:underline">Regístrate</a></p>
    </div>
@endsection
