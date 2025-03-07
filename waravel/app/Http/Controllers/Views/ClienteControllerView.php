<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Valoracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClienteControllerView extends Controller
{
    public function mostrarCliente($guid)
    {
        $cliente = Cliente::where('guid', $guid)->firstOrFail();

        $productos = Producto::where('vendedor_id', $cliente->id)
            ->where('estado', 'Disponible')
            ->paginate(5);

        if (auth()->check()) {
            $clienteAuth = auth()->user()->cliente;
            $productosFavoritos = $clienteAuth ? $clienteAuth->favoritos->pluck('id')->toArray() : [];
        } else {
            $productosFavoritos = [];
        }

        $valoraciones = Valoracion::with('creador')
            ->where('clienteValorado_id', $cliente->id)
            ->latest()
            ->paginate(5);

        $promedio = Valoracion::where('clienteValorado_id', $cliente->id)->avg('puntuacion') ?? 0;
        $estrellasLlenas = round($promedio);
        $estrellasVacias = 5 - $estrellasLlenas;

        return view('pages.ver-cliente', compact(
            'cliente',
            'productos',
            'valoraciones',
            'promedio',
            'estrellasLlenas',
            'estrellasVacias',
            'productosFavoritos'
        ));
    }

    // Buscar favoritos
    public function mostrarFavoritos($guid)
    {
        Log::info("Buscando favoritos para el cliente con Guid: {$guid}");

        $cliente = Cliente::where('guid',$guid)->first();

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $favoritos = $cliente->favoritos;
        Log::info("Se encontraron " . count($favoritos) . " favoritos para el cliente con ID: {$guid}");

        return view('pages.ver-favoritos', compact('favoritos'));
    }

    // Agregar un producto a favoritos
    public function añadirFavorito(Request $request)
    {
        Log::info("Intentando agregar un producto a favoritos para el cliente logeado");

        $validator = Validator::make($request->all(), [
            'productoGuid' => 'required',
            'userId' => 'required'
        ]);

        if ($validator->fails()) {
            Log::warning('No se han proporcionado los campos necesarios');
            return response()->json($validator->errors(), 400);
        }

        $productGuid = $request->input('productoGuid');
        $userId = $request->input('userId');

        $cliente = Cliente::where('usuario_id', $userId)->first();
        if (!$cliente) {
            Log::info("Cliente con Guid {$cliente->guid} no encontrado");
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $producto = Producto::where('guid', $productGuid)->first();
        if (!$producto) {
            Log::info("Producto con Guid {$productGuid} no encontrado");
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        // Verificar si el producto ya está en favoritos
        if ($cliente->favoritos()->where('producto_id', $producto->id)->exists()) {
            Log::info("Producto con Id {$producto->id} ya añadido en favoritos");
            return response()->json(['status' => 200, 'message' => 'Producto ya añadido en favoritos']);
        }

        // Agregar el producto a los favoritos usando el 'id' de ambos, cliente y producto
        $cliente->favoritos()->attach($producto->id);

        Log::info("Producto con Id {$producto->id} agregado a favoritos");

        return response()->json(['status' => 200, 'message' => 'Producto agregado a favoritos']);
    }


    // Quitar un producto de favoritos
    public function eliminarFavorito(Request $request)
    {
        Log::info("Intentando eliminar un producto de favoritos para el cliente logeado");

        $validator = Validator::make($request->all(), [
            'productoGuid' => 'required',
            'userId' => 'required'
        ]);

        if ($validator->fails()) {
            Log::warning('No se han proporcionado los campos necesarios');
            return response()->json($validator->errors(), 400);
        }

        $productGuid = $request->input('productoGuid');
        $userId = $request->input('userId');

        $cliente = Cliente::where('usuario_id', $userId)->first();
        if (!$cliente) {
            Log::info("Cliente con Guid {$cliente->guid} no encontrado");
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $producto = Producto::where('guid', $productGuid)->first();
        if (!$producto) {
            Log::info("Producto con Guid {$productGuid} no encontrado");
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        // Eliminar el producto de los favoritos
        $cliente->favoritos()->detach($producto->id);

        Log::info("Producto con Id {$producto->id} eliminado de favoritos");

        return response()->json(['status' => 200, 'message' => 'Producto eliminado de favoritos']);
    }
}
