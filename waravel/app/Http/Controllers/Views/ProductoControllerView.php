<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
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
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'estadoFisico' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'categoria' => 'required|string|max:255',
            'imagen1' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'imagen2' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'imagen3' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'imagen4' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'imagen5' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $producto = Producto::create([
            'guid' => Str::uuid()->toString(),
            'vendedor_id' => auth()->user()->cliente->id ?? null,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'estadoFisico' => $request->estadoFisico,
            'precio' => $request->precio,
            'categoria' => $request->categoria,
            'estado' => 'Disponible',
            'imagenes' => [],
        ]);

        for ($i = 1; $i <= 5; $i++) {
            if ($request->hasFile("imagen$i")) {
                $imagenRequest = Request::create('/', 'POST', [], [], ['image' => $request->file("imagen$i")], []);

                if (!$imagenRequest->hasFile('image')) {
                    return response()->json(['error' => "Error procesando imagen$i"], 400);
                }

                $this->addListingPhoto($imagenRequest, $producto->id);

                try {
                    $producto->refresh();
                } catch (\Exception $e) {
                    Log::error("Error al refrescar el producto: " . $e->getMessage());
                }
            }
        }

        Cache::put("producto_{$producto->guid}", $producto, now()->addMinutes(60));

        return redirect()->route('profile')->with('success', 'Producto añadido correctamente.');
    }


    public function showAddForm()
    {
        Log::info('Acceso al formulario de creación de producto');
        return view('profile.add-producto');
    }

    public function addListingPhoto(Request $request, $id) {
        $product = Producto::find($id);

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $images = is_array($product->imagenes) ? $product->imagenes : [];

        if (count($images) >= 5) {
            return response()->json(['message' => 'Máximo de 5 imágenes alcanzado'], 422);
        }

        $file = $request->file('image');
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $filePath = "productos/{$product->guid}/" . $filename;
        $file->storeAs("public/productos/{$product->guid}", $filename);

        $product->imagenes = array_merge($images, [$filePath]);
        $product->save();

        Redis::set('producto_' . $id, json_encode($product->fresh()->toArray()));

        return response()->json(['message' => 'Foto añadida', 'product' => $product]);
    }
}
