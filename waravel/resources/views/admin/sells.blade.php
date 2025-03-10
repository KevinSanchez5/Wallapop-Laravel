@extends('layouts.admin')

@section('content')
    <h1 class="text-3xl font-semibold mb-6 text-center text-gray-800 dark:text-gray-200">Administración de Ventas</h1>

    <div class="container mx-auto p-6 rounded-lg bg-white dark:bg-gray-800 shadow-lg">

        <!-- Filtro de Búsqueda -->
        <form method="GET" action="{{ route('admin.sells') }}" class="mb-6 flex justify-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar ventas por estado o precio..." class="w-1/2 p-2 rounded-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <button type="submit" class="ml-2 p-2 bg-[bff205] text-black rounded-md bg-[#BFF205] hover:bg-[#96bf03] rounded-md hover:bg-[96bf03]">Buscar</button>
        </form>

        <!-- Tabla de Ventas -->
        <table class="w-full text-sm table-auto">
            <thead>
            <tr class="bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                <th class="py-3 px-4 text-left">ID</th>
                <th class="py-3 px-4 text-left">Guid</th>
                <th class="py-3 px-4 text-left">Comprador</th>
                <th class="py-3 px-4 text-left">Precio</th>
                <th class="py-3 px-4 text-left">Fecha</th>
                <th class="py-3 px-4 text-left">Estado</th>
                <th class="py-3 px-4 text-left">Acciones</th>
            </tr>
            </thead>
            <tbody class="text-gray-700 dark:text-gray-300">
            @foreach($ventas as $venta)
                <tr class="hover:bg-gray-200 dark:hover:bg-gray-600">
                    <td class="py-2 px-4">{{ $venta->id }}</td>
                    <td class="py-2 px-4">{{ $venta->guid }}</a></td>
                    <td class="py-2 px-4">{{ $venta->comprador->guid }}, {{$venta->comprador->nombre}} {{$venta->comprador->apellido}}</td>
                    <td class="py-2 px-4">{{ $venta->precioTotal }}</td>
                    <td class="py-2 px-4">{{ $venta->created_at }}</td>
                    <td class="py-2 px-4">{{ $venta->estado }}</td>
                    <td class="py-2 px-4">
                        <form action="{{ route('admin.updateVentaEstado', $venta->guid) }}" method="POST" class="inline-block">
                            @csrf
                            @method('PUT')
                            <select name="estado" class="p-2 rounded-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="Pendiente" {{ $venta->estado === 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="Procesando" {{ $venta->estado === 'Procesando' ? 'selected' : '' }}>Procesando</option>
                                <option value="Enviado" {{ $venta->estado === 'Enviado' ? 'selected' : '' }}>Enviado</option>
                                <option value="Entregado" {{ $venta->estado === 'Entregado' ? 'selected' : '' }}>Entregado</option>
                            </select>
                            <button type="submit" class="ml-2 p-2 bg-blue-500 text-white rounded-md hover:bg-blue-700">Actualizar Estado</button>
                        </form>

                        <!-- Botón para eliminar la venta -->
                        <button type="button" onclick="showDeleteConfirmationToast('{{ $venta->guid }}')" class="ml-2 p-2 bg-red-500 text-white rounded-md hover:bg-red-700">
                            Reembolsar y Cancelar
                        </button>

                        <!-- Formulario de eliminación de venta -->
                        <form id="delete-form-{{ $venta->guid }}" action="{{ route('admin.delete.venta', $venta->guid) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $ventas->links() }}
        </div>
    </div>

    <!-- Toast de Confirmación para Eliminar Venta -->
    <div id="toast-confirm-delete" class="border border-black opacity-0 hidden flex items-center w-full max-w-xs p-4 mb-4 text-gray-800 bg-red-400 transition-opacity ease-in-out duration-700 shadow-sm" role="alert" style="position: fixed; top: 2rem; left: 50%; transform: translateX(-50%); border-radius: 12px; z-index: 9999">
        <div class="ms-3 text-md font-bold ml-5">¿Estás seguro de reembolsar y cancelar esta venta?</div>
        <button type="button" onclick="confirmDelete()" class="ml-4 bg-red-600 text-white px-3 py-1 rounded-md">Sí</button>
        <button type="button" onclick="hideToast('toast-confirm-delete')" class="ml-2 bg-gray-300 text-black px-3 py-1 rounded-md">No</button>
    </div>

    <script>
        let ventaAEliminar = null;

        // Mostrar el toast de confirmación para eliminar
        function showDeleteConfirmationToast(ventaId) {
            ventaAEliminar = ventaId;
            let toast = document.getElementById("toast-confirm-delete");
            toast.classList.remove("hidden");
            setTimeout(() => {
                toast.classList.remove("opacity-0");
            }, 100);
        }

        // Confirmar eliminación
        function confirmDelete() {
            if (ventaAEliminar) {
                document.getElementById(`delete-form-${ventaAEliminar}`).submit();
            }
        }

        // Ocultar el toast
        function hideToast(id) {
            let toast = document.getElementById(id);
            toast.classList.add("opacity-0");
            setTimeout(() => {
                toast.classList.add("hidden");
            }, 500);
        }
    </script>

@endsection
