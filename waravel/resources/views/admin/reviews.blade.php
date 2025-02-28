@extends('layouts.admin')

@section('content')
    <h1 class="text-3xl font-semibold mb-6 text-center text-gray-800 dark:text-gray-200">
        Administración de Valoraciones
    </h1>

    <div class="container mx-auto p-6 rounded-lg bg-white dark:bg-gray-800 shadow-lg">

        <!-- Filtro de Búsqueda -->
        <form method="GET" action="{{ route('admin.reviews') }}" class="mb-6 flex justify-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar valoraciones..."
                   class="w-1/2 p-2 rounded-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <button type="submit"
                    class="ml-2 p-2 bg-[#BFF205] text-black rounded-md hover:bg-[#96bf03]">
                Buscar
            </button>
        </form>

        <!-- Grid de Valoraciones -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($valoraciones as $valoracion)
                <div class="bg-gray-100 dark:bg-gray-700 p-5 rounded-lg shadow-md flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            {{ $valoracion->clienteValorado->nombre }} {{ $valoracion->clienteValorado->apellido }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Reseñado por:
                            <a href="{{ route('cliente.ver', optional($valoracion->creador)->guid) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                {{ optional($valoracion->creador)->nombre }} {{ optional($valoracion->creador)->apellido }}
                            </a>
                        </p>

                        <!-- Estrellas -->
                        <div class="mt-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $valoracion->puntuacion ? 'text-yellow-400' : 'text-gray-400' }}"></i>
                            @endfor
                        </div>

                        <p class="mt-3 text-gray-700 dark:text-gray-300 italic">
                            "{{ $valoracion->comentario }}"
                        </p>
                    </div>

                    <!-- Botón de eliminar -->
                    <form action="{{ route('admin.reviews.destroy', $valoracion->id) }}" method="POST" class="mt-4">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 transition duration-300">
                            Eliminar
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $valoraciones->links() }}
        </div>
    </div>
@endsection
