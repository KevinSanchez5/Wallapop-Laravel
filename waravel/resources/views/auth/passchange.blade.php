@extends('layouts.auth')

@section('title', 'Cambio de Contraseña')
@section('auth-content')
    <div x-data="{ step:1 } " class="p-6">
        <form action="#" method="POST">
            @csrf

            <!-- Step 1: Ingresar email y pedir correo con código-->
            <div x-show="step === 1">
                <div class="flex flex-col gap-4 mb-4">
                    <label style="margin-bottom: -15px" class="text-gray-700 dark:text-gray-300">Email</label>
                    <div class="flex gap-2">
                        <input type="email" id="email" name="email" class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <button type="button" onclick="enviarCorreoCodigo()" class="bg-[#c5fc00] text-black p-2 rounded font-semibold dark:bg-[#c5fc00]">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    <span id="sendCodeMessage" class="text-sm font-semibold"></span>
                </div>
                <label class="block text-gray-700 dark:text-gray-300">Código de Verificación</label>
                <input type="text" id ="codigo" name="codigo" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <span id="verifyCodeMessage" class="text-sm font-semibold"></span><br><br>

                <button type="button" onclick="verificarCodigo()" class="bg-blue-500 text-white p-2 rounded">
                    Verificar Código
                </button>
            </div>

            <!--Step 2: Cambiar contraseña, repetir contraseña y enviar formulario-->
            <div x-show="step === 2">
                <label class="block text-gray-700 dark:text-gray-300">Nueva Contraseña</label>
                <input type="password" name="new_password" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

                <label class="block text-gray-700 dark:text-gray-300">Repetir Contraseña</label>
                <input type="password" name="new_password_confirmation" class="w-full p-2 border rounded mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white">


                <br><br><br>
                <button type="submit" class="w-full bg-black text-white p-2 rounded font-semibold dark:bg-gray-700 dark:text-white">
                    Cambiar Contraseña
                </button>
            </div>
        </form>
    </div>

    <div class="text-center mt-4">
        <p class="text-gray-600 dark:text-gray-300">¿Recordaste tu contraseña? <a href="{{ route('login') }}" class="text-green-600 font-semibold">Iniciar sesión</a></p>
    </div>

    <script>
        function enviarCorreoCodigo() {
            let email = document.getElementById('email').value;
            let messageSpan = document.getElementById('sendCodeMessage');

            if (!email.trim()) {
                messageSpan.textContent = "Por favor, ingresa un correo válido.";
                messageSpan.style.color = "red";
                return;
            }

            fetch("/api/users/correo-codigo", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ email: email })
            })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.success) {
                        messageSpan.textContent = data.message;
                        messageSpan.style.color = "green";
                    } else {
                        messageSpan.textContent = "No se encontró un usuario con este correo.";
                        messageSpan.style.color = "red";
                    }
                })
                .catch(error => {
                    console.log(error);
                    messageSpan.textContent = "Hubo un error al enviar el código.";
                    messageSpan.style.color = "red";
                });
        }

        function verificarCodigo() {
            let email = document.getElementById('email').value;
            let codigo = document.getElementById('codigo').value;
            let messageSpan = document.getElementById('verifyCodeMessage');
            const component = document.querySelector('[x-data]');

            if (!email.trim() || !codigo.trim()) {
                messageSpan.textContent = "Por favor, ingresa el correo y el código.";
                messageSpan.style.color = "red";
                return;
            }

            fetch("/api/users/verificar-codigo", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ email: email, codigo: codigo })
            })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.success) {
                        messageSpan.textContent = data.message;
                        messageSpan.style.color = "green";
                        // Si el código es correcto, pasar al Step 2
                        Alpine.store('step', 2);
                    } else {
                        messageSpan.textContent = data.message;
                        messageSpan.style.color = "red";
                    }
                })
                .catch(error => {
                    console.log(error);
                    messageSpan.textContent = "Hubo un error al verificar el código.";
                    messageSpan.style.color = "red";
                });
        }
    </script>

@endsection
