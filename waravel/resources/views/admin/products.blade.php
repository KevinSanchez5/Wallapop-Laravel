@extends('layouts.admin')

@section('content')
    <h1 class="text-3xl font-semibold mb-6 text-center text-gray-800 dark:text-gray-200">Administración de Productos</h1>

    <div class="container mx-auto p-6 rounded-lg bg-white dark:bg-gray-800 shadow-lg">

        <!-- Filtro de Búsqueda -->
        <form method="GET" action="{{ route('admin.products') }}" class="mb-6 flex justify-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar productos..." class="w-1/2 p-2 rounded-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <button type="submit" class="ml-2 p-2 bg-[bff205] text-black rounded-md bg-[#BFF205] hover:bg-[#96bf03] rounded-md hover:bg-[96bf03]">Buscar</button>
        </form>

        <!-- Tabla de Productos -->
        <table class="w-full text-sm table-auto">
            <thead>
            <tr class="bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                <th class="py-3 px-4 text-left">ID</th>
                <th class="py-3 px-4 text-left">Nombre</th>
                <th class="py-3 px-4 text-left">Categoría</th>
                <th class="py-3 px-4 text-left">Precio</th>
                <th class="py-3 px-4 text-left">Estado</th>
                <th class="py-3 px-4 text-left">Acciones</th>
            </tr>
            </thead>
            <tbody class="text-gray-700 dark:text-gray-300">
            @foreach($productos as $producto)
                <tr class="hover:bg-gray-200 dark:hover:bg-gray-600">
                    <td class="py-2 px-4">{{ $producto->id }}</td>
                    <td class="py-2 px-4"><a href="{{ route('producto.show', $producto->guid) }}">{{ $producto->nombre }}</a></td>
                    <td class="py-2 px-4">{{ $producto->categoria }}</td>
                    <td class="py-2 px-4">{{ $producto->precio }}</td>
                    <td class="py-2 px-4">{{ $producto->estado }}</td>
                    <td class="py-2 px-4">
                        <button type="button" onclick="showStateChangeToast('{{ $producto->guid }}')" class="p-2 text-white rounded-md transition duration-300 {{ $producto->estado === 'Baneado' ? 'bg-blue-500 hover:bg-blue-700' : 'bg-red-500 hover:bg-red-700' }}">
                            {{ $producto->estado === 'Baneado' ? 'Rehabilitar' : 'Banear' }}
                        </button>

                        <form id="state-change-form-{{ $producto->guid }}" action="{{ route('admin.ban.product', $producto->guid) }}" method="POST" class="hidden">
                            @csrf
                            @method('PATCH')
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Toast de Confirmación para Cambiar Estado del Producto -->
        <div id="toast-confirm-state" class="border border-black opacity-0 hidden flex items-center w-full max-w-xs p-4 mb-4 text-gray-800 bg-yellow-400 transition-opacity ease-in-out duration-700 shadow-sm" role="alert" style="position: fixed; top: 2rem; left: 50%; transform: translateX(-50%); border-radius: 12px; z-index: 9999">
            <div class="ms-3 text-md font-bold ml-5">¿Estás seguro de cambiar el estado de este producto?</div>
            <button type="button" onclick="confirmStateChange()" class="ml-4 bg-yellow-600 text-white px-3 py-1 rounded-md">Sí</button>
            <button type="button" onclick="hideToast('toast-confirm-state')" class="ml-2 bg-gray-300 text-black px-3 py-1 rounded-md">No</button>
        </div>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $productos->links() }}
        </div>
    </div>
    <script>
        let productoAActualizar = null;

        function showStateChangeToast(productoId) {
            productoAActualizar = productoId;
            let toast = document.getElementById("toast-confirm-state");
            toast.classList.remove("hidden");
            setTimeout(() => {
                toast.classList.remove("opacity-0");
            }, 100);
        }

        function confirmStateChange() {
            if (productoAActualizar) {
                document.getElementById(`state-change-form-${productoAActualizar}`).submit();
            }
        }

        function hideToast(id) {
            let toast = document.getElementById(id);
            toast.classList.add("opacity-0");
            setTimeout(() => {
                toast.classList.add("hidden");
            }, 500);
        }
    </script>

@endsection
