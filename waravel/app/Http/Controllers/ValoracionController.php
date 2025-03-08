<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Valoracion;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redis;

class ValoracionController extends Controller
{
    /**
     * Muestra todas las valoraciones con paginación.
     *
     * Este método obtiene todas las valoraciones de la base de datos y las devuelve paginadas,
     * además, transforma los datos para que contengan solo los campos relevantes.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        Log::info('Obteniendo todas las valoraciones');
        $query = Valoracion::orderBy('id', 'asc');

        $valoraciones = $query->paginate(5);

        $data = $valoraciones->getCollection()->transform(function ($valoracion) {
            return [
                'id' => $valoracion->id,
                'guid' => $valoracion->guid,
                'comentario' => $valoracion->comentario,
                'puntuacion' => $valoracion->puntuacion,
                'clienteValorado_id' => $valoracion->clienteValorado_id,
                'autor_id' => $valoracion->autor_id,
                'created_at' => $valoracion->created_at->toDateTimeString(),
                'updated_at' => $valoracion->updated_at->toDateTimeString(),
            ];
        });

        $customResponse = [
            'valoraciones' => $data,
            'paginacion' => [
                'pagina_actual' => $valoraciones->currentPage(),
                'elementos_por_pagina' => $valoraciones->perPage(),
                'ultima_pagina' => $valoraciones->lastPage(),
                'elementos_totales' => $valoraciones->total(),
            ],
        ];

        Log::info('Valoraciones obtenidas');
        return response()->json($customResponse);
    }

    /**
     * Muestra una valoración específica.
     *
     * Este método intenta obtener una valoración específica por su GUID. Si la valoración
     * está en la caché de Redis, se devuelve directamente desde allí. Si no está en caché,
     * se busca en la base de datos, se guarda en Redis para futuras consultas, y luego se devuelve.
     *
     * @param string $guid
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($guid)
    {
        Log::info('Obteniendo valoración', ['id' => $guid]);
        $valoracionRedis = Redis::get('valoracion_' . $guid);

        if ($valoracionRedis) {
            Log::info('Valoración obtenida desde Redis');
            return response()->json(json_decode($valoracionRedis));
        }

        Log::info('Valoración no encontrada en Redis, buscando en la base de datos');
        $valoracion = Valoracion::where('guid',$guid)->first();

        if (!$valoracion) {
            return response()->json(['message' => 'Valoracion no encontrada'], 404);
        }

        Redis::set('valoracion_' . $guid, json_encode($valoracion), 'EX', 1800);
        Log::info('Valoración obtenida de la base de datos y almacenada en Redis');

        return response()->json($valoracion);
    }

    /**
     * Crea una nueva valoración.
     *
     * Este método valida los datos recibidos en la solicitud, como el comentario, puntuación,
     * cliente valorado y autor de la valoración. Si los datos son válidos, crea una nueva
     * valoración en la base de datos.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Log::info('Intentando crear una nueva valoración', ['request' => $request->all()]);
        $validator = Validator::make($request->all(), [
            'comentario' => 'required|string|max:1000',
            'puntuacion' => 'required|integer|min:1|max:5',
            'clienteValorado_id' => 'required|exists:clientes,id',
            'autor_id' => 'required|exists:clientes,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $valoracion = Valoracion::create($request->all());

        Log::info('Valoración creada exitosamente');

        return response()->json($valoracion, 201);
    }

    /**
     * Elimina una valoración específica.
     *
     * Este método intenta eliminar una valoración especificada por su GUID. Primero, busca
     * la valoración en Redis y, si no está allí, la busca en la base de datos. Luego,
     * elimina la valoración y limpia la caché de Redis asociada a esa valoración.
     *
     * @param string $guid
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($guid)
    {
        Log::info('Intentando eliminar valoración');
        $valoracion = Redis::get('valoracion_'. $guid);

        if (!$valoracion) {
            Log::info('Valoración no encontrada en Redis, buscando en la base de datos');
            $valoracion = Valoracion::where('guid',$guid)->first();
        } else {
            $valoracion = Valoracion::where('guid',$guid)->first();
        }

        if (!$valoracion) {
            return response()->json(['message' => 'Valoracion no encontrada'], 404);
        }

        if (is_array($valoracion)) {
            $valoracionModel = Valoracion::hydrate([$valoracion])->first();
        } else {
            $valoracionModel = $valoracion;
        }

        Log::info('Eliminando valoración con ID: ' . $guid);
        $valoracionModel->delete();

        // Limpiar caché de la valoración y de la lista
        Redis::del('valoracion_'. $guid);
        Log::info('Valoración eliminada correctamente y caché limpiado');

        return response()->json(['message' => 'Valoracion eliminada correctamente']);
    }
}
