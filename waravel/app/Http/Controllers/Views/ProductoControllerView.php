<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Support\Facades\Cache;

class ProductoControllerView extends Controller
{
    public function indexVista()
    {
        // Obtenemos los productos paginados
        $productos = Producto::with('vendedor', 'clientesFavoritos')->paginate(15);

        // Devolvemos la vista de inicio con los productos
        return view('pages.home', compact('productos'));
    }


    public function showVista($guid)
    {
        $producto = Cache::remember("producto_{$guid}", 60, function () use ($guid) {
            return Producto::with('vendedor', 'clientesFavoritos')->where('guid', $guid)->first();
        });

        if (!$producto) {
            return redirect()->route('inicio')->with('error', 'Producto no encontrado');
        }

        return view('pages.producto', compact('producto'));
    }

}
