@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('auth-content')

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label class="block text-gray-700 dark:text-gray-300">Email</label>
        <input type="email" name="email" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

        <label class="block text-gray-700 dark:text-gray-300">Contraseña</label>
        <input type="password" name="password" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

        <div class="text-right mb-4">
            @if (Route::has('passchange'))
                <a class="text-gray-600 dark:text-gray-300" href="{{ route('passchange') }}">
                    {{ __('¿Has olvidado tu contraseña?') }}
                </a>
            @endif
        </div>
        <br><br>
        <button type="submit" class="w-full bg-black text-white p-2 rounded font-semibold dark:bg-gray-700 dark:text-white">
            {{ __('Iniciar Sesión') }}
        </button>
        <a class="text-gray-600 dark:text-gray-300" href="{{ route('register') }}">{{ __('¿No tienes cuenta?') }}</a>
    </form>

@endsection
