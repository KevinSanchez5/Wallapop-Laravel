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
                    <form onsubmit="event.preventDefault(); showToast('{{ route('admin.delete.review', $valoracion->guid) }}')" method="POST" class="mt-4">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 transition duration-300">
                            Eliminar
                        </button>
                    </form>

                </div>
            @endforeach
        </div>

        <!-- Toast de Confirmación para Eliminar Valoración -->
        <div id="toast-confirm-delete" class="border border-black opacity-0 hidden flex items-center w-full max-w-xs p-4 mb-4 text-gray-800 bg-red-400 transition-opacity ease-in-out duration-700 shadow-sm" role="alert" style="position: fixed; top: 2rem; left: 50%; transform: translateX(-50%); border-radius: 12px; z-index: 9999">
            <div class="ms-3 text-md font-bold ml-5">¿Estás seguro de eliminar esta valoración?</div>
            <button type="button" id="confirmDeleteBtn" class="ml-4 bg-red-600 text-white px-3 py-1 rounded-md">Sí</button>
            <button type="button" onclick="hideToast('toast-confirm-delete')" class="ml-2 bg-gray-300 text-black px-3 py-1 rounded-md">No</button>
        </div>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $valoraciones->links() }}
        </div>
    </div>

    <script>
        function showToast(deleteUrl) {
            const toast = document.getElementById('toast-confirm-delete');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

            // Mostrar el toast
            toast.classList.remove('hidden');
            toast.classList.add('opacity-100');

            // Asignar la acción de eliminación al botón
            confirmDeleteBtn.onclick = function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = deleteUrl;

                // Crear el campo _method para que Laravel reconozca que es una solicitud DELETE
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                // Crear el campo CSRF para la protección
                const csrfField = document.createElement('input');
                csrfField.type = 'hidden';
                csrfField.name = '_token';
                csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfField);

                // Enviar el formulario
                document.body.appendChild(form);
                form.submit();
            };
        }

        function hideToast(toastId) {
            const toast = document.getElementById(toastId);
            toast.classList.remove('opacity-100');
            toast.classList.add('opacity-0');
            setTimeout(() => toast.classList.add('hidden'), 700);
        }
    </script>
@endsection
