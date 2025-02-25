@php
    use App\Models\Valoracion;
@endphp

@extends('layouts.app')

@section('title', "Perfil de {$cliente->nombre}")

@section('content')
    <x-header />

    <div class="container mx-auto py-6 flex gap-6">
        <!-- Información del Cliente -->
        <div class="flex-shrink-0 w-auto bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">
            <!-- Avatar con efecto hover -->
            <div class="relative w-32 h-32 mx-auto">
                <img src="{{ asset('storage/' . ($cliente->avatar ?? 'clientes/default.jpg')) }}"
                     alt="Avatar de {{ $cliente->nombre }}"
                     class="w-32 h-32 rounded-full mx-auto shadow-md transition-transform duration-300 hover:scale-105">
            </div>

            <!-- Nombre del Cliente -->
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white text-center mt-4">
                {{ $cliente->nombre }} {{ $cliente->apellido }}
            </h2>

            <!-- Teléfono -->
            <p class="text-gray-600 dark:text-gray-400 text-center mt-1">
                📞 {{ $cliente->telefono }}
            </p>

            <!-- Cálculo de estrellas -->
            @php
                $promedio = Valoracion::where('clienteValorado_id', $cliente->id)->avg('puntuacion') ?? 0;
                $estrellasLlenas = round($promedio);
                $estrellasVacias = 5 - $estrellasLlenas;
            @endphp

                <!-- Estrellas -->
            <div class="text-center mt-4 flex justify-center space-x-1">
                @for ($i = 0; $i < $estrellasLlenas; $i++)
                    <svg class="w-7 h-7 text-yellow-500 animate-fadeIn" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 17.3l-5.4 3.4 1.4-6-4.6-4 6.1-.5L12 4l2.5 6.1 6.1.5-4.6 4 1.4 6z"/>
                    </svg>
                @endfor
                @for ($i = 0; $i < $estrellasVacias; $i++)
                    <svg class="w-7 h-7 text-gray-400 animate-fadeIn opacity-80" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 17.3l-5.4 3.4 1.4-6-4.6-4 6.1-.5L12 4l2.5 6.1 6.1.5-4.6 4 1.4 6z"/>
                    </svg>
                @endfor
            </div>

            <!-- Puntuación numérica -->
            <p class="text-gray-600 dark:text-gray-400 text-sm text-center mt-2">
                {{ number_format($promedio, 1) }} / 5
            </p>

            <!-- Dirección y mapa -->
            @if(isset($cliente->direccion->codigoPostal))
                <p class="text-gray-600 dark:text-gray-400 text-center mt-2">
                    📍 Código Postal: {{ $cliente->direccion->codigoPostal }}
                </p>
                <div class="mt-4">
                    <iframe
                        width="100%"
                        height="250"
                        class="rounded-lg shadow-lg"
                        style="border:0;"
                        loading="lazy"
                        allowfullscreen
                        referrerpolicy="no-referrer-when-downgrade"
                        src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBlxJ4a_HfUSAVljwVgN7NkwtBk4IGTX_A&q={{ urlencode($cliente->direccion->codigoPostal) }},ES">
                    </iframe>
                </div>
            @endif

            <!-- Botón de Editar Perfil -->
            <div class="text-center mt-6">
                <button onclick="toggleModal()" class="px-6 py-3 rounded-lg text-gray-800 bg-[#BFF205] hover:bg-[#A0D500] focus:outline-none focus:ring-2 focus:ring-[#A0D500] transition duration-300">
                    <b>Editar perfil</b>
                </button>
            </div>
        </div>

        <div class="flex-grow bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <!-- Botones -->
            <div class="flex justify-center mb-4">
                <button onclick="mostrarSeccion('productos')"
                        class="px-4 py-2 rounded-lg text-black bg-[#BFF205] hover:bg-[#A0D500]">
                    <b>Productos</b>
                </button>
                <button onclick="mostrarSeccion('valoraciones')"
                        class="ml-2 px-4 py-2 rounded-lg text-black bg-[#BFF205] hover:bg-[#A0D500]">
                    <b>Valoraciones</b>
                </button>
            </div>

            <!-- Productos -->
            <div id="productos" class="seccion">
                <div class="animate-fadeIn">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Productos en venta</h2>
                        <a href="{{ route('producto.add') }}" class="p-2 bg-[#BFF205] text-black rounded-md hover:bg-[#A0D500] transition duration-300 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Añadir Producto
                        </a>
                    </div>
                </div>
                <ul>
                    @isset($productos)
                        @forelse ($productos as $producto)
                            <li class="p-4 border-b border-gray-300 dark:border-gray-700 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-4 w-full">
                                    <img src="{{ asset('storage/' . ($producto->imagenes[0] ?? 'default.jpg')) }}"
                                         alt="{{ $producto->nombre }}"
                                         class="w-16 h-16 rounded-md object-cover shadow-md">
                                    <div class="flex flex-col">
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $producto->nombre }}</h3>
                                        <p class="text-gray-600 dark:text-gray-400">Precio: €{{ number_format($producto->precio, 2) }}</p>
                                        <a href="{{ route('producto.show', $producto->guid) }}"
                                           class="text-blue-500 dark:text-blue-400 font-semibold hover:underline">Ver más</a>
                                    </div>
                                </div>

                                <span class="inline-flex items-center justify-center px-4 py-2 rounded-full text-white
                                    @if($producto->estado == 'Disponible') bg-green-500
                                    @elseif($producto->estado == 'Vendido') bg-red-500
                                    @elseif($producto->estado == 'Desactivado') bg-gray-500
                                    @elseif($producto->estado == 'Baneado') bg-purple-500
                                    @endif w-32 text-center mr-25">
                                    {{ $producto->estado }}
                                </span>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('producto.edit', $producto->guid) }}" class="p-2 bg-[#BFF205] text-black rounded-md hover:bg-[#A0D500] transition duration-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5">
                                            <path d="M12 20h9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4L16.5 3.5z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>

                                    <form action="{{ route('producto.changestatus', $producto->guid) }}" method="POST">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="p-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 transition duration-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5">
                                                <path d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                    </form>

                                    <form action="{{ route('producto.destroy', $producto->guid) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition duration-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5">
                                                <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @empty
                            <li class="text-gray-500 dark:text-gray-400">No hay productos en venta.</li>
                        @endforelse
                    @endisset
                </ul>
            </div>

            <!-- Valoraciones -->
            <div id="valoraciones" class="seccion hidden">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Valoraciones</h2>
                <ul>
                    @foreach ($valoraciones as $valoracion)
                        <li class="p-4 border-b border-gray-300 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('cliente.ver', $valoracion->creador->guid) }}"
                                   class="text-blue-500 dark:text-blue-400 font-semibold hover:underline">
                                    {{ optional($valoracion->creador)->nombre ?? 'Usuario eliminado' }}
                                    {{ optional($valoracion->creador)->apellido ?? '' }}
                                </a>
                                <span class="text-yellow-500">{{ str_repeat('⭐', $valoracion->puntuacion) }}</span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400">{{ $valoracion->comentario }}</p>
                            <p class="text-gray-500 text-sm mt-1">
                                📅 {{ $valoracion->created_at->format('d/m/Y') }}
                            </p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Modal de Edición de Perfil -->
    <div id="editProfileModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-600 opacity-75"></div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg z-10 w-full max-w-md mx-auto p-6 relative">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">Editar Perfil</h2>
                    <button onclick="toggleModal()" class="text-gray-600 dark:text-gray-300 text-2xl leading-none">&times;</button>
                </div>
                <!-- Formulario -->
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Avatar -->
                    <div class="flex flex-col items-center">
                        @if($cliente->avatar)
                            <img src="{{ asset('storage/' . $cliente->avatar) }}" alt="Avatar" class="w-20 h-20 rounded-full object-cover mb-3">
                        @else
                            <div class="w-20 h-20 bg-gray-300 dark:bg-gray-700 flex items-center justify-center rounded-full mb-3">
                                <span class="text-gray-600 dark:text-gray-300">Sin imagen</span>
                            </div>
                        @endif
                        <label class="block w-full text-center">
                            <span class="sr-only">Subir nueva imagen</span>
                            <input type="file" name="avatar" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:border-0 file:rounded-md file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-gray-700 dark:file:text-white">
                        </label>
                        @error('avatar')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Datos personales (2 columnas) -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $cliente->nombre) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white" required>
                            @error('nombre')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="apellidos" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellidos</label>
                            <input type="text" name="apellidos" id="apellidos" value="{{ old('apellidos', $cliente->apellido) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white" required>
                            @error('apellidos')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Email (bloqueado) -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo Electrónico</label>
                        <input type="email" name="email" id="email" value="{{ old('email', Auth::user()->email) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white cursor-not-allowed" disabled>
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" value="{{ old('telefono', $cliente->telefono) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white" required>
                        @error('telefono')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Datos de dirección (organizados en 2 columnas con el código postal en una fila completa) -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="direccion_calle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Calle</label>
                            <input type="text" name="direccion[calle]" id="direccion_calle" value="{{ old('direccion.calle', $cliente->direccion->calle ?? '') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white" required>
                            @error('direccion.calle')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="direccion_numero" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número</label>
                            <input type="number" name="direccion[numero]" id="direccion_numero" value="{{ old('direccion.numero', $cliente->direccion->numero ?? '') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white" required>
                            @error('direccion.numero')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="direccion_piso" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Piso</label>
                            <input type="number" name="direccion[piso]" id="direccion_piso" value="{{ old('direccion.piso', $cliente->direccion->piso ?? '') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white">
                            @error('direccion.piso')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="direccion_letra" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Letra</label>
                            <input type="text" name="direccion[letra]" id="direccion_letra" value="{{ old('direccion.letra', $cliente->direccion->letra ?? '') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white">
                            @error('direccion.letra')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-2">
                            <label for="direccion_codigoPostal" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código Postal</label>
                            <input type="number" name="direccion[codigoPostal]" id="direccion_codigoPostal" value="{{ old('direccion.codigoPostal', $cliente->direccion->codigoPostal ?? '') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white" required>
                            @error('direccion.codigoPostal')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Botón de Guardar -->
                    <div class="pt-4">
                        <button type="submit" class="w-full rounded-lg bg-[#BFF205] hover:bg-[#A0D500] text-black transition font-medium py-2 px-4 rounded-md shadow">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-footer />

    <script>
        function toggleModal() {
            const modal = document.getElementById('editProfileModal');
            modal.classList.toggle('hidden');
        }

        function mostrarSeccion(seccion) {
            document.getElementById('productos').classList.add('hidden');
            document.getElementById('valoraciones').classList.add('hidden');
            document.getElementById(seccion).classList.remove('hidden');
        }
    </script>

    <style>
        .hidden { display: none; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
    </style>
@endsection
