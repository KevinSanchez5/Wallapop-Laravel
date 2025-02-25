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
                        <img id="show-password" src="{{ asset('imgs/password-show.svg') }}" alt="Show Password" class="h-5 w-5">
                        <img id="hide-password" src="{{ asset('imgs/password-hide.svg') }}" alt="Hide Password" class="h-5 w-5 hidden">
                    </button>
                </div>
                <p class="text-red-500 text-sm mt-2" x-text="errors.password"></p>

                <label class="block text-gray-700 dark:text-gray-300">Repetir contraseña</label>
                <div class="relative">
                    <input type="password" x-model="form.password_confirmation" name="password_confirmation" @input="validarCampo('password_confirmation')" @blur="validarCampo('password_confirmation')" class="w-full p-2 border rounded mb-1 dark:bg-gray-800 dark:border-gray-700 dark:text-white" required>
                    <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'show-password-confirm', 'hide-password-confirm')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                        <img id="show-password-confirm" src="{{ asset('imgs/password-show.svg') }}" alt="Show Password" class="h-5 w-5">
                        <img id="hide-password-confirm" src="{{ asset('imgs/password-hide.svg') }}" alt="Hide Password" class="h-5 w-5 hidden">
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
                        let response = await fetch(`/api/verificar-email?email=${this.form.email}`);
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
