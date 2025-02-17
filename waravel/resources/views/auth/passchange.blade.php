@extends('layouts.app')

@section('title', 'Cambio de Contraseña')

@section('content')
    <div class="flex min-h-screen" style="margin-top: -24px">
        <!-- Sección Izquierda -->
        <div class="w-1/2 bg-cover bg-center flex items-center justify-center text-white" style="background-image: url({{ asset('imgs/fondo_auth.jpg') }}");>
    </div>

    <!-- Sección Derecha (Formulario) -->
    <div class="w-1/2 flex items-center justify-center bg-white p-10">
        <div class="w-96">
            <!-- Icono -->
            <div class="flex justify-center mb-4">
                <img src="{{ asset('imgs/logo_negro.png') }}" alt="Passchange Icon" class="w-30">
            </div>

            <h2 class="text-2xl font-bold text-center mb-6">Cambio de Contraseña</h2>

            <form action="#" method="POST">
                @csrf
                <!-- Campo Email -->
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" class="w-full p-2 border rounded mb-4">

                <!-- Botón para enviar el email de verificación -->
                <button type="button" class="w-full bg-blue-500 text-white p-2 rounded font-semibold mb-4">
                    Enviar Email de Verificación
                </button>

                <!-- Campo Nueva Contraseña -->
                <label class="block text-gray-700">Nueva Contraseña</label>
                <input type="password" name="new_password" class="w-full p-2 border rounded mb-4">

                <!-- Campo Repetir Contraseña -->
                <label class="block text-gray-700">Repetir Contraseña</label>
                <input type="password" name="new_password_confirmation" class="w-full p-2 border rounded mb-4">

                <!-- Campo Código de Verificación -->
                <label class="block text-gray-700">Código de Verificación</label>
                <input type="text" name="verification_code" class="w-full p-2 border rounded mb-4">

                <button type="submit" class="w-full bg-black text-white p-2 rounded font-semibold">
                    Cambiar Contraseña
                </button>
            </form>

            <div class="text-center mt-4">
                <p>¿Recordaste tu contraseña? <a href="{{ route('login') }}" class="text-green-600 font-semibold">Iniciar sesión</a></p>
            </div>
        </div>
    </div>
    </div>
@endsection
