@extends('layouts.auth')

@section('title', 'Registro')

@section('auth-content')
    <div x-data="{ step: 1 }" class=" p-6 ">
        <form action="{{ route('register') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Step 1: Contacto e Información Personal -->
            <div x-show="step === 1">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Información de Contacto y Personal</h2>

                <label class="block text-gray-700 dark:text-gray-300">Email</label>
                <input type="email" name="email" class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:border-gray-700 dark:text-white" required>

                <label class="block text-gray-700 dark:text-gray-300">Teléfono</label>
                <input type="text" name="telefono" class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:border-gray-700 dark:text-white" required>

                <div class="flex gap-2">
                    <div class="w-1/2">
                        <label class="block text-gray-700 dark:text-gray-300">Nombre</label>
                        <input type="text" name="nombre" class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:border-gray-700 dark:text-white" required>
                    </div>
                    <div class="w-1/2">
                        <label class="block text-gray-700 dark:text-gray-300">Apellidos</label>
                        <input type="text" name="apellidos" class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:border-gray-700 dark:text-white" required>
                    </div>
                </div>
            </div>

            <!-- Step 2: Dirección -->
            <div x-show="step === 2">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Dirección</h2>

                <input type="text" name="direccion[calle]" class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:border-gray-700 dark:text-white" required placeholder="Calle">
                <input type="number" name="direccion[numero]" class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:border-gray-700 dark:text-white" required placeholder="Número">
                <input type="number" name="direccion[piso]" class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:border-gray-700 dark:text-white" placeholder="Piso">
                <input type="text" name="direccion[letra]" class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:border-gray-700 dark:text-white" placeholder="Letra">
                <input type="number" name="direccion[codigoPostal]" class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:border-gray-700 dark:text-white" required placeholder="Código Postal">
            </div>

            <!-- Step 3: Seguridad -->
            <div x-show="step === 3">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Seguridad</h2>

                <label class="block text-gray-700 dark:text-gray-300">Contraseña</label>
                <input type="password" name="password" class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:border-gray-700 dark:text-white" required>

                <label class="block text-gray-700 dark:text-gray-300">Repetir contraseña</label>
                <input type="password" name="password_confirmation" class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:border-gray-700 dark:text-white" required>
            </div>

            <!-- Botones de navegación -->
            <div class="flex justify-between mt-4">
                <button type="button" x-show="step > 1" @click="step--"
                        class="bg-[#BFF205] text-black px-4 py-2 rounded transition duration-300 hover:bg-[#a8d904] hover:scale-105">
                    <b>Anterior</b>
                </button>
                <button type="button" x-show="step < 3" @click="step++"
                        class="bg-[#BFF205] text-black px-4 py-2 rounded transition duration-300 hover:bg-[#a8d904] hover:scale-105">
                    <b>Siguiente</b>
                </button>
                <button type="submit" x-show="step === 3"
                        class="bg-black text-white px-4 py-2 rounded transition duration-300 hover:bg-[#a8d904] hover:scale-105">
                    <b>Registrarse</b>
                </button>
            </div>
        </form>

        <div class="text-center mt-4">
            <p class="text-gray-600 dark:text-gray-300">¿Ya tienes cuenta?
                <a href="{{ route('login') }}" class="text-blue-500 font-semibold">Iniciar sesión</a>
            </p>
        </div>
    </div>
@endsection
