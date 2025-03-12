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
                    <svg id="show-password" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16" fill="context-fill" fill-opacity="context-fill-opacity" alt="Mostrar" class="h-5 w-5">
                        <path d="M3.067 1.183a.626.626 0 0 0-.885.885l1.306 1.306A8.885 8.885 0 0 0 0 7.595l0 .809C1.325 11.756 4.507 14 8 14c1.687 0 3.294-.535 4.66-1.455l2.273 2.273a.626.626 0 0 0 .884-.886L3.067 1.183zm3.759 5.528 2.463 2.463c-.32.352-.777.576-1.289.576-.965 0-1.75-.785-1.75-1.75 0-.512.225-.969.576-1.289zM8 12.75c-3.013 0-5.669-1.856-6.83-4.75a7.573 7.573 0 0 1 3.201-3.745l1.577 1.577A2.958 2.958 0 0 0 5 8c0 1.654 1.346 3 3 3 .858 0 1.624-.367 2.168-.948l1.613 1.613A7.118 7.118 0 0 1 8 12.75z"/>
                        <path d="M8 2c-.687 0-1.356.11-2.007.275l1.049 1.049A7.06 7.06 0 0 1 8 3.25c3.013 0 5.669 1.856 6.83 4.75a7.925 7.925 0 0 1-1.141 1.971l.863.863A9.017 9.017 0 0 0 16 8.404l0-.809C14.675 4.244 11.493 2 8 2z"/>
                    </svg>

                    <svg id="hide-password" alt="Ocultar" class="h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16" fill="context-fill" fill-opacity="context-fill-opacity">
                        <path d="M16 7.595C14.675 4.244 11.493 2 8 2S1.325 4.244 0 7.595l0 .809C1.325 11.756 4.507 14 8 14s6.675-2.244 8-5.595l0-.81zM8 12.75c-3.013 0-5.669-1.856-6.83-4.75C2.331 5.106 4.987 3.25 8 3.25S13.669 5.106 14.83 8c-1.161 2.894-3.817 4.75-6.83 4.75z"/>
                        <path d="M8 11c-1.654 0-3-1.346-3-3s1.346-3 3-3 3 1.346 3 3-1.346 3-3 3zm0-4.75c-.965 0-1.75.785-1.75 1.75S7.035 9.75 8 9.75 9.75 8.965 9.75 8 8.965 6.25 8 6.25z"/>
                    </svg>
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
