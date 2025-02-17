@extends('layouts.app')

@section('title', 'Registro')

@section('content')
    <div class="flex min-h-screen" style="margin-top: -24px">
        <div class="w-1/2 bg-gradient-to-r from-black via-transparent to-transparent bg-cover bg-center flex items-center justify-center text-white"
             style="background-image: url({{ asset('imgs/fondo_auth.jpg') }});">
        </div>

        <div class="w-1/2 flex items-center justify-center bg-white p-10 dark:bg-gray-900">
            <div class="w-96">
                <div class="flex justify-center mb-4">
                    <!-- Logo en modo claro -->
                    <img id="logo" src="{{ asset('imgs/logo_negro.png') }}" alt="Register Icon" class="w-30 dark:hidden">
                    <!-- Logo en modo oscuro -->
                    <img id="logo_blanco" src="{{ asset('imgs/logo_blanco.png') }}" alt="Register Icon" class="w-30 hidden dark:block">
                </div>

                <h2 class="text-2xl font-bold text-center mb-6 dark:text-white">SignUp</h2>

                <form action="#" method="POST">
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

                    <button type="submit" class="w-full bg-black text-white p-2 rounded font-semibold dark:bg-gray-700 dark:text-white">
                        Registrarse
                    </button>
                </form>

                <div class="text-center mt-4">
                    <p class="text-gray-600 dark:text-gray-300">Ya tienes cuenta? <a href="{{ route('login') }}" class="text-green-600 font-semibold">Iniciar sesión</a></p>
                </div>
            </div>
        </div>
    </div>
@endsection
