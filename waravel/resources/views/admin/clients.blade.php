@extends('layouts.admin')

@section('content')
    <h1 class="text-3xl font-semibold mb-6 text-center text-gray-800 dark:text-gray-200">Administración de Clientes/Usuarios</h1>

    <div class="container mx-auto p-6 rounded-lg bg-white dark:bg-gray-800 shadow-lg">

        <!-- Filtro de Búsqueda -->
        <form method="GET" action="{{ route('admin.clients') }}" class="mb-6 flex justify-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar clientes..." class="w-1/2 p-2 rounded-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <button type="submit" class="ml-2 p-2 bg-[bff205] text-black rounded-md bg-[#BFF205] hover:bg-[#96bf03] rounded-md hover:bg-[96bf03]">Buscar</button>
        </form>

        <!-- Tabla de Clientes -->
        <table class="w-full text-sm table-auto">
            <thead>
            <tr class="bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                <th class="py-3 px-4 text-left">ID</th>
                <th class="py-3 px-4 text-left">Nombre Completo</th>
                <th class="py-3 px-4 text-left">Email</th>
                <th class="py-3 px-4 text-left">Rol</th>
                <th class="py-3 px-4 text-left">Productos</th>
                <th class="py-3 px-4 text-left">Acciones</th>
            </tr>
            </thead>
            <tbody class="text-gray-700 dark:text-gray-300">
            @foreach($clientes as $cliente)
                <tr class="hover:bg-gray-200 dark:hover:bg-gray-600">
                    <td class="py-2 px-4">{{ $cliente->id }}</td>
                    <td class="py-2 px-4"><a href="{{route("cliente.ver", $cliente->guid)}}"><b>{{ $cliente->nombre }} {{ $cliente->apellido }}</b></a></td>
                    <td class="py-2 px-4">{{ $cliente->usuario->email }}</td>
                    <td class="py-2 px-4 capitalize">{{ $cliente->usuario->role }}</td>
                    <td class="py-2 px-4">{{ $cliente->productos_count }}</td>
                    <td class="py-2 px-4">
                        <form action="{{route('admin.delete.client', $cliente->guid)}}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 transition duration-300">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $clientes->links() }}
        </div>
    </div>
@endsection
