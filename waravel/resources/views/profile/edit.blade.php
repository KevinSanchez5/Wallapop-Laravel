@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
    <x-header />
    <br><br>
    <div class="max-w-3xl mx-auto p-6 bg-white dark:bg-gray-900 shadow-md rounded-lg">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Editar Perfil</h2>

        <form id="editProfileForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Avatar -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 mb-2">Imagen de perfil</label>
                <div class="relative border border-dashed border-gray-400 rounded-lg flex items-center justify-center w-32 h-32 cursor-pointer bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 overflow-hidden transition mx-auto">
                    <label for="avatar" class="absolute inset-0 flex items-center justify-center">
                        <input type="file" id="avatar" name="avatar" class="sr-only" accept="image/*" onchange="previewImage(event)">
                        @if($cliente->avatar)
                            <img id="preview" src="{{ asset('storage/' . $cliente->avatar) }}" class="absolute inset-0 w-full h-full object-cover rounded-md" alt="">
                        @else
                            <img id="preview" src="" class="absolute inset-0 w-full h-full object-cover hidden rounded-md" alt="">
                        @endif
                        <svg id="icon" class="size-10 text-gray-300 transition-opacity {{ $cliente->avatar ? 'hidden' : '' }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z" clip-rule="evenodd" />
                        </svg>
                    </label>
                </div>
                @error('avatar')
                <p class="text-red-500 text-sm mt-1 text-center">{{ $message }}</p>
                @enderror
            </div>

            <!-- Datos Personales -->
            <div class="flex gap-4 mb-4">
                <div class="w-1/2">
                    <label for="nombre" class="block text-gray-700 dark:text-gray-300">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $cliente->nombre) }}"
                           class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#BFF205] transition-all"
                           required>
                    @error('nombre')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="w-1/2">
                    <label for="apellidos" class="block text-gray-700 dark:text-gray-300">Apellidos</label>
                    <input type="text" id="apellidos" name="apellidos" value="{{ old('apellidos', $cliente->apellido) }}"
                           class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#BFF205] transition-all"
                           required>
                    @error('apellidos')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Email (bloqueado) -->
            <div class="mb-4">
                <label for="email" class="block text-gray-700 dark:text-gray-300">Correo Electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                       class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 dark:text-white cursor-not-allowed"
                       disabled>
            </div>

            <!-- Teléfono -->
            <div class="mb-4">
                <label for="telefono" class="block text-gray-700 dark:text-gray-300">Teléfono</label>
                <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $cliente->telefono) }}"
                       class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#BFF205] transition-all"
                       required>
                @error('telefono')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dirección -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="direccion_calle" class="block text-gray-700 dark:text-gray-300">Calle</label>
                    <input type="text" id="direccion_calle" name="direccion[calle]" value="{{ old('direccion.calle', $cliente->direccion->calle ?? '') }}"
                           class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#BFF205] transition-all"
                           required>
                    @error('direccion.calle')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="direccion_numero" class="block text-gray-700 dark:text-gray-300">Número</label>
                    <input type="number" id="direccion_numero" name="direccion[numero]" value="{{ old('direccion.numero', $cliente->direccion->numero ?? '') }}"
                           class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#BFF205] transition-all"
                           required>
                    @error('direccion.numero')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex gap-4">
                    <div class="w-1/3">
                        <label for="direccion_piso" class="block text-gray-700 dark:text-gray-300">Piso</label>
                        <input type="number" id="direccion_piso" name="direccion[piso]" value="{{ old('direccion.piso', $cliente->direccion->piso ?? '') }}"
                               class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#BFF205] transition-all">
                        @error('direccion.piso')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="w-1/3">
                        <label for="direccion_letra" class="block text-gray-700 dark:text-gray-300">Letra</label>
                        <input type="text" id="direccion_letra" name="direccion[letra]" value="{{ old('direccion.letra', $cliente->direccion->letra ?? '') }}"
                               class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#BFF205] transition-all">
                        @error('direccion.letra')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="w-1/3">
                        <label for="direccion_codigoPostal" class="block text-gray-700 dark:text-gray-300">Código Postal</label>
                        <input type="number" id="direccion_codigoPostal" name="direccion[codigoPostal]" value="{{ old('direccion.codigoPostal', $cliente->direccion->codigoPostal ?? '') }}"
                               class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#BFF205] transition-all"
                               required>
                        @error('direccion.codigoPostal')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            <!-- Botones de acción -->
            <div class="mt-6 flex justify-between">
                <a href="{{ route('profile') }}" class="px-4 py-2 rounded-lg bg-gray-300 dark:bg-gray-700 dark:text-white hover:bg-gray-400 transition">
                    Volver atrás
                </a>
                <button type="button" onclick="confirmChanges()" class="px-4 py-2 rounded-lg bg-[#BFF205] hover:bg-[#A0D500] text-black transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
    <br><br>
    <x-footer />

    <!-- Toast de Confirmación -->
    <div id="toast-confirm" class="opacity-0 flex items-center w-full max-w-xs p-4 mb-4 text-gray-800 bg-[#BFF205] transition-opacity ease-in-out duration-700 shadow-sm" role="alert" style="position: fixed; top: 2rem; left: 50%; transform: translateX(-50%); border-radius: 20rem; z-index: 9999">
        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8">
            <span class="sr-only">Check icon</span>
        </div>
        <div class="ms-3 text-md font-normal ml-5">¿Estás seguro de guardar los cambios?</div>
        <button type="button" onclick="submitForm()" class="ml-4 bg-[#A0D500] text-black px-3 py-1 rounded-md">Sí</button>
        <button type="button" onclick="hideToast()" class="ml-2 bg-gray-300 text-black px-3 py-1 rounded-md">No</button>
    </div>

    <!-- Script para Mostrar el Toast de Confirmación -->
    <script>
        function confirmChanges() {
            const toast = document.getElementById('toast-confirm');
            toast.classList.remove('hidden');
            toast.classList.remove('opacity-0');
            toast.classList.add('opacity-100');
        }

        function hideToast() {
            const toast = document.getElementById('toast-confirm');
            toast.classList.remove('opacity-100');
            toast.classList.add('opacity-0');
            setTimeout(() => toast.classList.add('hidden'), 700);
        }

        function submitForm() {
            hideToast();
            document.getElementById('editProfileForm').submit();
        }

        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const imgElement = document.getElementById('preview');
                const iconElement = document.getElementById('icon');

                imgElement.src = reader.result;
                imgElement.classList.remove('hidden');
                iconElement.classList.add('hidden');
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
