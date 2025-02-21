@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Añadir Producto</h2>

        <form action="{{ route('producto.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="nombre" class="block text-gray-700 dark:text-gray-300">Nombre del producto</label>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
                @error('nombre')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="descripcion" class="block text-gray-700 dark:text-gray-300">Descripción del producto</label>
                <textarea id="descripcion" name="descripcion" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>{{ old('descripcion') }}</textarea>
                @error('descripcion')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="estadoFisico" class="block text-gray-700 dark:text-gray-300">Estado físico</label>
                <select id="estadoFisico" name="estadoFisico" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
                    <option value="Nuevo" {{ old('estadoFisico') == 'Nuevo' ? 'selected' : '' }}>Nuevo</option>
                    <option value="Usado" {{ old('estadoFisico') == 'Usado' ? 'selected' : '' }}>Usado</option>
                    <option value="Deteriorado" {{ old('estadoFisico') == 'Deteriorado' ? 'selected' : '' }}>Deteriorado</option>
                </select>
                @error('estadoFisico')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="precio" class="block text-gray-700 dark:text-gray-300">Precio</label>
                <input type="number" id="precio" name="precio" value="{{ old('precio') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" step="0.01" required>
                @error('precio')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="categoria" class="block text-gray-700 dark:text-gray-300">Categoría</label>
                <select id="categoria" name="categoria" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
                    <option value="Tecnologia" {{ old('categoria') == 'Tecnologia' ? 'selected' : '' }}>Tecnología</option>
                    <option value="Ropa" {{ old('categoria') == 'Ropa' ? 'selected' : '' }}>Ropa</option>
                    <option value="Hogar" {{ old('categoria') == 'Hogar' ? 'selected' : '' }}>Hogar</option>
                    <option value="Coleccionismo" {{ old('categoria') == 'Coleccionismo' ? 'selected' : '' }}>Coleccionismo</option>
                    <option value="Vehiculos" {{ old('categoria') == 'Vehiculos' ? 'selected' : '' }}>Vehículos</option>
                    <option value="Videojuegos" {{ old('categoria') == 'Videojuegos' ? 'selected' : '' }}>Videojuegos</option>
                    <option value="Musica" {{ old('categoria') == 'Musica' ? 'selected' : '' }}>Música</option>
                    <option value="Deporte" {{ old('categoria') == 'Deporte' ? 'selected' : '' }}>Deporte</option>
                    <option value="Cine" {{ old('categoria') == 'Cine' ? 'selected' : '' }}>Cine</option>
                    <option value="Cocina" {{ old('categoria') == 'Cocina' ? 'selected' : '' }}>Cocina</option>
                </select>
                @error('categoria')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="imagenes" class="block text-gray-700 dark:text-gray-300">Imágenes (Mínimo una imagen)</label>
                <input type="file" id="imagenes" name="imagenes[]" multiple class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
                @error('imagenes')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 rounded-lg bg-[#BFF205] hover:bg-[#A0D500] text-black">
                    Añadir Producto
                </button>
            </div>
        </form>
    </div>
@endsection
