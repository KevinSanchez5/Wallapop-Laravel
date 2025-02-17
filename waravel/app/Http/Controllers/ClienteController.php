<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class ClienteController extends Controller
{
    // Mostrar todos los clientes
    public function index()
    {
        $clientes = Cliente::with('usuario', 'productos', 'valoracionesRecibidas', 'valoracionesCreadas')->get();
        return response()->json($clientes);
    }

    // Mostrar un cliente específico
    public function show($id)
    {
        $cliente = Cache::remember("cliente_{$id}", 60, function () use ($id) {
            return Cliente::with('usuario', 'productos', 'valoracionesRecibidas', 'valoracionesCreadas')->find($id);
        });

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

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

        // Limpiar caché de la lista de clientes
        Cache::forget('clientes_all');

        return response()->json($cliente, 201);
    }

    // Actualizar un cliente existente
    public function update(Request $request, $id)
    {
        $cliente = Cliente::find($id);
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

        // Limpiar caché del cliente y de la lista
        Cache::forget("cliente_{$id}");
        Cache::forget('clientes_all');

        return response()->json($cliente);
    }

    // Eliminar un cliente
    public function destroy($id)
    {
        $cliente = Cliente::find($id);
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $cliente->delete();

        // Limpiar caché del cliente y de la lista
        Cache::forget("cliente_{$id}");
        Cache::forget('clientes_all');

        return response()->json(['message' => 'Cliente eliminado correctamente']);
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

        // Limpiar caché del cliente
        Cache::forget("cliente_{$id}");

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

        // Limpiar caché del cliente
        Cache::forget("cliente_{$id}");

        return response()->json(['message' => 'Producto eliminado de favoritos']);
    }
}
