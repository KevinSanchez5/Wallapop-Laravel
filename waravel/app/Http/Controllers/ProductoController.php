<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redis;

use Illuminate\Support\Str;

class ProductoController extends Controller
{
    // Mostrar todos los productos
    public function index()
    {
        $productos = Producto::all();
        return response()->json($productos);
    }

    // Mostrar un producto específico
    public function show($id)
    {
        $productoRedis = Redis::get('producto_'.$id);

        if ($productoRedis) {
            return response()->json(json_decode($productoRedis));
        }

        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        Redis::set('producto_'. $id, json_encode($producto), 'EX',1800);

        return response()->json($producto);
    }

    // Crear un nuevo producto
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guid' => 'required|unique:productos,guid',
            'vendedor_id' => 'required|exists:clientes,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'estadoFisico' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'categoria' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'imagenes' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $producto = Producto::create($request->all());


        return response()->json($producto, 201);
    }

    // Actualizar un producto existente
    public function update(Request $request, $id)
    {
        $producto = Redis::get('producto_'.$id);
        if (!$producto) {
            $producto = Producto::find($id);
        }

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:255',
            'descripcion' => 'required|string',
            'estadoFisico' => 'required|string|max:255',
            'precio' => 'numeric|min:0',
            'categoria' => 'string|max:255',
            'estado' => 'string|max:255',
            'imagenes' => 'required|array',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $producto->update($request->all());

        // Limpiar caché del producto y de la lista
        Redis::del('producto_' . $id);
        Redis::set('producto_' . $id, json_encode($producto), 'EX', 1800);

        return response()->json($producto);
    }

    // Eliminar un producto
    public function destroy($id)
    {
        $producto = Redis::get('producto_'. $id);

        if (!$producto) {
            $producto = Producto::find($id);
        }
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $producto->delete();

        // Limpiar caché del producto y de la lista
        Redis::del('producto_'. $id);


        return response()->json(['message' => 'Producto eliminado correctamente']);
    }


    public function addListingPhoto(Request $request, $id) {
        $product = Producto::find($id);
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verificar que no tiene más de 5 fotos
        $images = $product->imagenes ?? [];
        if (count($images) >= 5) {
            return response()->json(['message' => 'Solo se pueden subir un máximo de 5 fotos'], 422);
        }

        $file = $request->file('image');
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs("products/{$product->guid}", $filename, 'public');

        $product->imagenes = array_merge($images, [$filePath]);
        $product->save();


        return response()->json(['message' => 'Foto añadida', 'product' => $product]);
    }


    public function indexVista()
    {
        // Obtenemos los productos desde la base de datos
        $productos = Producto::with('vendedor', 'clientesFavoritos')->get();

        // Devolvemos la vista de inicio con los productos
        return view('pages.home', compact('productos'));
    }

    public function showVista($id)
    {
        if (!is_numeric($id)) {
            return redirect()->route('inicio')->with('error', 'ID no válido');
        }

        $producto = Cache::remember("producto_{$id}", 60, function () use ($id) {
            return Producto::with('vendedor', 'clientesFavoritos')->find($id);
        });

        if (!$producto) {
            return redirect()->route('inicio')->with('error', 'Producto no encontrado');
        }

        return view('productos.show', compact('producto'));
    }

}
