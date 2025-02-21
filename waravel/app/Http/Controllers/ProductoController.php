<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redis;

use Illuminate\Support\Str;

class ProductoController extends Controller
{
    // Mostrar todos los productos
    public function index()
    {
        $query = Producto::orderBy('id', 'asc');

        $productos = $query->paginate(15);

        Log::info('Obteniendo todos los productos de la base de datos');
        $data = $productos->getCollection()->transform(function ($producto) {
           return [
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
        });

        $customResponse = [
            'productos' => $data,
            'paginacion' => [
                'pagina_actual' => $productos->currentPage(),
                'elementos_por_pagina' => $productos->perPage(),
                'ultima_pagina' => $productos->lastPage(),
                'elementos_totales' => $productos->total(),
            ],
        ];

        Log::info('Productos obtenidos de la base de datos correctamente');
        return response()->json($customResponse);
    }

    // Mostrar un producto específico
    public function show($id)
    {
        Log::info('Buscando producto de la cache en Redis');
        $productoRedis = Redis::get('producto_'.$id);

        if ($productoRedis) {
            return response()->json(json_decode($productoRedis));
        }

        Log::info('Buscando producto de la base de datos');
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        Redis::set('producto_'. $id, json_encode($producto), 'EX',1800);

        Log::info('Producto obtenido correctamente');
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

        if (is_string($producto)) {
            $productoArray = json_decode($producto, true);
            $productoModel = Producto::hydrate([$productoArray])->first();
        } elseif ($producto instanceof Producto) {
            $productoModel = $producto;
        } else {
            $productoModel = Producto::hydrate([$producto])->first();
        }

        $validator = Validator::make($request->all(), [
            'nombre'       => 'string|max:255',
            'descripcion'  => 'required|string',
            'estadoFisico' => 'required|string|max:255',
            'precio'       => 'numeric|min:0',
            'categoria'    => 'string|max:255',
            'estado'       => 'string|max:255',
            //'imagenes'   => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors'  => $validator->errors()
            ], 422);
        }

        $productoModel->update($request->all());

        // Limpiar caché del producto y de la lista
        Redis::del('producto_' . $id);
        Redis::set('producto_' . $id, json_encode($productoModel), 'EX', 1800);

        return response()->json($productoModel);
    }

    // Eliminar un producto
    public function destroy($id)
    {
        $producto = Redis::get('producto_' . $id);

        if ($producto) {
            $producto = json_decode($producto, true);
        } else {
            $producto = Producto::find($id);
        }

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        if (is_array($producto)) {
            $productoModel = Producto::hydrate([$producto])->first();
        } else {
            $productoModel = $producto;
        }

        $productoModel->delete();

        // Limpiar caché del producto
        Redis::del('producto_' . $id);

        return response()->json(['message' => 'Producto eliminado correctamente']);
    }


    public function addListingPhoto(Request $request, $id) {
        $product = Redis::get('producto_' . $id);

        if (!$product) {
            $product = Producto::find($id);
        }else{
            $product = Producto::hydrate(json_decode($product, true));
        }

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $images = $product->imagenes ?? [];
        if (count($images) >= 5) {
            return response()->json(['message' => 'Solo se pueden subir un máximo de 5 fotos'], 422);
        }

        $file = $request->file('image');
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs("productos/{$product->guid}", $filename, 'public');

        $product->imagenes = array_merge($images, [$filePath]);
        $product->save();


        return response()->json(['message' => 'Foto añadida', 'product' => $product]);
    }

    public function deleteListingPhoto($Id, Request $request) {
        $filePath = $request->input('image');

        if (!$filePath) {
            return response()->json(['message' => 'Foto no proporcionada'], 422);
        }

        $product = Redis::get('producto_' . $Id);

        if (!$product) {
            $product = Producto::find($Id);
        } else {
            $product = Producto::hydrate(json_decode($product, true));
        }

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $images = $product->imagenes?? [];
        $key = array_search($filePath, $images);
        if ($key === false) {
            return response()->json(['message' => 'Foto no encontrada en el producto'], 404);
        }

        Storage::disk('public')->delete($filePath);
        unset($images[$key]);
        $product->imagenes = $images;
        $product->save();

        return response()->json(['message' => 'Foto eliminada', 'product' => $product]);
    }
}
