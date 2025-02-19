@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('auth-content')
    <form action="#" method="POST">
        @csrf
        <label class="block text-gray-700 dark:text-gray-300">Email</label>
        <input type="email" name="email" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

        <label class="block text-gray-700 dark:text-gray-300">Contraseña</label>
        <input type="password" name="password" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

        <div class="text-right mb-4">
            <p class="text-gray-600 dark:text-gray-300">¿Has olvidado tu contraseña? <a href="{{ route('passchange') }}" class="text-green-600 font-semibold">Cambiar Contraseña</a></p>
        </div>
        <br><br>
        <button type="submit" class="w-full bg-black text-white p-2 rounded font-semibold dark:bg-gray-700 dark:text-white">
            Iniciar Sesión
        </button>
    </form>

    <div class="text-center mt-4">
        <p class="text-gray-600 dark:text-gray-300">¿No tienes cuenta? <a href="{{ route('register') }}" class="text-green-600 font-semibold">Regístrate</a></p>
    </div>
@endsection
