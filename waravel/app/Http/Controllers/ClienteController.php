<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /* Crear un cliente con dirección
    $cliente = new Cliente([
    'nombre' => 'Juan Pérez',
    'email' => 'juan@example.com',
    'direccion' => new Direccion('Av. Siempre Viva', 742, 'Springfield', 'EE.UU.'),
    ]);

    $cliente->save();

    // Consultar un cliente y acceder a la dirección como un objeto
    $cliente = Cliente::first();
    echo $cliente->direccion->ciudad; // Springfield
    */
}
