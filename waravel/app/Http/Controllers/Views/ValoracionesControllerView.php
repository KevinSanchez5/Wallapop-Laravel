<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Models\Valoracion;
use App\Models\Cliente;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ValoracionesControllerView extends Controller
{

    public function index()
    {
        Log::info('Obteniendo todas las valoraciones');

        // Consulta de las valoraciones ordenadas por ID
        $valoraciones = Valoracion::orderBy('id', 'asc')->paginate(5);


        $data = $valoraciones->map(function ($valoracion) {
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

        // Respuesta personalizada con los datos de las valoraciones y paginación
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

        return view('clientes.valoraciones', compact('valoraciones', 'customResponse'));
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

    public function store(Request $request)
    {
        Log::info('Intentando crear una nueva valoración', ['request' => $request->all()]);

        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debes estar logeado para crear una valoración');
        }

        $clienteId = auth()->user()->id;

        // Verificar que el autor de la valoración es el comprador del producto
        $producto = Producto::where('guid', $request->guid_producto)->first();

        if (!$producto) {
            return redirect()->route('profile')->with('error', 'Producto no encontrado');
        }

        // Verificar si el comprador realmente ha comprado este producto
        $compra = Compra::where('producto_id', $producto->id)
            ->where('cliente_id', $clienteId)
            ->first();

        if (!$compra) {
            return redirect()->route('profile')->with('error', 'Solo el comprador de este producto puede dejar una valoración');
        }

        // Validación de los datos de la valoración
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

        // Crear la valoración
        $valoracion = Valoracion::create([
            'guid' => $request->guid,
            'comentario' => $request->comentario,
            'puntuacion' => $request->puntuacion,
            'clienteValorado_id' => $request->clienteValorado_id,
            'autor_id' => $clienteId,
            'producto_id' => $producto->id,
        ]);

        Log::info('Valoración creada exitosamente');

        return redirect()->route('profile')->with('success', 'Valoración añadida correctamente.');
    }


    public function destroy($guid)
    {
        Log::info('Intentando eliminar valoración');

        // Obtener la valoración desde el caché o base de datos
        $valoracion = Cache::get('valoracion_' . $guid);

        // Verificar si la valoración existe
        if (!$valoracion) {
            return response()->json(['message' => 'Valoración no encontrada'], 404);
        }

        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debes estar logeado para eliminar una valoración');
        }

        $clienteId = auth()->user()->id;

        // Verificar si el usuario es el comprador o un administrador
        if ($valoracion->autor_id !== $clienteId && !auth()->user()->is_admin) {
            Log::warning('Usuario no autorizado para eliminar esta valoración', ['guid' => $guid]);
            return redirect()->route('profile')->with('error', 'No tienes permiso para eliminar esta valoración');
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
        Cache::forget('valoracion_' . $guid);
        Log::info('Valoración eliminada correctamente y caché limpiado');

        return redirect()->route('profile')->with('success', 'Valoración eliminada correctamente.');
    }

    public function writeReview($guid){
        Log::info('Accediendo a la página de detalle del pedido');

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus pedidos.');
        }

        Log::info('Autenticando usuario');
        $usuario = Auth::user();

        Log::info('Buscando el perfil del cliente en la base de datos');
        $cliente = Cliente::where('usuario_id', $usuario->id)->first();

        if (!$cliente) {
            return redirect()->route('home')->with('error', 'No se ha encontrado el perfil del cliente.');
        }

        Log::info('Perfil del cliente encontrado, obteniendo pedidos');

        Log::info('Buscando la venta');

        $pedido = Venta::where('guid', $guid)->first();

        if (!$pedido) {
            return redirect()->route('profile')->with('error', 'No se ha encontrado el pedido.');
        }

        Log::info('Pedido encontrado, verificando que le pertenece al cliente');

        if ($cliente->id !== $pedido->comprador->id) {
            Log::error('El pedido no le pertenece al cliente.');
            return redirect()->route('profile')->with('error', 'No tienes permisos para ver este pedido.');
        }

        Log::info('Pedido válido, verificando que no se haya realizado una valoración todavía');

        $valoracionExistente = Valoracion::where('venta_id', $pedido->id)->first();

        if ($valoracionExistente) {
            return redirect()->route('profile')->with('error', 'Ya has realizado una valoración en este pedido.');
        }

        Log::info('Pedido válido, redireccionando a la página de escribir valoración');

        return view('pages.write-review', compact('cliente', 'usuario', 'pedido'));
    }

    public function storeReview(Request $request, $guid){
        Log::info('Intentando crear una nueva valoración');

        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debes estar logeado para escribir una valoración');
        }

        $clienteId = auth()->user()->id;

        // Verificar que el cliente es el mismo que realizó el pedido
        $cliente = Cliente::where('usuario_id', $clienteId)->first();

        if (!$cliente) {
            return redirect()->route('home')->with('error', 'No se ha encontrado el cliente.');
        }

        // Verificar que el pedido existe y pertenece al cliente
        $pedido = Venta::where('guid', $guid)->first();

        if (!$pedido) {
            return redirect()->route('profile')->with('error', 'No se ha encontrado el pedido.');
        }

        $valoracionExistente = Valoracion::where('venta_id', $pedido->id)->first();

        if ($valoracionExistente) {
            return redirect()->back()->with('error', 'Ya has realizado una valoración en este pedido.');
        }

        if ($pedido->estado != 'Entregado'){
            Log::error('El pedido no está en estado entregado.');
            return redirect()->back()->with('error', 'No puedes escribir una valoración en un pedido que no ha sido entregado.');
        }

        if ($cliente->id!== $pedido->comprador->id) {
            Log::error('El cliente no es el mismo que realizó el pedido.');
            return redirect()->back()->with('error', 'No tienes permisos para escribir una valoración en este pedido.');
        }

        // Validación de los datos de la valoración
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|min:10',
            'rating' => 'required|integer|between:1,5',
        ], [
            'comment.required' => 'El comentario es obligatorio.',
            'comment.min' => 'El comentario debe tener al menos 10 caracteres.',
            'rating.required' => 'La calificación es obligatoria.',
            'rating.between' => 'La calificación debe estar entre 1 y 5.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $comment = $request->get('comment');
        $rating = $request->get('rating');

        // Crear las valoraciónes
        foreach ($pedido->lineaVentas as $line){
            $valoracion = new Valoracion();
            $valoracion->comentario = $comment;
            $valoracion->puntuacion = $rating;
            $valoracion->clienteValorado_id = $line['vendedor']['id'];
            $valoracion->autor_id = $cliente->id;
            $valoracion->venta_id = $pedido->id;
            $valoracion->save();
        }

        Log::info('Valoración creada exitosamente');
        return redirect()->back();
    }
}
