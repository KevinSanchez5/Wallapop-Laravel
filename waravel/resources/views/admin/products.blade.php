@extends('admin.dashboard')

@section('content')
    <div class="main-content">
        <h1>Gestionar Productos</h1>
        <table class="table table-bordered mt-4">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($productos as $producto)
                <tr>
                    <td>{{ $producto->id }}</td>
                    <td>{{ $producto->nombre }}</td>
                    <td>{{ $producto->estado }}</td>
                    <td>
                        <a href="#" class="btn btn-warning btn-sm">Banear</a>
                        <a href="#" class="btn btn-danger btn-sm">Eliminar</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
