<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClienteControllerView extends Controller
{
    public function mostrarCliente($guid)
    {
        $cliente = Cliente::where('guid', $guid)->firstOrFail();
        $productos = Producto::where('vendedor_id', $cliente->id)
            ->where('estado', 'Disponible')
            ->get();

        return view('pages.ver-cliente', compact('cliente', 'productos'));
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
    public function añadirFavorito(Request $request, $guid)
    {
        Log::info("Intentando agregar un producto a favoritos para el cliente con Guid: {$guid}");

        $cliente = Cliente::where('guid', $guid)->first();
        if (!$cliente) {
            Log::info("Cliente con Guid {$guid} no encontrado");
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $producto = Producto::where('guid', $request->producto_guid)->first();
        if (!$producto) {
            Log::info("Producto con Guid {$request->producto_guid} no encontrado");
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        // Agregar el producto a los favoritos usando el 'id' de ambos, cliente y producto
        $cliente->favoritos()->attach($producto->id);

        Log::info("Producto con Id {$producto->id} agregado a favoritos");

        return response()->json(['message' => 'Producto agregado a favoritos']);
    }


    // Quitar un producto de favoritos
    public function eliminarFavorito(Request $request, $guid)
    {
        Log::info("Intentando eliminar un producto de favoritos para el cliente con Guid: {$guid}");

        // Buscar el cliente por 'guid'
        $cliente = Cliente::where('guid', $guid)->first();
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        // Buscar el producto por 'id' (no 'guid')
        $producto = Producto::find($request->producto_id); // Cambiar 'producto_guid' por 'producto_id'
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        // Eliminar el producto de los favoritos
        $cliente->favoritos()->detach($producto->id); // Usamos 'producto_id' aquí

        Log::info("Producto con Id {$producto->id} eliminado de favoritos para el cliente con guid: {$guid}");

        return response()->json(['message' => 'Producto eliminado de favoritos']);
    }
}
