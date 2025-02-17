@extends('layouts.app')

@section('title', 'Cambio de Contraseña')

@section('content')
    <div class="flex min-h-screen" style="margin-top: -24px">
        <div class="w-1/2 bg-cover bg-center flex items-center justify-center text-white" style="background-image: url({{ asset('imgs/fondo_auth.jpg') }});">
        </div>

        <div class="w-1/2 flex items-center justify-center bg-white dark:bg-gray-900 p-10">
            <div class="w-96">
                <div class="flex justify-center mb-4">
                    <!-- Logo en modo claro -->
                    <img id="logo" src="{{ asset('imgs/logo_negro.png') }}" alt="Passchange Icon" class="w-30 dark:hidden">
                    <!-- Logo en modo oscuro -->
                    <img id="logo_blanco" src="{{ asset('imgs/logo_blanco.png') }}" alt="Passchange Icon" class="w-30 hidden dark:block">
                </div>

                <h2 class="text-2xl font-bold text-center mb-6 dark:text-white">Cambio de Contraseña</h2>

                <form action="#" method="POST">
                    @csrf

                    <div class="flex flex-col gap-4 mb-4">
                        <label style="margin-bottom: -15px" class="text-gray-700 dark:text-gray-300">Email</label>
                        <div class="flex gap-2">
                            <input type="email" name="email" class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <button type="button" class="bg-[#c5fc00] text-black p-2 rounded font-semibold dark:bg-[#c5fc00]">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>

                    <label class="block text-gray-700 dark:text-gray-300">Nueva Contraseña</label>
                    <input type="password" name="new_password" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

                    <label class="block text-gray-700 dark:text-gray-300">Repetir Contraseña</label>
                    <input type="password" name="new_password_confirmation" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

                    <label class="block text-gray-700 dark:text-gray-300">Código de Verificación</label>
                    <input type="text" name="verification_code" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <br><br><br>
                    <button type="submit" class="w-full bg-black text-white p-2 rounded font-semibold dark:bg-gray-700 dark:text-white">
                        Cambiar Contraseña
                    </button>
                </form>

                <div class="text-center mt-4">
                    <p class="text-gray-600 dark:text-gray-300">¿Recordaste tu contraseña? <a href="{{ route('login') }}" class="text-green-600 font-semibold">Iniciar sesión</a></p>
                </div>
            </div>
        </div>
    </div>
@endsection
