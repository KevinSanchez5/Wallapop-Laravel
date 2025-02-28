<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Valoracion;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redis;

class ValoracionController extends Controller
{
    // Mostrar todas las valoraciones
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

    // Mostrar una valoración específica
    public function show($guid)
    {
        Log::info('Obteniendo valoración', ['id' => $guid]);
        $valoracionRedis = Redis::get('valoracion_' . $guid);

        if ($valoracionRedis) {
            Log::info('Valoración obtenida desde Redis');
            return response()->json(json_decode($valoracionRedis));
        }

        Log::info('Valoración no encontrada en Redis, buscando en la base de datos');
        $valoracion = Valoracion::where('guid',$guid)->firstOrFail();

        if (!$valoracion) {
            return response()->json(['message' => 'Valoracion no encontrada'], 404);
        }

        Redis::set('valoracion_' . $guid, json_encode($valoracion), 'EX', 1800);
        Log::info('Valoración obtenida de la base de datos y almacenada en Redis');

        return response()->json($valoracion);
    }

    // Crear una nueva valoración
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

    // Eliminar una valoración
    public function destroy($guid)
    {
        Log::info('Intentando eliminar valoración');
        $valoracion = Redis::get('valoracion_'. $guid);

        if (!$valoracion) {
            Log::info('Valoración no encontrada en Redis, buscando en la base de datos');
            $valoracion = Valoracion::where('guid',$guid)->firstOrFail();
        } else {
            $valoracion = Valoracion::where('guid',$guid)->firstOrFail();
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
