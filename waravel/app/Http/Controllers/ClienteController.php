<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redis;

class ClienteController extends Controller
{
    // Mostrar todos los clientes
    public function index()
    {
        $query = Cliente::orderBy('id', 'asc');

        $clientes = $query->paginate(5);

        $data = $clientes->getCollection()->transform(function ($cliente) {
            return [
                'id' => $cliente->id,
                'guid' => $cliente->guid,
                'nombre' => $cliente->nombre,
                'apellido' => $cliente->apellido,
                'avatar' => $cliente->avatar,
                'telefono' => $cliente->telefono,
                'direccion' => $cliente->direccion,
                'activo' => $cliente->activo,
                'usuario_id' => $cliente->usuario_id,
                'created_at' => $cliente->created_at->toDateTimeString(),
                'updated_at' => $cliente->updated_at->toDateTimeString(),
            ];
        });

        $customResponse = [
            'clientes' => $data,
            'paginacion' => [
                'pagina_actual' => $clientes->currentPage(),
                'elementos_por_pagina' => $clientes->perPage(),
                'ultima_pagina' => $clientes->lastPage(),
                'elementos_totales' => $clientes->total(),
            ],
        ];

        return response()->json($customResponse);
    }

    // Mostrar un cliente específico
    public function show($id)
    {
        $clienteRedis = Redis::get('cliente_' . $id);

        if ($clienteRedis) {
            return response()->json(json_decode($clienteRedis));
        }

        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        Redis::set('cliente_' . $id, json_encode($cliente), 'EX', 1800);

        return response()->json($cliente);
    }

    // Crear un nuevo cliente
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guid' => 'required|unique:clientes,guid',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'avatar' => 'default|url',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|array',
            'activo' => 'required|boolean',
            'usuario_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cliente = Cliente::create($request->all());

        return response()->json($cliente, 201);
    }

    // Actualizar un cliente existente
    public function update(Request $request, $id)
    {
        $cliente = Redis::get('cliente_' . $id);

        if (!$cliente) {
            $cliente = Cliente::find($id);
        }

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:255',
            'apellido' => 'string|max:255',
            'avatar' => 'nullable|url',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|array',
            'activo' => 'boolean',
            'usuario_id' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cliente->update($request->all());

        // Limpiar caché del cliente
        Redis::del('cliente_' . $id);
        Redis::set('cliente_' . $id, json_encode($cliente), 'EX', 1800);

        return response()->json($cliente);
    }

    // Eliminar un cliente
    public function destroy($id)
    {
        $cliente = Redis::get('cliente_' . $id);

        if (!$cliente) {
            $cliente = Cliente::find($id);
        }

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $cliente->delete();

        // Limpiar caché del cliente
        Redis::del('cliente_' . $id);

        return response()->json(['message' => 'Cliente eliminado correctamente']);
    }

    // Buscar favoritos
    public function searchFavorites($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $favoritos = $cliente->favoritos;

        return response()->json($favoritos);
    }

    // Agregar un producto a favoritos
    public function addToFavorites(Request $request, $id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $producto = Producto::find($request->producto_id);
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $cliente->favoritos()->attach($producto->id);

        return response()->json(['message' => 'Producto agregado a favoritos']);
    }

    // Quitar un producto de favoritos
    public function removeFromFavorites(Request $request, $id)
    {
        $cliente = Cliente::find($id);
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $producto = Producto::find($request->producto_id);
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $cliente->favoritos()->detach($producto->id);

        return response()->json(['message' => 'Producto eliminado de favoritos']);
    }
}
