<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductoControllerView extends Controller
{
    public function indexVista()
    {
        // Obtener productos con relaciones y paginación
        $productos = Producto::with('vendedor', 'clientesFavoritos')->orderBy('id', 'asc')->paginate(15);

        // Transformar los datos sin perder la paginación
        $productos->setCollection($productos->getCollection()->map(function ($producto) {
            return (object) [ // Convertir a objeto para mantener compatibilidad con Blade
                'id' => $producto->id,
                'guid' => $producto->guid,
                'vendedor_id' => $producto->vendedor_id,
                'nombre' => $producto->nombre,
                'descripcion' => $producto->descripcion,
                'estadoFisico' => $producto->estadoFisico,
                'precio' => $producto->precio,
                'categoria' => $producto->categoria,
                'estado' => $producto->estado,
                'imagenes' => $producto->imagenes,
                'created_at' => $producto->created_at->toDateTimeString(),
                'updated_at' => $producto->updated_at->toDateTimeString(),
            ];
        }));

        // Retornar la vista con los productos paginados
        return view('pages.home', compact('productos'));
    }

    public function search()
    {
        $query = Producto::query();

        if (request()->has('search') && request('search') !== '') {
            $search = request('search');

            $normalizedSearch = Str::lower(Str::replace(
                ['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú'],
                ['a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u'],
                $search
            ));

            $query->whereRaw("LOWER(REPLACE(nombre, 'á', 'a')) LIKE ?", ["%{$normalizedSearch}%"]);
        }

        if (request()->has('categoria') && request('categoria') !== 'todos') {
            $query->where('categoria', request('categoria'));
        }

        $productos = $query->paginate(15);

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
