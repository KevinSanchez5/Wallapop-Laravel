@extends('layouts.auth')

@section('title', 'Registro')

@section('auth-content')
    <form action="{{ route('register') }}" method="POST">
        @csrf
        <!-- Email -->
        <label class="block text-gray-700 dark:text-gray-300">Email</label>
        <input type="email" name="email" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>

        <!-- Teléfono -->
        <label class="block text-gray-700 dark:text-gray-300">Teléfono</label>
        <input type="text" name="telefono" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>

        <div class="flex gap-2">
            <div class="w-1/2">
                <!-- Nombre -->
                <label class="block text-gray-700 dark:text-gray-300">Nombre</label>
                <input type="text" name="nombre" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
            </div>
            <div class="w-1/2">
                <!-- Apellidos -->
                <label class="block text-gray-700 dark:text-gray-300">Apellidos</label>
                <input type="text" name="apellidos" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
            </div>
        </div>

        <!-- Dirección -->
        <label class="block text-gray-700 dark:text-gray-300">Dirección</label>
        <input type="text" name="direccion[calle]" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required placeholder="Calle">
        <input type="number" name="direccion[numero]" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required placeholder="Número">
        <input type="number" name="direccion[piso]" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required placeholder="Piso">
        <input type="text" name="direccion[letra]" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required placeholder="Letra">
        <input type="number" name="direccion[codigoPostal]" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required placeholder="Código Postal">

        <!-- Contraseña -->
        <label class="block text-gray-700 dark:text-gray-300">Contraseña</label>
        <input type="password" name="password" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>

        <!-- Confirmar Contraseña -->
        <label class="block text-gray-700 dark:text-gray-300">Repetir contraseña</label>
        <input type="password" name="password_confirmation" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>

        <br><br>
        <button type="submit" class="w-full bg-black text-white p-2 rounded font-semibold dark:bg-gray-700 dark:text-white">
            Registrarse
        </button>
    </form>

    <div class="text-center mt-4">
        <p class="text-gray-600 dark:text-gray-300">¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-green-600 font-semibold">Iniciar sesión</a></p>
    </div>
@endsection
