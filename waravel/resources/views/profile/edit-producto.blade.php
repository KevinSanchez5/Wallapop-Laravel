@extends('layouts.app')

@section('title', 'Editar Producto')

@section('content')
    <x-header />
    <br><br>
    <div class="max-w-3xl mx-auto p-6 bg-white dark:bg-gray-900 shadow-md rounded-lg">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Editar Producto</h2>

        <form id="editProductForm" action="{{ route('producto.update', $producto->guid) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Imágenes del Producto -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 mb-2">Imágenes del producto</label>
                <div class="grid grid-cols-5 gap-4 place-items-center">
                    @for ($i = 1; $i <= 5; $i++)
                        <div class="relative border border-dashed border-gray-400 rounded-lg flex items-center justify-center w-24 h-24 cursor-pointer bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 overflow-hidden transition">
                            <label for="imagen{{ $i }}" class="absolute inset-0 flex items-center justify-center">
                                <input type="file" id="imagen{{ $i }}" name="imagen{{ $i }}" class="sr-only" accept="image/*" onchange="previewImage(event, {{ $i }})">

                                @if (isset($producto->imagenes[$i - 1]))
                                    <img id="preview{{ $i }}" src="{{ asset('storage/' . $producto->imagenes[$i - 1]) }}"
                                         class="absolute inset-0 w-full h-full object-cover rounded-md">
                                @else
                                    <img id="preview{{ $i }}" src="" class="absolute inset-0 w-full h-full object-cover hidden rounded-md">
                                @endif

                                <svg id="icon{{ $i }}" class="size-10 text-gray-300 transition-opacity {{ isset($producto->imagenes[$i - 1]) ? 'hidden' : '' }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z" clip-rule="evenodd" />
                                </svg>
                            </label>
                        </div>
                    @endfor
                </div>
                @error('imagen1')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nombre del Producto -->
            <div class="mb-4">
                <label for="nombre" class="block text-gray-700 dark:text-gray-300">Nombre del producto</label>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $producto->nombre) }}"
                       class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#BFF205] transition-all"
                       required>
                @error('nombre')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Descripción -->
            <div class="mb-4">
                <label for="descripcion" class="block text-gray-700 dark:text-gray-300">Descripción</label>
                <textarea id="descripcion" name="descripcion"
                          class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#BFF205] transition-all"
                          required>{{ old('descripcion', $producto->descripcion) }}</textarea>
                @error('descripcion')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Estado Físico -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 mb-2">Estado físico</label>
                <div class="flex justify-center space-x-4">
                    @foreach (['Nuevo', 'Usado', 'Deteriorado'] as $estado)
                        <input type="radio" id="estado{{ $estado }}" name="estadoFisico" value="{{ $estado }}"
                               class="hidden peer" {{ old('estadoFisico', $producto->estadoFisico) === $estado ? 'checked' : '' }} required>
                        <label for="estado{{ $estado }}"
                               class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-100 dark:bg-gray-800 dark:text-white peer-checked:bg-[#BFF205] peer-checked:text-black peer-checked:border-[#BFF205] transition-all duration-300">
                            {{ $estado }}
                        </label>
                    @endforeach
                </div>
                @error('estadoFisico')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Stock -->
            <div class="mb-4">
                <label for="stock" class="block text-gray-700 dark:text-gray-300">Stock</label>
                <input type="number" id="stock" name="stock" value="{{ old('stock', $producto->stock) }}"
                       class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#BFF205] transition-all"
                       min="1" required>
                @error('stock')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Precio -->
            <div class="mb-4">
                <label for="precio" class="block text-gray-700 dark:text-gray-300">Precio</label>
                <input type="number" id="precio" name="precio" value="{{ old('precio', $producto->precio) }}"
                       class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#BFF205] transition-all"
                       step="0.01" min="0.50" required>
                @error('precio')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Categoría -->
            <div class="mb-4">
                <label for="categoria" class="block text-gray-700 dark:text-gray-300">Categoría</label>
                <select id="categoria" name="categoria"
                        class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#BFF205] transition-all" required>
                    @foreach (['Tecnologia', 'Ropa', 'Hogar', 'Coleccionismo', 'Vehiculos', 'Videojuegos', 'Musica', 'Deporte', 'Cine', 'Cocina'] as $categoria)
                        <option value="{{ $categoria }}" {{ old('categoria', $producto->categoria) === $categoria ? 'selected' : '' }}>
                            {{ $categoria }}
                        </option>
                    @endforeach
                </select>
                @error('categoria')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones de acción -->
            <div class="mt-6 flex justify-between">
                <a href="{{ route('profile') }}" class="px-4 py-2 rounded-lg bg-gray-300 dark:bg-gray-700 dark:text-white hover:bg-gray-400 transition">
                    Volver atrás
                </a>
                <button type="button" onclick="confirmChanges()" class="px-4 py-2 rounded-lg bg-[#BFF205] hover:bg-[#A0D500] text-black transition">
                    Actualizar Producto
                </button>
            </div>
        </form>
    </div>
    <br><br>
    <x-footer />

    <!-- Toast de Confirmación -->
    <div id="toast-confirm" class="opacity-0 hidden flex items-center w-full max-w-xs p-4 mb-4 text-gray-800 bg-[#BFF205] transition-opacity ease-in-out duration-700 shadow-sm" role="alert" style="position: fixed; top: 2rem; left: 50%; transform: translateX(-50%); border-radius: 20rem; z-index: 9999">
        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8">
            <span class="sr-only">Check icon</span>
        </div>
        <div class="ms-3 text-md font-normal ml-5">¿Estás seguro de actualizar el producto?</div>
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
            setTimeout(() => toast.classList.add('hidden'), 700); // Espera a que termine la animación
        }

        function submitForm() {
            hideToast();
            document.getElementById('editProductForm').submit();
        }

        function previewImage(event, index) {
            const reader = new FileReader();
            reader.onload = function() {
                const imgElement = document.getElementById(`preview${index}`);
                const iconElement = document.getElementById(`icon${index}`);

                imgElement.src = reader.result;
                imgElement.classList.remove('hidden');
                iconElement.classList.add('hidden');
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
