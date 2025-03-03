<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redis;

class ClienteController extends Controller
{
    // Mostrar todos los clientes
    public function index()
    {
        $query = Cliente::orderBy('id', 'asc');

        $clientes = $query->paginate(5);

        Log::info('Obteniendo todos los clientes de la base de datos');

        $data = $clientes->getCollection()->transform(function ($cliente) {
            return [
                'id' => $cliente->id,
                'guid' => $cliente->guid,
                'nombre' => $cliente->nombre,
                'apellido' => $cliente->apellido,
                'avatar' => $cliente->avatar,
                'telefono' => $cliente->telefono,
                'direccion' => $cliente->direccion,
                'activo' => $cliente->activo,
                'usuario_id' => $cliente->usuario_id,
                'created_at' => $cliente->created_at->toDateTimeString(),
                'updated_at' => $cliente->updated_at->toDateTimeString(),
            ];
        });

        $customResponse = [
            'clientes' => $data,
            'paginacion' => [
                'pagina_actual' => $clientes->currentPage(),
                'elementos_por_pagina' => $clientes->perPage(),
                'ultima_pagina' => $clientes->lastPage(),
                'elementos_totales' => $clientes->total(),
            ],
        ];
        Log::info('Clientes obtenidos de la base de datos correctamente');
        return response()->json($customResponse);
    }

    // Mostrar un cliente específico
    public function show($guid)
    {
        Log::info('Buscando cliente de la cache en Redis');
        $clienteRedis = Redis::get('cliente_' . $guid);

        if ($clienteRedis) {
            return response()->json(json_decode($clienteRedis));
        }
        Log::info('Buscando cliente de la base de datos');
        $cliente = Cliente::where('guid', $guid)->first();

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        Log::info('Guardando cliente en cache redis');
        Redis::set('cliente_' . $guid, json_encode($cliente), 'EX', 1800);

        Log::info('Cliente obtenido correctamente');
        return response()->json($cliente);
    }

    // Crear un nuevo cliente
    public function store(Request $request)
    {
        Log::info('Validando cliente');
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'avatar' => 'nullable|string',
            'telefono' => 'required|string|min:9|max:9',
            'direccion' => 'required|array',
            'activo' => 'required|boolean',
            'usuario_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        Log::info('Guardando cliente en base de datos');

        // Si no hay avatar se añade el valor por defecto
        if (empty($data['avatar'])) {
            $data['avatar'] = 'avatar.png';
        }

        Log::info('Cliente guardado correctamente');
        $cliente = Cliente::create($data);

        return response()->json($cliente, 201);
    }

    // Actualizar un cliente existente
    public function update(Request $request, $guid)
    {

        Log::info('Buscando Cliente de la cache en Redis');
        $cliente = Redis::get('cliente_' . $guid);
        if (!$cliente) {
            Log::info('Buscando cliente de la base de datos');
            $cliente = Cliente::where('guid',$guid)->first();
        }

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        if (is_string($cliente)) {
            $clienteArray = json_decode($cliente, true);
            $clienteModel = Cliente::hydrate([$clienteArray])->first();
        } elseif ($cliente instanceof Cliente) {
            $clienteModel = $cliente;
        } else {
            $clienteModel = Cliente::hydrate([$cliente])->first();
        }

        Log::info('Validando cliente');
        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:255',
            'apellido' => 'string|max:255',
            'avatar' => 'nullable|string',
            'telefono' => 'nullable|string|min:9|max:9',
            'direccion' => 'nullable|array',
            'activo' => 'boolean',
            'usuario_id' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        Log::info('Actualizando cliente en base de datos');
        $clienteModel->update($request->all());

        // Limpiar caché del cliente
        Log::info('Eliminando cliente de la cache');
        Redis::del('cliente_' . $guid);
        Log::info('Actualizando cliente en cache');
        Redis::set('cliente_' . $guid, json_encode($clienteModel->toArray()), 'EX', 1800);
        Log::info('Cliente actualizado correctamente');
        return response()->json($clienteModel);
    }

