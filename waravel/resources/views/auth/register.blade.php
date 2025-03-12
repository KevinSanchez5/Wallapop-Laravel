@extends('layouts.auth')

@section('title', 'Registro')

@section('auth-content')
    <div x-data="registro()" class=" p-6 ">
        <form action="{{ route('register') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Step 1: Contacto e Información Personal -->
            <div x-show="step === 1">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Información de Contacto y Personal</h2>

                <label class="block text-gray-700 dark:text-gray-300">Email</label>
                <input type="email" x-model="form.email" name="email"
                       @input="validarCampo('email');"
                       @blur="validarCampo('email');"
                       class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:border-gray-700 dark:text-white" required>
                <p class="text-red-500 text-sm" x-text="errors.email"></p>


                <label class="block text-gray-700 dark:text-gray-300">Teléfono</label>
                <input type="text" x-model="form.telefono" name="telefono" @input="validarCampo('telefono')" @blur="validarCampo('telefono')" class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                <p class="text-red-500 text-sm" x-text="errors.telefono"></p>

                <div class="flex gap-2">
                    <div class="w-1/2">
                        <label class="block text-gray-700 dark:text-gray-300">Nombre</label>
                        <input type="text" x-model="form.nombre" name="nombre" @input="validarCampo('nombre')" @blur="validarCampo('nombre')" class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:border-gray-700 dark:text-white" required>
                        <p class="text-red-500 text-sm" x-text="errors.nombre"></p>
                    </div>
                    <div class="w-1/2">
                        <label class="block text-gray-700 dark:text-gray-300">Apellidos</label>
                        <input type="text" x-model="form.apellidos" name="apellidos" @input="validarCampo('apellidos')" @blur="validarCampo('apellidos')" class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:border-gray-700 dark:text-white" required>
                        <p class="text-red-500 text-sm" x-text="errors.apellidos"></p>
                    </div>
                </div>
            </div>

            <!-- Step 2: Dirección -->
            <div x-show="step === 2">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Dirección</h2>

                <input type="text" x-model="form.direccion.calle" name="direccion[calle]" @input="validarCampo('direccion.calle')" @blur="validarCampo('direccion.calle')" class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:border-gray-700 dark:text-white" required placeholder="Calle">
                <p class="text-red-500 text-sm" x-text="errors['direccion.calle']"></p>

                <input type="number" x-model="form.direccion.numero" name="direccion[numero]" @input="validarCampo('direccion.numero')" @blur="validarCampo('direccion.numero')" class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:border-gray-700 dark:text-white" required placeholder="Número">
                <p class="text-red-500 text-sm" x-text="errors['direccion.numero']"></p>

                <input type="number" name="direccion[piso]" class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:border-gray-700 dark:text-white" placeholder="Piso">
                <input type="text" x-model="form.direccion.letra" name="direccion[letra]" @input="validarCampo('direccion.letra')" @blur="validarCampo('direccion.letra')" class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:border-gray-700 dark:text-white" placeholder="Letra">
                <p class="text-red-500 text-sm" x-text="errors['direccion.letra']"></p>

                <input type="number" x-model="form.direccion.codigoPostal" name="direccion[codigoPostal]" @input="validarCampo('direccion.codigoPostal')" @blur="validarCampo('direccion.codigoPostal')" class="w-full p-2 border rounded mb-4 dark:bg-gray-800 dark:border-gray-700 dark:text-white" required placeholder="Código Postal">
                <p class="text-red-500 text-sm" x-text="errors['direccion.codigoPostal']"></p>
            </div>

            <div x-show="step === 3">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Seguridad</h2>

                <label class="block text-gray-700 dark:text-gray-300">Contraseña</label>
                <div class="relative">
                    <input type="password" x-model="form.password" name="password" @input="validarCampo('password')" @blur="validarCampo('password')" class="w-full p-2 border rounded mb-1 dark:bg-gray-800 dark:border-gray-700 dark:text-white" required>
                    <button type="button" onclick="togglePasswordVisibility('password', 'show-password', 'hide-password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                        <svg id="show-password" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16" fill="context-fill" fill-opacity="context-fill-opacity" alt="Mostrar" class="h-5 w-5">
                            <path d="M3.067 1.183a.626.626 0 0 0-.885.885l1.306 1.306A8.885 8.885 0 0 0 0 7.595l0 .809C1.325 11.756 4.507 14 8 14c1.687 0 3.294-.535 4.66-1.455l2.273 2.273a.626.626 0 0 0 .884-.886L3.067 1.183zm3.759 5.528 2.463 2.463c-.32.352-.777.576-1.289.576-.965 0-1.75-.785-1.75-1.75 0-.512.225-.969.576-1.289zM8 12.75c-3.013 0-5.669-1.856-6.83-4.75a7.573 7.573 0 0 1 3.201-3.745l1.577 1.577A2.958 2.958 0 0 0 5 8c0 1.654 1.346 3 3 3 .858 0 1.624-.367 2.168-.948l1.613 1.613A7.118 7.118 0 0 1 8 12.75z"/>
                            <path d="M8 2c-.687 0-1.356.11-2.007.275l1.049 1.049A7.06 7.06 0 0 1 8 3.25c3.013 0 5.669 1.856 6.83 4.75a7.925 7.925 0 0 1-1.141 1.971l.863.863A9.017 9.017 0 0 0 16 8.404l0-.809C14.675 4.244 11.493 2 8 2z"/>
                        </svg>

                        <svg id="hide-password" alt="Ocultar" class="h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16" fill="context-fill" fill-opacity="context-fill-opacity">
                            <path d="M16 7.595C14.675 4.244 11.493 2 8 2S1.325 4.244 0 7.595l0 .809C1.325 11.756 4.507 14 8 14s6.675-2.244 8-5.595l0-.81zM8 12.75c-3.013 0-5.669-1.856-6.83-4.75C2.331 5.106 4.987 3.25 8 3.25S13.669 5.106 14.83 8c-1.161 2.894-3.817 4.75-6.83 4.75z"/>
                            <path d="M8 11c-1.654 0-3-1.346-3-3s1.346-3 3-3 3 1.346 3 3-1.346 3-3 3zm0-4.75c-.965 0-1.75.785-1.75 1.75S7.035 9.75 8 9.75 9.75 8.965 9.75 8 8.965 6.25 8 6.25z"/>
                        </svg>
                    </button>
                </div>
                <p class="text-red-500 text-sm mt-2" x-text="errors.password"></p>

                <label class="block text-gray-700 dark:text-gray-300">Repetir contraseña</label>
                <div class="relative">
                    <input type="password" x-model="form.password_confirmation" name="password_confirmation" @input="validarCampo('password_confirmation')" @blur="validarCampo('password_confirmation')" class="w-full p-2 border rounded mb-1 dark:bg-gray-800 dark:border-gray-700 dark:text-white" required>
                    <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'show-password-confirm', 'hide-password-confirm')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                        <svg id="show-password" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16" fill="context-fill" fill-opacity="context-fill-opacity" alt="Mostrar" class="h-5 w-5">
                            <path d="M3.067 1.183a.626.626 0 0 0-.885.885l1.306 1.306A8.885 8.885 0 0 0 0 7.595l0 .809C1.325 11.756 4.507 14 8 14c1.687 0 3.294-.535 4.66-1.455l2.273 2.273a.626.626 0 0 0 .884-.886L3.067 1.183zm3.759 5.528 2.463 2.463c-.32.352-.777.576-1.289.576-.965 0-1.75-.785-1.75-1.75 0-.512.225-.969.576-1.289zM8 12.75c-3.013 0-5.669-1.856-6.83-4.75a7.573 7.573 0 0 1 3.201-3.745l1.577 1.577A2.958 2.958 0 0 0 5 8c0 1.654 1.346 3 3 3 .858 0 1.624-.367 2.168-.948l1.613 1.613A7.118 7.118 0 0 1 8 12.75z"/>
                            <path d="M8 2c-.687 0-1.356.11-2.007.275l1.049 1.049A7.06 7.06 0 0 1 8 3.25c3.013 0 5.669 1.856 6.83 4.75a7.925 7.925 0 0 1-1.141 1.971l.863.863A9.017 9.017 0 0 0 16 8.404l0-.809C14.675 4.244 11.493 2 8 2z"/>
                        </svg>

                        <svg id="hide-password" alt="Ocultar" class="h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16" fill="context-fill" fill-opacity="context-fill-opacity">
                            <path d="M16 7.595C14.675 4.244 11.493 2 8 2S1.325 4.244 0 7.595l0 .809C1.325 11.756 4.507 14 8 14s6.675-2.244 8-5.595l0-.81zM8 12.75c-3.013 0-5.669-1.856-6.83-4.75C2.331 5.106 4.987 3.25 8 3.25S13.669 5.106 14.83 8c-1.161 2.894-3.817 4.75-6.83 4.75z"/>
                            <path d="M8 11c-1.654 0-3-1.346-3-3s1.346-3 3-3 3 1.346 3 3-1.346 3-3 3zm0-4.75c-.965 0-1.75.785-1.75 1.75S7.035 9.75 8 9.75 9.75 8.965 9.75 8 8.965 6.25 8 6.25z"/>
                        </svg>
                    </button>
                </div>
                <p class="text-red-500 text-sm" x-text="errors.password_confirmation"></p>
            </div>

            <!-- Botones de navegación -->
            <div class="flex justify-between mt-4">
                <button type="button" x-show="step > 1" @click="step--"
                        class="bg-[#BFF205] text-black px-4 py-2 rounded transition duration-300 hover:bg-[#a8d904] hover:scale-105">
                    <b>Anterior</b>
                </button>
                <button type="button" x-show="step < 3" @click="validarStep()"
                        class="bg-[#BFF205] text-black px-4 py-2 rounded transition duration-300 hover:bg-[#a8d904] hover:scale-105">
                    <b>Siguiente</b>
                </button>
                <button type="submit" x-show="step === 3" @click.prevent="enviarFormulario()"
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
    <script>
        function togglePasswordVisibility(inputId, showIconId, hideIconId) {
            let input = document.querySelector(`input[name="${inputId}"]`);
            let showIcon = document.getElementById(showIconId);
            let hideIcon = document.getElementById(hideIconId);

            if (input.type === "password") {
                input.type = "text";
                showIcon.classList.add("hidden");
                hideIcon.classList.remove("hidden");
            } else {
                input.type = "password";
                showIcon.classList.remove("hidden");
                hideIcon.classList.add("hidden");
            }
        }

        function registro() {
            return {
                step: 1,
                form: {
                    email: '',
                    telefono: '',
                    nombre: '',
                    apellidos: '',
                    direccion: { calle: '', numero: '', letra: '', codigoPostal: '' },
                    password: '',
                    password_confirmation: ''
                },
                errors: {},

                validarCampo(campo) {
                    if (this.errors[campo]) {
                        delete this.errors[campo];
                    }

                    if (campo === 'email') {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(this.form.email)) {
                            this.errors.email = "El email no es válido.";
                        }
                    } else if (campo === 'telefono') {
                        let telefono = this.form.telefono.trim();
                        if (!telefono) {
                            this.errors.telefono = "El teléfono es obligatorio.";
                        } else {
                            if (!/^[67]/.test(telefono)) {
                                this.errors.telefono = "El teléfono debe empezar por 6 o 7.";
                            }
                            if (!/^\d{9}$/.test(telefono)) {
                                this.errors.telefono = this.errors.telefono ? this.errors.telefono + " y tener 9 dígitos." : "El teléfono debe tener 9 dígitos.";
                            }
                        }
                    } else if (campo === 'nombre') {
                        if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(this.form.nombre.trim())) {
                            this.errors.nombre = "El nombre solo puede contener letras.";
                        }
                    } else if (campo === 'apellidos') {
                        if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(this.form.apellidos.trim())) {
                            this.errors.apellidos = "Los apellidos solo pueden contener letras.";
                        }
                    } else if (campo === 'direccion.calle') {
                        if (!this.form.direccion.calle.trim()) {
                            this.errors["direccion.calle"] = "La calle es obligatoria.";
                        }
                    } else if (campo === 'direccion.numero') {
                        if (!this.form.direccion.numero || isNaN(this.form.direccion.numero) || this.form.direccion.numero <= 0) {
                            this.errors["direccion.numero"] = "El número es obligatorio, debe ser un número y no puede ser negativo.";
                        }
                    } else if (campo === 'direccion.letra') {
                        if (this.form.direccion.letra.trim() && !/^[a-zA-Z]+$/.test(this.form.direccion.letra.trim())) {
                            this.errors["direccion.letra"] = "La letra solo puede contener letras.";
                        }
                    } else if (campo === 'direccion.codigoPostal') {
                        const postalCodeRegex = /^\d{5}$/;
                        if (!postalCodeRegex.test(this.form.direccion.codigoPostal.trim())) {
                            this.errors["direccion.codigoPostal"] = "El código postal es obligatorio y debe ser un código postal válido.";
                        }
                    } else if (campo === 'password') {
                        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/;
                        if (!this.form.password.trim()) {
                            this.errors.password = "La contraseña es obligatoria.";
                        } else if (!passwordRegex.test(this.form.password)) {
                            this.errors.password = "La contraseña debe tener entre 8 y 20 caracteres, incluir al menos una letra mayúscula, una letra minúscula, un número y un carácter especial.";
                            if (this.form.password !== this.form.password_confirmation) {
                                this.errors.password_confirmation = "Las contraseñas no coinciden.";
                            }
                        }
                    }
                },

                async validarEmail() {
                    if (!this.form.email) return;

                    try {
                        let response = await fetch(`api/users/verificar-correo/${this.form.email}`);
                        let data = await response.json();

                        if (data.exists) {
                            this.errors.email = "El email ya está en uso.";
                        } else {
                            delete this.errors.email;
                        }
                    } catch (error) {
                        console.error("Error al verificar el email", error);
                    }
                },

                async validarStep() {
                    this.errors = {}; // Clear all errors before validating

                    if (this.step === 1) {
                        this.validarCampo('email');
                        this.validarCampo('telefono');
                        this.validarCampo('nombre');
                        this.validarCampo('apellidos');

                        await this.validarEmail(); // Validate email before moving to the next step

                        if (Object.keys(this.errors).length === 0) {
                            this.step++; // Advance to the next step if there are no errors
                        }
                    } else if (this.step === 2) {
                        this.validarCampo('direccion.calle');
                        this.validarCampo('direccion.numero');
                        this.validarCampo('direccion.codigoPostal');

                        if (Object.keys(this.errors).length === 0) {
                            this.step++; // Advance to the next step if there are no errors
                        }
                    } else if (this.step === 3) {
                        this.validarCampo('password');
                        this.validarCampo('password_confirmation');

                        if (Object.keys(this.errors).length === 0 && this.step < 3) {
                            this.step++;
                        }
                    }
                },

                enviarFormulario() {
                    this.validarStep();

                    if (Object.keys(this.errors).length === 0) {
                        document.querySelector('form').submit();
                    }
                }
            };
        }
    </script>
@endsection
