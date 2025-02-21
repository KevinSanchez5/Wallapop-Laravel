<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductoControllerView extends Controller
{
    public function indexVista()
    {
        Log::info('Acceso a la vista de productos disponibles');

        $query = Producto::with('vendedor', 'clientesFavoritos')
            ->where('estado', 'Disponible');

        if (auth()->check()) {
            Log::info('Usuario autenticado, excluyendo sus productos');
            $query->where('vendedor_id', '!=', auth()->id());
        }

        $productos = $query->orderBy('created_at', 'desc')->paginate(15);

        Log::info('Productos cargados', ['total_productos' => $productos->count()]);

        return view('pages.home', compact('productos'));
    }

    public function search()
    {
        Log::info('Búsqueda de productos iniciada');

        $query = Producto::query()->where('estado', 'Disponible');

        if (auth()->check()) {
            Log::info('Usuario autenticado, excluyendo sus productos');
            $query->where('vendedor_id', '!=', auth()->id());
        }

        if (request()->has('search') && request('search') !== '') {
            $search = request('search');

            $normalizedSearch = Str::lower(Str::replace(
                ['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú'],
                ['a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u'],
                $search
            ));

            Log::info('Realizando búsqueda con término normalizado', ['search' => $normalizedSearch]);

            $query->whereRaw("LOWER(REPLACE(nombre, 'á', 'a')) LIKE ?", ["%{$normalizedSearch}%"]);
        }

        if (request()->has('categoria') && request('categoria') !== 'todos') {
            $query->where('categoria', request('categoria'));
            Log::info('Filtro por categoría aplicado', ['categoria' => request('categoria')]);
        }

        $productos = $query->orderBy('created_at', 'desc')->paginate(15);

        Log::info('Productos cargados después de la búsqueda', ['total_productos' => $productos->count()]);

        return view('pages.home', compact('productos'));
    }

    public function showVista($guid)
    {
        Log::info('Acceso al producto con GUID: ' . $guid);

        $producto = Cache::remember("producto_{$guid}", 60, function () use ($guid) {
            return Producto::with('vendedor', 'clientesFavoritos')->where('guid', $guid)->first();
        });

        if (!$producto) {
            Log::warning('Producto no encontrado', ['guid' => $guid]);
            return redirect()->route('pages.home')->with('error', 'Producto no encontrado');
        }

        Log::info('Producto encontrado', ['producto' => $producto]);

        return view('pages.ver-producto', compact('producto'));
    }

    public function store(Request $request)
    {
        Log::info('Creación de nuevo producto iniciada', ['request_data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'guid' => 'required|unique:productos,guid',
            'vendedor_id' => 'required|exists:clientes,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'estadoFisico' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'categoria' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'imagenes' => 'required|array|min:1',
            'imagenes.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            Log::error('Error de validación al crear producto', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $producto = Producto::create([
            'guid' => $request->guid,
            'vendedor_id' => $request->vendedor_id,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'estadoFisico' => $request->estadoFisico,
            'precio' => $request->precio,
            'categoria' => $request->categoria,
            'estado' => $request->estado,
            'imagenes' => [],
        ]);

        Log::info('Producto creado correctamente', ['producto_id' => $producto->id]);

        $imagenes = [];
        foreach ($request->file('imagenes') as $imagen) {
            $filename = time() . '_' . Str::random(10) . '.' . $imagen->getClientOriginalExtension();
            $filePath = $imagen->storeAs("productos/{$producto->guid}", $filename, 'public');
            $imagenes[] = $filePath;
            Log::info('Imagen subida correctamente', ['file_path' => $filePath]);
        }

        $producto->imagenes = $imagenes;
        $producto->save();

        Log::info('Producto actualizado con imágenes', ['producto_id' => $producto->id]);

        return response()->json($producto, 201);
    }

    public function showAddForm()
    {
        Log::info('Acceso al formulario de creación de producto');
        return view('add-producto');
    }
}
