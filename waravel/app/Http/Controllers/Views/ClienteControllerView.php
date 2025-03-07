<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Valoracion;

class ClienteControllerView extends Controller
{
    public function mostrarCliente($guid)
    {
        $cliente = Cliente::where('guid', $guid)->firstOrFail();

        $productos = Producto::where('vendedor_id', $cliente->id)
            ->where('estado', 'Disponible')
            ->paginate(5);

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
            'estrellasVacias'
        ));
    }
}
