@extends('layouts.auth')

@section('title', 'Cambio de Contraseña')
@section('auth-content')
    <div x-data="{ step:1 } " class="p-6">
        <form action="{{ route('passchange.store') }}" method="POST" onsubmit="return validarFormulario()">
            @csrf

            <!-- Step 1: Ingresar email y pedir correo con código-->
            <div x-show="step === 1">
                <div class="flex flex-col gap-4 mb-4">
                    <label class="text-gray-700 dark:text-gray-300">Email</label>
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
                <button id="botonSiguiente" x-show="step===1" @click="step++" type ="button" class="w-full bg-blue-300 text-white p-2 rounded font-semibold dark:bg-gray-700 dark:text-white" style="display:none;" hidden>
                    Siguiente
                </button>
            </div>

            <!--Step 2: Cambiar contraseña, repetir contraseña y enviar formulario-->
            <div x-show="step === 2">
                <!-- Nueva Contraseña -->
                <label class="block text-gray-700 dark:text-gray-300">Nueva Contraseña</label>
                <div class="relative">
                    <input type="password" id="new_password" name="new_password" class="w-full p-2 border rounded mb-0 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required
                           oninput="validarCampoEnTiempoReal('password')">
                    <button type="button" onclick="togglePasswordVisibility('new_password', 'show-password', 'hide-password')" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                        <img id="show-password" src="{{ asset('imgs/password-show.svg') }}" alt="Mostrar" class="h-5 w-5">
                        <img id="hide-password" src="{{ asset('imgs/password-hide.svg') }}" alt="Ocultar" class="h-5 w-5 hidden">
                    </button>
                </div>
                <!-- Mensaje de error para la nueva contraseña -->
                <span id="passwordError" class="text-sm text-red-500"></span>
                <br>

                <!-- Repetir Contraseña -->
                <label class="block text-gray-700 dark:text-gray-300">Repetir Contraseña</label>
                <div class="relative">
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="w-full p-2 border rounded mb-0 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required
                           oninput="validarCampoEnTiempoReal('password_confirmation')">
                    <button type="button" onclick="togglePasswordVisibility('new_password_confirmation', 'show-password-confirmation', 'hide-password-confirmation')" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                        <img id="show-password-confirmation" src="{{ asset('imgs/password-show.svg') }}" alt="Mostrar" class="h-5 w-5">
                        <img id="hide-password-confirmation" src="{{ asset('imgs/password-hide.svg') }}" alt="Ocultar" class="h-5 w-5 hidden">
                    </button>
                </div>
                <!-- Mensaje de error para la confirmación de contraseña -->
                <span id="passwordConfirmationError" class="text-sm text-red-500"></span>

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
                        messageSpan.style.color = "green";
                    } else {
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
            let botonSiguiente = document.getElementById('botonSiguiente');

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
                        botonSiguiente.hidden = false;
                        botonSiguiente.style.display = 'inline-block'
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

        function togglePasswordVisibility(fieldId, showIconId, hideIconId) {
            let field = document.getElementById(fieldId);
            let showIcon = document.getElementById(showIconId);
            let hideIcon = document.getElementById(hideIconId);

            if (field.type === "password") {
                field.type = "text";
                showIcon.classList.add("hidden");
                hideIcon.classList.remove("hidden");
            } else {
                field.type = "password";
                showIcon.classList.remove("hidden");
                hideIcon.classList.add("hidden");
            }
        }

        function validarCampo(campo) {
            let errors = {};

            if (campo === 'password') {
                const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/;
                let password = document.getElementById('new_password').value;

                if (!password.trim()) {
                    errors.password = "La contraseña es obligatoria.";
                } else if (!passwordRegex.test(password)) {
                    errors.password = "La contraseña debe tener entre 8 y 20 caracteres, incluir al menos una letra mayúscula, una letra minúscula, un número y un carácter especial.";
                }
            } else if (campo === 'password_confirmation') {
                let password = document.getElementById('new_password').value;
                let passwordConfirmation = document.getElementById('new_password_confirmation').value;

                if (password !== passwordConfirmation) {
                    errors.password_confirmation = "Las contraseñas no coinciden.";
                }
            }

            return errors;
        }

        function validarFormulario() {
            let passwordErrors = validarCampo('password');
            let passwordConfirmationErrors = validarCampo('password_confirmation');

            document.getElementById('passwordError').textContent = passwordErrors.password || '';
            document.getElementById('passwordConfirmationError').textContent = passwordConfirmationErrors.password_confirmation || '';

            return Object.keys(passwordErrors).length === 0 && Object.keys(passwordConfirmationErrors).length === 0;
        }
    </script>

@endsection
