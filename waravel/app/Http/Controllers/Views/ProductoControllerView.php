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
        $query = Producto::with('vendedor', 'clientesFavoritos')
            ->where('estado', 'Disponible');

        if (auth()->check()) {
            $query->where('vendedor_id', '!=', auth()->id());
        }

        $productos = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('pages.home', compact('productos'));
    }

    public function search()
    {
        $query = Producto::query()->where('estado', 'Disponible');

        if (auth()->check()) {
            $query->where('vendedor_id', '!=', auth()->id());
        }

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

        $productos = $query->orderBy('created_at', 'desc')->paginate(15);

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

        return view('pages.ver-producto', compact('producto'));
    }
}
