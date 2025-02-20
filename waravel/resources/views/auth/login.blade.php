@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('auth-content')

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
        <input type="password" name="password" required
               class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white">
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
