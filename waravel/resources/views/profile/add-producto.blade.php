@extends('layouts.app')

@section('title', 'Añadir un nuevo producto')

@section('content')
    <x-header />
    <br><br>
    <div class="max-w-3xl mx-auto p-6 bg-white dark:bg-gray-900 shadow-md rounded-lg">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Añadir Producto</h2>

        <form action="{{ route('producto.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-5 gap-4 place-items-center">
                @for ($i = 1; $i <= 5; $i++)
                    <div class="relative border border-dashed border-gray-400 rounded-lg flex items-center justify-center w-24 h-24 cursor-pointer bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 overflow-hidden transition">
                        <label for="imagen{{ $i }}" class="absolute inset-0 flex items-center justify-center">
                            <input type="file" id="imagen{{ $i }}" name="imagen{{ $i }}" class="sr-only" accept="image/*" onchange="previewImage(event, {{ $i }})">
                            <img id="preview{{ $i }}" class="absolute inset-0 w-full h-full object-cover hidden rounded-md" />
                            <svg id="icon{{ $i }}" class="size-10 text-gray-300 transition-opacity" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z" clip-rule="evenodd" />
                            </svg>
                        </label>
                    </div>
                @endfor
            </div>

            <div class="mt-4">
                <label for="nombre" class="block text-gray-700 dark:text-gray-300">Nombre del producto</label>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}"
                       class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#BFF205] transition-all" required>
            </div>

            <div class="mt-4">
                <label for="descripcion" class="block text-gray-700 dark:text-gray-300">Descripción</label>
                <textarea id="descripcion" name="descripcion" class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#BFF205] transition-all" required>{{ old('descripcion') }}</textarea>
            </div>

            <!-- Estado Físico con Botones -->
            <div class="mt-4">
                <label class="block text-gray-700 dark:text-gray-300 mb-2">Estado físico</label>
                <div class="flex justify-center space-x-4">
                    @foreach (['Nuevo', 'Usado', 'Deteriorado'] as $estado)
                        <input type="radio" id="estado{{ $estado }}" name="estadoFisico" value="{{ $estado }}" class="hidden peer" required>
                        <label for="estado{{ $estado }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-100 dark:bg-gray-800 dark:text-white peer-checked:bg-[#BFF205] peer-checked:text-black peer-checked:border-[#BFF205] transition-all duration-300">
                            {{ $estado }}
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Nuevo campo de Stock -->
            <div class="mt-4">
                <label for="stock" class="block text-gray-700 dark:text-gray-300">Stock</label>
                <input type="number" id="stock" name="stock" value="{{ old('stock') }}"
                       class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#BFF205] transition-all"
                       min="1" required>
            </div>

            <div class="mt-4">
                <label for="precio" class="block text-gray-700 dark:text-gray-300">Precio</label>
                <input type="number" id="precio" name="precio" value="{{ old('precio') }}"
                       class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#BFF205] transition-all"
                       step="1.0" min="0.50" required>
            </div>

            <div class="mt-4">
                <label for="categoria" class="block text-gray-700 dark:text-gray-300">Categoría</label>
                <select id="categoria" name="categoria" class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#BFF205] transition-all" required>
                    <option value="Tecnologia">Tecnología</option>
                    <option value="Ropa">Ropa</option>
                    <option value="Hogar">Hogar</option>
                    <option value="Coleccionismo">Coleccionismo</option>
                    <option value="Vehiculos">Vehículos</option>
                    <option value="Videojuegos">Videojuegos</option>
                    <option value="Musica">Música</option>
                    <option value="Deporte">Deporte</option>
                    <option value="Cine">Cine</option>
                    <option value="Cocina">Cocina</option>
                </select>
            </div>

            <div class="mt-6 flex justify-between">
                <a href="{{ route('profile') }}" class="px-4 py-2 rounded-lg bg-gray-300 dark:bg-gray-700 dark:text-white hover:bg-gray-400 transition">
                    Volver atrás
                </a>
                <button type="submit" class="px-4 py-2 rounded-lg bg-[#BFF205] hover:bg-[#A0D500] text-black transition">
                    Añadir Producto
                </button>
            </div>
        </form>
    </div>
    <br><br>
    <x-footer />

    <script>
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
