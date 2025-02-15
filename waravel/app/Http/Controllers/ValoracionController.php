<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ValoracionController extends Controller
{
    // Mostrar todas las valoraciones
    public function index()
    {
        $valoraciones = Valoracion::with('clienteValorado', 'creador')->get();
        return response()->json($valoraciones);
    }

    // Mostrar una valoración específica
    public function show($id)
    {
        $valoracion = Valoracion::with('clienteValorado', 'creador')->find($id);
        if (!$valoracion) {
            return response()->json(['message' => 'Valoración no encontrada'], 404);
        }
        return response()->json($valoracion);
    }

    // Crear una nueva valoración
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guid' => 'required|unique:valoracions,guid',
            'comentario' => 'required|string|max:1000',
            'puntuacion' => 'required|integer|min:1|max:5',
            'clienteValorado_id' => 'required|exists:clientes,id',
            'autor_id' => 'required|exists:clientes,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $valoracion = Valoracion::create($request->all());
        return response()->json($valoracion, 201);
    }

    // Actualizar una valoración existente
    public function update(Request $request, $id)
    {
        $valoracion = Valoracion::find($id);
        if (!$valoracion) {
            return response()->json(['message' => 'Valoración no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'comentario' => 'string|max:1000',
            'puntuacion' => 'integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $valoracion->update($request->all());
        return response()->json($valoracion);
    }

    // Eliminar una valoración
    public function destroy($id)
    {
        $valoracion = Valoracion::find($id);
        if (!$valoracion) {
            return response()->json(['message' => 'Valoración no encontrada'], 404);
        }

        $valoracion->delete();
        return response()->json(['message' => 'Valoración eliminada correctamente']);
    }
}
