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
                        <!-- Botón para cambiar el estado del producto -->
                        <form action="{{ route('admin.banProduct', $producto->guid) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="p-2 text-black rounded-md bg-[#BFF205] hover:bg-[#96bf03] ">
                                {{ $producto->estado === 'Baneado' ? 'Rehabilitar' : 'Bannear' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $productos->links() }}
        </div>
    </div>
@endsection
