<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class ProductoController extends Controller
{
    // Mostrar todos los productos
    public function index()
    {
        $productos = Producto::with('vendedor', 'clientesFavoritos')->get();
        return response()->json($productos);
    }

    // Mostrar un producto específico
    public function show($id)
    {
        $producto = Cache::remember("producto_{$id}", 60, function () use ($id) {
            return Producto::with('vendedor', 'clientesFavoritos')->find($id);
        });

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return response()->json($producto);
    }

    // Crear un nuevo producto
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guid' => 'required|unique:productos,guid',
            'vendedor_id' => 'required|exists:clientes,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'estadoFisico' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'categoria' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'imagenes' => 'required|array',
            'imagenes.*' => 'url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $producto = Producto::create($request->all());

        // Limpiar caché de la lista de productos
        Cache::forget('productos_all');

        return response()->json($producto, 201);
    }

    // Actualizar un producto existente
    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:255',
            'descripcion' => 'required|string',
            'estadoFisico' => 'required|string|max:255',
            'precio' => 'numeric|min:0',
            'categoria' => 'string|max:255',
            'estado' => 'string|max:255',
            'imagenes' => 'required|array',
            'imagenes.*' => 'url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $producto->update($request->all());

        // Limpiar caché del producto y de la lista
        Cache::forget("producto_{$id}");
        Cache::forget('productos_all');

        return response()->json($producto);
    }

    // Eliminar un producto
    public function destroy($id)
    {
        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $producto->delete();

        // Limpiar caché del producto y de la lista
        Cache::forget("producto_{$id}");
        Cache::forget('productos_all');

        return response()->json(['message' => 'Producto eliminado correctamente']);
    }

    // Agregar un producto a favoritos de un cliente
    public function addToFavorites(Request $request, $id)
    {
        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $cliente = Cliente::find($request->cliente_id);
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $producto->clientesFavoritos()->attach($cliente->id);

        // Limpiar caché del producto y de la lista
        Cache::forget("producto_{$id}");
        Cache::forget('productos_all');

        return response()->json(['message' => 'Producto agregado a favoritos']);
    }

    // Quitar un producto de favoritos de un cliente
    public function removeFromFavorites(Request $request, $id)
    {
        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $cliente = Cliente::find($request->cliente_id);
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $producto->clientesFavoritos()->detach($cliente->id);

        // Limpiar caché del producto y de la lista
        Cache::forget("producto_{$id}");
        Cache::forget('productos_all');

        return response()->json(['message' => 'Producto eliminado de favoritos']);
    }
}
