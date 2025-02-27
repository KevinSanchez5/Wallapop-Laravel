@extends('layouts.admin')

@section('content')

<h1 class="text-3xl font-semibold mb-6">Administración de Clientes/Usuarios</h1>

<div class="container mx-auto p-6 rounded-lg">

    <table class="w-full border-collapse">
        <thead>
        <tr class="bg-gray-200 text-gray-700">
            <th class="border px-4 py-2">ID</th>
            <th class="border px-4 py-2">Nombre Completo</th>
            <th class="border px-4 py-2">Email</th>
            <th class="border px-4 py-2">Rol</th>
            <th class="border px-4 py-2">Acciones</th>
        </tr>
        </thead>
        <tbody>
        @foreach($clientes as $cliente)
            <tr class="text-center bg-white hover:bg-gray-100">
                <td class="border px-4 py-2">{{ $cliente->id }}</td>
                <td class="border px-4 py-2">{{ $cliente->usuario->nombre }} {{ $cliente->usuario->apellido }}</td>
                <td class="border px-4 py-2">{{ $cliente->usuario->email }}</td>
                <td class="border px-4 py-2">{{ $cliente->usuario->role }}</td>
                <td class="border px-4 py-2">
                    <form action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700">
                            Eliminar
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
