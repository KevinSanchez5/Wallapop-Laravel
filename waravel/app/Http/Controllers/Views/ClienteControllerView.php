<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Producto;

// Asegúrate de importar el modelo

class ClienteControllerView extends Controller
{
    /**
     * Muestra la vista de "Mi Cuenta" con los datos del cliente y sus productos.
     */
    public function mostrarCliente($guid)
    {
        $cliente = Cliente::where('guid', $guid)->firstOrFail();
        $productos = Producto::where('vendedor_id', $cliente->id)
            ->where('estado', 'Disponible')
            ->get();

        return view('client.verCliente', compact('cliente', 'productos'));
    }



}
