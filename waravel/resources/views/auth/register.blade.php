@extends('layouts.auth')

@section('title', 'Registro')

@section('auth-content')
    <form action="#" method="POST">
        @csrf
        <label class="block text-gray-700 dark:text-gray-300">Email</label>
        <input type="email" name="email" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

        <div class="flex gap-2">
            <div class="w-1/2">
                <label class="block text-gray-700 dark:text-gray-300">Nombre</label>
                <input type="text" name="nombre" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            <div class="w-1/2">
                <label class="block text-gray-700 dark:text-gray-300">Apellidos</label>
                <input type="text" name="apellidos" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
        </div>

        <label class="block text-gray-700 dark:text-gray-300">Dirección</label>
        <input type="text" name="direccion" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

        <label class="block text-gray-700 dark:text-gray-300">Contraseña</label>
        <input type="password" name="password" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

        <label class="block text-gray-700 dark:text-gray-300">Repetir contraseña</label>
        <input type="password" name="password_confirmation" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

        <br><br>
        <button type="submit" class="w-full bg-black text-white p-2 rounded font-semibold dark:bg-gray-700 dark:text-white">
            Registrarse
        </button>
    </form>

    <div class="text-center mt-4">
        <p class="text-gray-600 dark:text-gray-300">¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-green-600 font-semibold">Iniciar sesión</a></p>
    </div>
@endsection
