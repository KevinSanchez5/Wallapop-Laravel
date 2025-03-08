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
    /**
     * Obtener todos los productos con paginación.
     *
     * Este método recupera todos los productos de la base de datos y los devuelve con paginación.
     *
     * @return \Illuminate\Http\JsonResponse Lista paginada de productos.
     */
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
               'stock' => $producto->stock,
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

    /**
     * Obtener un producto específico por su GUID.
     *
     * Este método obtiene un producto específico por su GUID, buscando primero en la caché (Redis),
     * y luego en la base de datos si es necesario.
     *
     * @param string $guid GUID del producto a buscar.
     *
     * @return \Illuminate\Http\JsonResponse Detalles del producto o mensaje de error.
     */
    public function show($guid)
    {
        Log::info('Buscando producto de la cache en Redis');
        $productoRedis = Redis::get('producto_'.$guid);

        if ($productoRedis) {
            return response()->json(json_decode($productoRedis));
        }

        Log::info('Buscando producto de la base de datos');
        $producto = Producto::where('guid',$guid)->first();

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        Log::info('Guardando producto en cache redis');
        Redis::set('producto_'. $guid, json_encode($producto), 'EX',1800);

        Log::info('Producto obtenido correctamente');
        return response()->json($producto);
    }

    /**
     * Almacenar un nuevo producto en la base de datos.
     *
     * Este método valida los datos de la solicitud, luego crea y guarda un nuevo producto.
     *
     * @param \Illuminate\Http\Request $request La solicitud que contiene los datos del producto.
     *
     * @return \Illuminate\Http\JsonResponse El producto creado.
     */
    public function store(Request $request)
    {
        Log::info('Validando producto');
        $validator = Validator::make($request->all(), [
            'vendedor_id' => 'required|exists:clientes,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'estadoFisico' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categoria' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'imagenes' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Log::info('Guardando producto en base de datos');
        $producto = Producto::create($request->all());

        Log::info('Producto guardado correctamente');
        return response()->json($producto, 201);
    }

    /**
     * Actualizar un producto existente en la base de datos.
     *
     * Este método actualiza un producto existente por su GUID. Valida los datos de la solicitud,
     * actualiza el registro en la base de datos y refresca la caché (Redis).
     *
     * @param \Illuminate\Http\Request $request La solicitud con los datos actualizados del producto.
     * @param string $guid GUID del producto a actualizar.
     *
     * @return \Illuminate\Http\JsonResponse El producto actualizado.
     */
    public function update(Request $request, $guid)
    {
        Log::info('Buscando producto de la cache en Redis');
        $producto = Redis::get('producto_'.$guid);
        if (!$producto) {
            Log::info('Buscando producto de la base de datos');
            $producto = Producto::where('guid',$guid)->first();
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

        Log::info('Validando producto');
        $validator = Validator::make($request->all(), [
            'nombre'       => 'string|max:255',
            'descripcion'  => 'required|string',
            'estadoFisico' => 'required|string|max:255',
            'precio'       => 'numeric|min:0',
            'stock'        => 'integer|min:0',
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

        Log::info('Actualizando producto en base de datos');
        $productoModel->update($request->all());

        Log::info('Eliminando producto de la cache');
        Redis::del('producto_' . $guid);

        Log::info('Actualizando producto en cache');
        Redis::set('producto_' . $guid, json_encode($productoModel), 'EX', 1800);

        Log::info('Producto actualizado correctamente');
        return response()->json($productoModel);
    }

    /**
     * Eliminar un producto por su GUID.
     *
     * Este método elimina un producto por su GUID tanto de la base de datos como de la caché (Redis).
     *
     * @param string $guid GUID del producto a eliminar.
     *
     * @return \Illuminate\Http\JsonResponse Mensaje de éxito o error.
     */
    public function destroy($guid)
    {
        Log::info('Buscando producto de la cache en Redis');
        $producto = Redis::get('producto_' . $guid);

        if ($producto) {
            $producto = json_decode($producto, true);
        } else {
            Log::info('Buscando producto de la base de datos');
            $producto = Producto::where('guid',$guid)->first();
        }

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        if (is_array($producto)) {
            $productoModel = Producto::hydrate([$producto])->first();
        } else {
            $productoModel = $producto;
        }

        Log::info('Eliminando producto de la base de datos');
        $productoModel->delete();

        Log::info('Eliminando producto de la cache');
        Redis::del('producto_' . $guid);

        Log::info('Producto eliminado correctamente');
        return response()->json(['message' => 'Producto eliminado correctamente']);
    }

    /**
     * Añadir una foto a un producto en la lista.
     *
     * Este método permite añadir una imagen a la lista del producto, asegurando que no se exceda el límite
     * de 5 fotos. La imagen se guarda en el almacenamiento y se actualiza la lista de imágenes del producto.
     *
     * @param \Illuminate\Http\Request $request La solicitud que contiene la imagen a añadir.
     * @param string $guid GUID del producto al que se añadirá la imagen.
     *
     * @return \Illuminate\Http\JsonResponse Mensaje de éxito y producto actualizado.
     */

    public function addListingPhoto(Request $request, $guid) {
        Log::info('Buscando producto de la cache en Redis');
        $product = Redis::get('producto_' . $guid);

        if (!$product) {
            Log::info('Buscando producto de la base de datos');
            $product = Producto::where('guid',$guid)->first();
        }else{
            $product = Producto::hydrate(json_decode($product, true));
        }

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        Log::info('Validando producto');
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Log::info('Verificando numero de imagenes');
        $images = $product->imagenes ?? [];
        if (count($images) >= 5) {
            return response()->json(['message' => 'Solo se pueden subir un máximo de 5 fotos'], 422);
        }

        Log::info('Guardando imagen del producto en storage');
        $file = $request->file('image');
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs("productos/{$product->guid}", $filename, 'public');

        $product->imagenes = array_merge($images, [$filePath]);
        Log::info('Guardando imagen del producto');
        $product->save();

        Log::info('Eliminando producto de la cache');
        Redis::del('producto_' . $guid);

        Log::info('Imagen guardada correctamente');
        return response()->json(['message' => 'Foto añadida', 'product' => $product]);
    }

    /**
     * Eliminar una foto de la lista de un producto.
     *
     * Este método elimina una foto específica de la lista del producto y también la elimina del almacenamiento.
     *
     * @param string $guid GUID del producto del cual se eliminará la foto.
     * @param \Illuminate\Http\Request $request La solicitud que contiene la ruta de la foto a eliminar.
     *
     * @return \Illuminate\Http\JsonResponse Mensaje de éxito y producto actualizado.
     */

    public function deleteListingPhoto($guid, Request $request) {
        $filePath = $request->input('image');

        if (!$filePath) {
            return response()->json(['message' => 'Foto no proporcionada'], 422);
        }

        Log::info('Buscando producto de la cache en Redis');
        $product = Redis::get('producto_' . $guid);

        if (!$product) {
            Log::info('Buscando producto de la base de datos');
            $product = Producto::where('guid',$guid)->firstOrFail();
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

        Log::info('Eliminando imagen del storage');
        Storage::disk('public')->delete($filePath);
        unset($images[$key]);
        $product->imagenes = $images;
        Log::info('Eliminando imagen del producto');
        $product->save();

        Log::info('Eliminando producto de la cache');
        Redis::del('producto_' . $guid);

        Log::info('Imagen eliminada correctamente');
        return response()->json(['message' => 'Foto eliminada', 'product' => $product]);
    }
}
