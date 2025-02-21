<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Models\Valoracion;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ValoracionesControllerView extends Controller
{
    /**
     * Muestra la lista de valoraciones de un cliente específico.
     */
    public function index($guid)
    {
        $cliente = Cliente::where('guid', $guid)->firstOrFail();
        $valoraciones = Valoracion::where('clienteValorado_id', $cliente->id)->latest()->get();

        return view('clientes.valoraciones', compact('cliente', 'valoraciones'));
    }

    /**
     * Calcula la puntuación media de un cliente y la devuelve.
     */
    public function promedio($guid)
    {
        $cliente = Cliente::where('guid', $guid)->firstOrFail();
        $promedio = Valoracion::where('clienteValorado_id', $cliente->id)->avg('puntuacion');

        return response()->json([
            'promedio' => round($promedio, 1),
            'estrellas' => str_repeat('⭐', round($promedio))
        ]);
    }
}
