<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Models\Valoracion;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ValoracionesControllerView extends Controller
{

  /*  public function index()
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
        return view('clientes.valoraciones', compact('valoraciones'));
    }
    /**
     * Muestra la lista de valoraciones de un cliente específico.
     */
    public function show($guid)
    {
        $cliente = Cliente::where('guid', $guid)->firstOrFail();
        $valoraciones = Valoracion::where('clienteValorado_id', $cliente->id)->latest()->get();

        return view('clientes.valoraciones', compact('cliente', 'valoraciones'));
    }

    /**
     * Calcula la puntuación media de un cliente y la devuelve.
     */
    public function promedio($guid)
    {
        $cliente = Cliente::where('guid', $guid)->firstOrFail();
        $promedio = Valoracion::where('clienteValorado_id', $cliente->id)->avg('puntuacion');

        return response()->json([
            'promedio' => round($promedio, 1),
            'estrellas' => str_repeat('⭐', round($promedio))
        ]);
    }
 /*
    public function store(Request $request)
    {
        Log::info('Intentando crear una nueva valoración', ['request' => $request->all()]);
        $validator = Validator::make($request->all(), [
            'guid' => 'required|unique:valoraciones,guid',
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

        return view('clientes.valoraciones') ->with('success', 'Valoración añadida correctamente.');
    }

    public function destroy($guid)
    {
        Log::info('Intentando eliminar valoración');

        // Obtener la valoración desde el caché o base de datos
        $valoracion = Cache::get('valoracion_' . $guid);

        if (!$valoracion) {
            Log::info('Valoración no encontrada en caché, buscando en la base de datos');
            $valoracion = Valoracion::where('guid', $guid)->firstOrFail();
        }

        // Verificar si la valoración existe
        if (!$valoracion) {
            return response()->json(['message' => 'Valoración no encontrada'], 404);
        }

        // Si la valoración está en formato de array, hidratarla para convertirla en modelo de Eloquent
        if (is_array($valoracion)) {
            $valoracionModel = Valoracion::hydrate([$valoracion])->first();
        } else {
            $valoracionModel = $valoracion;
        }

        Log::info('Eliminando valoración con ID: ' . $guid);
        $valoracionModel->delete();

        // Limpiar caché de la valoración y de la lista
        Cache::forget('valoracion_' . $guid); // Usar Cache::forget en lugar de Cache::del
        Log::info('Valoración eliminada correctamente y caché limpiado');

        return view('clientes.valoraciones')->with('success', 'Valoración eliminada.');
    }*/

}
