<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Producto;

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
}
