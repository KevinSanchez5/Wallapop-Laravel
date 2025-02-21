@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Añadir Producto</h2>

        <form action="{{ route('producto.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-5 gap-4">
                @for ($i = 1; $i <= 5; $i++)
                    <div class="border border-dashed border-gray-400 rounded-lg flex flex-col items-center justify-center p-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 relative">
                        <label for="imagen{{ $i }}" class="flex flex-col items-center cursor-pointer">
                            <img id="preview{{ $i }}" class="hidden w-16 h-16 object-cover rounded-md" />
                            <svg class="mx-auto size-10 text-gray-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-xs text-gray-600 dark:text-gray-400 mt-2">Subir imagen {{ $i }}</span>
                            <input type="file" id="imagen{{ $i }}" name="imagen{{ $i }}" class="sr-only" accept="image/*" onchange="previewImage(event, {{ $i }})">
                        </label>
                    </div>
                @endfor
            </div>

            <div>
                <label for="nombre" class="block text-gray-700 dark:text-gray-300">Nombre del producto</label>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
            </div>

            <div>
                <label for="descripcion" class="block text-gray-700 dark:text-gray-300">Descripción</label>
                <textarea id="descripcion" name="descripcion" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>{{ old('descripcion') }}</textarea>
            </div>

            <div>
                <label for="estadoFisico" class="block text-gray-700 dark:text-gray-300">Estado físico</label>
                <select id="estadoFisico" name="estadoFisico" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
                    <option value="Nuevo">Nuevo</option>
                    <option value="Usado">Usado</option>
                    <option value="Deteriorado">Deteriorado</option>
                </select>
            </div>

            <div>
                <label for="precio" class="block text-gray-700 dark:text-gray-300">Precio</label>
                <input type="number" id="precio" name="precio" value="{{ old('precio') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" step="0.01" required>
            </div>

            <div>
                <label for="categoria" class="block text-gray-700 dark:text-gray-300">Categoría</label>
                <select id="categoria" name="categoria" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
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

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 rounded-lg bg-[#BFF205] hover:bg-[#A0D500] text-black">
                    Añadir Producto
                </button>
            </div>
        </form>
    </div>

    <script>
        function previewImage(event, index) {
            const reader = new FileReader();
            reader.onload = function() {
                const imgElement = document.getElementById(`preview${index}`);
                imgElement.src = reader.result;
                imgElement.classList.remove('hidden');
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