    // Eliminar un cliente
    public function destroy($guid)
    {
        Log::info('Buscando cliente en la caché de Redis');

        // Intenta obtener el cliente desde Redis
        $cliente = Redis::get('cliente_' . $guid);

        if ($cliente) {
            Log::info('Cliente encontrado en Redis');
            $cliente = Cliente::where('guid', $guid)->first();
        }

        // Si no está en Redis, buscar en la base de datos
        if (!$cliente) {
            Log::info('Buscando cliente en la base de datos');
            $cliente = Cliente::where('guid', $guid)->first();
        }

        // Si el cliente no existe, devolver error 404
        if (!$cliente) {
            Log::warning("Cliente con GUID $guid no encontrado");
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        Log::info('Eliminando cliente de la base de datos');
        $cliente->delete();

        Log::info('Eliminando cliente de la caché de Redis');
        Redis::del('cliente_' . $guid);

        Log::info('Cliente eliminado correctamente');
        return response()->json(['message' => 'Cliente eliminado correctamente']);
    }

    // Buscar favoritos
    public function searchFavorites($guid)
    {
        Log::info("Buscando favoritos para el cliente con Guid: {$guid}");

        $cliente = Cliente::where('guid',$guid)->first();

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $favoritos = $cliente->favoritos;
        Log::info("Se encontraron " . count($favoritos) . " favoritos para el cliente con ID: {$guid}");

        return response()->json($favoritos);
    }

    // Agregar un producto a favoritos
    public function addToFavorites(Request $request, $guid)
    {
        Log::info("Intentando agregar un producto a favoritos para el cliente con Guid: {$guid}");

        $cliente = Cliente::where('guid', $guid)->first();
        if (!$cliente) {
            Log::info("Cliente con Guid {$guid} no encontrado");
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $producto = Producto::where('guid', $request->producto_guid)->first();
        if (!$producto) {
            Log::info("Producto con Guid {$request->producto_guid} no encontrado");
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        // Agregar el producto a los favoritos usando el 'id' de ambos, cliente y producto
        $cliente->favoritos()->attach($producto->id);

        Log::info("Producto con Id {$producto->id} agregado a favoritos");

        return response()->json(['message' => 'Producto agregado a favoritos']);
    }


    // Quitar un producto de favoritos
    public function removeFromFavorites(Request $request, $guid)
    {
        Log::info("Intentando eliminar un producto de favoritos para el cliente con Guid: {$guid}");

        // Buscar el cliente por 'guid'
        $cliente = Cliente::where('guid', $guid)->first();
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        // Buscar el producto por 'id' (no 'guid')
        $producto = Producto::find($request->producto_id); // Cambiar 'producto_guid' por 'producto_id'
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        // Eliminar el producto de los favoritos
        $cliente->favoritos()->detach($producto->id); // Usamos 'producto_id' aquí

        Log::info("Producto con Id {$producto->id} eliminado de favoritos para el cliente con guid: {$guid}");

        return response()->json(['message' => 'Producto eliminado de favoritos']);
    }

    public function updateProfilePhoto(Request $request, $guid) {
        Log::info("Intentando actualizar la foto de perfil para el cliente con guid: {$guid}");
        $cliente = Redis::get('cliente_' . $guid);

        if (!$cliente) {
            $cliente = Cliente::where('guid',$guid)->first();
        }else{
            $cliente = Cliente::hydrate([$cliente])->first();;
        }

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('avatar');
        $filename = $cliente->guid . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('clientes/avatares', $filename, 'public');

        Cliente::where('guid', $guid)->update(['avatar' => $filePath]);
        Log::info("Avatar actualizado para el cliente con ID: {$guid}");

        // Limpiar caché del cliente
        Redis::del('cliente_' . $guid);

        return response()->json(['message' => 'Avatar actualizado', 'cliente' => $cliente]);
    }
}
