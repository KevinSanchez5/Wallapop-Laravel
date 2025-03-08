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
    public function writeReview($guid){
        Log::info('Accediendo a la página de detalle del pedido');

        Log::info('Autenticando usuario');
        $usuario = Auth::user();

        Log::info('Buscando el perfil del cliente en la base de datos');
        $cliente = Cliente::where('usuario_id', $usuario->id)->first();

        if (!$cliente) {
            return redirect()->route('pages.home')->with('error', 'No se ha encontrado el perfil del cliente.');
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

        $clienteId = auth()->user()->id;

        // Verificar que el cliente es el mismo que realizó el pedido
        $cliente = Cliente::where('usuario_id', $clienteId)->first();

        if (!$cliente) {
            return redirect()->route('pages.home')->with('error', 'No se ha encontrado el cliente.');
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
        return redirect()->route('order.detail', $pedido->guid);
    }
}
