<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductoControllerView extends Controller
{
    protected function getClienteId()
    {
        return Cliente::where('usuario_id', auth()->id())->value('id');
    }

    public function indexVista()
    {
        Log::info('Acceso a la vista de productos disponibles');

        $query = Producto::with('vendedor', 'clientesFavoritos')
            ->where('estado', 'Disponible');

        if (auth()->check()) {
            Log::info('Usuario autenticado, excluyendo sus productos');
            $clienteId = $this->getClienteId();
            $query->where('vendedor_id', '!=', $clienteId);
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
            $clienteId = $this->getClienteId();
            $query->where('vendedor_id', '!=', $clienteId);
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
        Log::info('Creando un nuevo producto');

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

        $clienteId = $this->getClienteId();

        $producto = Producto::create([
            'guid' => Str::uuid()->toString(),
            'vendedor_id' => $clienteId,
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
                    Log::error("Error procesando imagen$i");
                    return response()->json(['error' => "Error procesando imagen$i"], 400);
                }

                $this->addListingPhoto($imagenRequest, $producto->id);
            }
        }

        Cache::put("producto_{$producto->guid}", $producto, now()->addMinutes(60));

        Log::info('Producto creado correctamente', ['producto' => $producto]);

        return redirect()->route('profile')->with('success', 'Producto añadido correctamente.');
    }

    public function edit($guid)
    {
        Log::info('Acceso al formulario de edición del producto con GUID: ' . $guid);

        $producto = Producto::where('guid', $guid)->first();

        if (!$producto) {
            Log::warning('Producto no encontrado', ['guid' => $guid]);
            return redirect()->route('profile')->with('error', 'Producto no encontrado');
        }

        $clienteId = $this->getClienteId();

        if ($clienteId !== $producto->vendedor_id) {
            Log::warning('Usuario no autorizado para editar el producto', ['guid' => $guid]);
            return redirect()->route('profile')->with('error', 'No tienes permiso para editar este producto');
        }

        Log::info('Producto listo para editar', ['producto' => $producto]);

        return view('profile.edit-producto', compact('producto'));
    }

    public function update(Request $request, $guid)
    {
        Log::info('Actualizando producto con GUID: ' . $guid);

        $producto = Producto::where('guid', $guid)->first();

        if (!$producto) {
            Log::warning('Producto no encontrado', ['guid' => $guid]);
            return redirect()->route('profile')->with('error', 'Producto no encontrado');
        }

        $clienteId = $this->getClienteId();

        if ($clienteId !== $producto->vendedor_id) {
            Log::warning('Usuario no autorizado para editar el producto', ['guid' => $guid]);
            return redirect()->route('profile')->with('error', 'No tienes permiso para editar este producto');
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'estadoFisico' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'categoria' => 'required|string|max:255',
            'imagen1' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'imagen2' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'imagen3' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'imagen4' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'imagen5' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $producto->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'estadoFisico' => $request->estadoFisico,
            'precio' => $request->precio,
            'categoria' => $request->categoria,
        ]);

        for ($i = 1; $i <= 5; $i++) {
            if ($request->hasFile("imagen$i")) {
                $imagenRequest = Request::create('/', 'POST', [], [], ['image' => $request->file("imagen$i")], []);

                if (!$imagenRequest->hasFile('image')) {
                    Log::error("Error procesando imagen$i");
                    return response()->json(['error' => "Error procesando imagen$i"], 400);
                }

                $this->addListingPhoto($imagenRequest, $producto->id);
            }
        }

        Cache::put("producto_{$producto->guid}", $producto, now()->addMinutes(60));

        Log::info('Producto actualizado correctamente', ['producto' => $producto]);

        return redirect()->route('profile')->with('success', 'Producto actualizado correctamente.');
    }

    public function showAddForm()
    {
        Log::info('Acceso al formulario de creación de producto');
        return view('profile.add-producto');
    }

    public function addListingPhoto(Request $request, $id)
    {
        $product = Producto::find($id);

        if (!$product) {
            Log::error('Producto no encontrado', ['id' => $id]);
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        Log::info('Producto encontrado', ['product' => $product]);

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        if ($validator->fails()) {
            Log::error('Error de validación', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $images = is_array($product->imagenes) ? $product->imagenes : [];

        if (count($images) >= 5) {
            Log::warning('Máximo de 5 imágenes alcanzado', ['product' => $product]);
            return response()->json(['message' => 'Máximo de 5 imágenes alcanzado'], 422);
        }

        $file = $request->file('image');

        if ($file && $file->isValid()) {
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs("productos/{$product->guid}", $filename, 'public');

            Log::info('Imagen almacenada', ['filePath' => $filePath]);

            $imagePath = "productos/{$product->guid}/{$filename}";
            $product->imagenes = array_merge($images, [$imagePath]);
            $product->save();

            Log::info('Producto actualizado con nuevas imágenes', ['product' => $product]);

            return response()->json(['message' => 'Foto añadida', 'product' => $product]);
        } else {
            Log::error('El archivo no es válido o no se cargó correctamente', ['file' => $file]);
            return response()->json(['message' => 'El archivo no es válido'], 422);
        }
    }

    public function destroy($guid)
    {
        Log::info('Eliminando producto con GUID: ' . $guid);

        $producto = Producto::where('guid', $guid)->first();

        if (!$producto) {
            Log::warning('Producto no encontrado', ['guid' => $guid]);
            return redirect()->route('profile')->with('error', 'Producto no encontrado');
        }

        $clienteId = $this->getClienteId();

        if ($clienteId !== $producto->vendedor_id) {
            Log::warning('Usuario no autorizado para eliminar el producto', ['guid' => $guid]);
            return redirect()->route('profile')->with('error', 'No tienes permiso para eliminar este producto');
        }

        $producto->delete();

        Log::info('Producto eliminado correctamente', ['producto' => $producto]);

        return redirect()->route('profile')->with('success', 'Producto eliminado correctamente.');
    }

    public function changestatus($guid)
    {
        $producto = Producto::where('guid', $guid)->first();

        if (!$producto) {
            return redirect()->route('profile')->with('error', 'Producto no encontrado');
        }

        // Cambiar el estado del producto
        $nuevoEstado = $producto->estado === 'Disponible' ? 'Desactivado' : 'Disponible';
        $producto->estado = $nuevoEstado;
        $producto->save();

        return redirect()->route('profile')->with('success', 'Estado del producto actualizado correctamente.');
    }
}
