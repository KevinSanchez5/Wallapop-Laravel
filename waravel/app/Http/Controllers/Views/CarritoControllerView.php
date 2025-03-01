<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Models\Carrito;
use App\Models\Cliente;
use App\Models\User;
use App\Models\LineaCarrito;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CarritoControllerView extends Controller
{
    public function showCart()
    {
        Log::info('Buscando el carrito en la sesión');
        $cart = session()->get('carrito', new Carrito([
            'lineasCarrito' => [],
            'precioTotal' => 0
        ]));

        Log::info('Devolviendo la vista con el carrito');
        return view('pages.shoppingCart', compact('cart'));
    }

    public function removeFromCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'productId' => 'required'
        ]);

        if ($validator->fails()) {
            Log::warning('No se han proporcionado los campos necesarios');
            return response()->json($validator->errors(), 400);
        }

        $productId = $request->input('productId');

        Log::info('Buscando el carrito en la sesión');
        $cart = session()->get('carrito', new Carrito([
            'lineasCarrito' => [],
            'precioTotal' => 0,
            'itemAmount' => 0
        ]));

        if ($cart->lineasCarrito == null) {
            Log::warning("No hay productos en el carrito");
            return response()->json($validator->errors(), 404);
        }

        $lineas = $cart->lineasCarrito;
        $found = false;

        foreach ($lineas as $key => &$linea) {
            if ($linea->producto->guid == $productId) {
                Log::info("Producto encontrado en el carrito, editándolo");
                $cart->precioTotal -= $linea->precioTotal;
                $cart->itemAmount -= $linea->cantidad;
                unset($lineas[$key]);
                $found = true;
                break;
            }
        }

        if ($found) {
            Log::info("Producto no encontrado en el carrito, añadíendolo");
            $cart->lineasCarrito = array_values($lineas);
            session()->put('carrito', $cart);
            return response()->json( [
                "precioTotal" => $cart->precioTotal,
                "itemAmount" => $cart->itemAmount,
                "lineaPrice" => 0,
                "status" => 200
            ]);
        }

        Log::warning("No se ha podido eliminar el producto del carrito");
        return response()->json("Error", 404);
    }

    public function deleteOneFromCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'productId' => 'required'
        ]);

        if ($validator->fails()) {
            Log::warning('No se han proporcionado los campos necesarios');
            return response()->json($validator->errors(), 400);
        }

        $productId = $request->input('productId');

        Log::info('Buscando el carrito en la sesión');
        $cart = session()->get('carrito', new Carrito([
            'lineasCarrito' => [],
            'precioTotal' => 0
        ]));

        if ($cart->lineasCarrito == null) {
            Log::warning("No hay productos en el carrito");
            return response()->json($validator->errors(), 404);
        }

        $lineas = $cart->lineasCarrito;
        $found = false;
        $deletedTheItem = false;
        $lineaPrice = 0;

        foreach ($lineas as $key => &$linea) {
            if ($linea->producto->guid == $productId) {
                if ($linea->cantidad == 1){
                    Log::info("Producto encontrado en el carrito, eliminándolo");
                    $cart->precioTotal -= $linea->precioTotal;
                    unset($lineas[$key]);
                    $found = true;
                    $deletedTheItem = true;
                    break;
                }else{
                    Log::info("Producto encontrado en el carrito, disminuyendo la cantidad");
                    $cart->precioTotal -= $linea->producto->precio;
                    $linea->cantidad -= 1;
                    $linea->precioTotal -= $linea->producto->precio;
                    $lineaPrice = $linea->precioTotal;
                    $found = true;
                    break;
                }
            }
        }

        session(['carrito' => $cart]);

        if ($found) {
            $cart->lineasCarrito = array_values($lineas);
            $cart->itemAmount -= 1;
            Log::info('Actualizando el carrito en la sesión');
            session()->put('carrito', $cart);
            return response()->json( [
                "precioTotal" => $cart->precioTotal,
                "itemAmount" => $cart->itemAmount,
                "lineaPrice" => $lineaPrice,
                "deletedTheItem" => $deletedTheItem,
                "status" => 200
            ]);
        }

        Log::warning("No se ha podido eliminar el producto del carrito");
        return response()->json("Error", 404);
    }

    public function addOneToCart(Request $request){
        $validator = Validator::make($request->all(), [
            'productId' => 'required'
        ]);

        if ($validator->fails()) {
            Log::warning('No se han proporcionado los campos necesarios');
            return response()->json($validator->errors(), 400);
        }

        $productId = $request->input('productId');

        Log::info('Buscando el carrito en la sesión');
        $cart = session()->get('carrito', new Carrito([
            'lineasCarrito' => [],
            'precioTotal' => 0,
            'itemAmount' => 0
        ]));

        if ($cart->lineasCarrito == null) {
            Log::warning("No hay productos en el carrito");
            return response()->json($validator->errors(), 404);
        }

        $lineas = $cart->lineasCarrito;
        $found = false;
        $lineaPrice = 0;

        foreach ($lineas as &$linea) {
            if ($linea->producto->guid == $productId) {
                Log::info("Producto encontrado en el carrito, aumentando la cantidad");
                $cart->precioTotal += $linea->producto->precio;
                if ($linea->cantidad + 1 > $linea->producto->stock){
                    Log::warning("El producto no tiene stock suficiente para agregar más");
                    return response()->json("No hay stock suficiente para agregar más productos", 400);
                }
                $linea->cantidad += 1;
                $linea->precioTotal += $linea->producto->precio;
                $lineaPrice = $linea->precioTotal;
                $found = true;
                break;
            }
        }

        if ($found) {
            $cart->lineasCarrito = array_values($lineas);
            $cart->itemAmount += 1;
            Log::info('Actualizando el carrito en la sesión');
            session()->put('carrito', $cart);
            return response()->json( [
                "precioTotal" => $cart->precioTotal,
                "itemAmount" => $cart->itemAmount,
                "lineaPrice" => $lineaPrice,
                "status" => 200
            ]);
        }

        Log::warning("No se ha podido eliminar el producto del carrito");
        return response()->json("Error", 404);
    }


    public function addToCartOrEditSetProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'productId' => 'required',
            'amount'  => 'required|numeric',
        ]);


        if ($validator->fails()) {
            Log::warning('No se han proporcionado los campos necesarios');
            return response()->json($validator->errors(), 400);
        }

        Log::info('Buscando el carrito en la sesión');
        $cart = session('carrito', new Carrito([
            'lineasCarrito' => [],
            'precioTotal' => 0,
            'itemAmount' => 0
        ]));

        if ($cart->lineasCarrito == null) {
            $cart->lineasCarrito = [];
            $cart->precioTotal = 0;
        }

        $productId = $request->get('productId');
        $amount = $request->get('amount');
        $producto = Producto::where('guid', $productId)->firstOrFail();
        $lineas = $cart->lineasCarrito;
        $found = false;
        $lineaPrice = 0;

        foreach ($lineas as &$linea) {
            if ($linea->producto->id == $producto->id) {
                Log::info("Producto encontrado en el carrito, editándolo");
                $cart->precioTotal -= $linea->precioTotal;
                if ($linea->cantidad + $amount > $linea->producto->stock){
                    Log::warning("El producto no tiene stock suficiente para agregar más");
                    return response()->json("No hay stock suficiente para agregar más productos", 400);
                }
                $linea->cantidad += $amount;
                $linea->precioTotal = $linea->cantidad * $producto->precio;
                $lineaPrice = $linea->precioTotal;
                $cart->precioTotal += $linea->precioTotal;
                $found = true;
                break;
            }
        }

        if (!$found) {
            Log::info("Producto no encontrado en el carrito, añadíendolo");
            $lineas[] = new LineaCarrito([
                'producto' => $producto,
                'cantidad' => $amount,
                'precioTotal' => $producto->precio * $amount
            ]);

            $cart->precioTotal += $producto->precio * $amount;
        }

        $cart->lineasCarrito = $lineas;
        $cart->itemAmount += $amount;
        Log::info('Actualizando el carrito en la sesión');
        session(['carrito' => $cart]);

        return response()->json( [
            "precioTotal" => $cart->precioTotal,
            "itemAmount" => $cart->itemAmount,
            "lineaPrice" => $lineaPrice,
            "status" => 200
        ]);
    }

    public function showOrder()
    {
        Log::info('Buscando el carrito en la sesión');
        $cart = session()->get('carrito', new Carrito([
            'lineasCarrito' => [],
            'precioTotal' => 0
        ]));

        Log::info('Autenticando usuario');
        $usuario = Auth::user();

        if (!$usuario) {
            Log::warning('Usuario no autenticado intentó acceder al resumen de pedido');
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tu pedido.');
        }

        Log::info('Buscando el perfil del cliente en la base de datos');
        $cliente = $usuario->cliente;

        if (!$cliente) {
            Log::warning("El usuario {$usuario->id} no tiene un perfil de cliente asociado.");
            return redirect()->route('home')->with('error', 'No tienes un perfil de cliente asociado.');
        }

        Log::info('Devolviendo la vista con el carrito');
        return view('pages.orderSummary', compact('cart', 'cliente', 'usuario'));
    }
}
