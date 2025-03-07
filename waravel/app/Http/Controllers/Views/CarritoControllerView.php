<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Models\Carrito;
use App\Models\Cliente;
use App\Models\LineaCarrito;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
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
            return response()->json
            ([
                "message" => "No se han proporcionado los campos necesarios",
                "status" => 400
            ]);
        }

        $productId = $request->input('productId');

        Log::info('Buscando el carrito en la sesión');
        $cart = session()->get('carrito', new Carrito([
            'lineasCarrito' => [],
            'precioTotal' => 0,
            'itemAmount' => 0
        ]));

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
        return response()->json([
            "message" => "No se ha podido eliminar el producto del carrito",
            "status" => 404
            ]
        );
    }

    public function deleteOneFromCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'productId' => 'required'
        ]);

        if ($validator->fails()) {
            Log::warning('No se han proporcionado los campos necesarios');
            return response()->json
            ([
                "message" => "No se han proporcionado los campos necesarios",
                "status" => 400
            ]);
        }

        $productId = $request->input('productId');

        Log::info('Buscando el carrito en la sesión');
        $cart = session()->get('carrito', new Carrito([
            'lineasCarrito' => [],
            'precioTotal' => 0,
            'itemAmount' => 0
        ]));

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
        return response()->json([
            "message" => "No se ha encontrado el producto en el carrito, por favor recargue la página",
            "status" => 404
            ]
        );
    }

    public function addOneToCart(Request $request){
        $validator = Validator::make($request->all(), [
            'productId' => 'required'
        ]);

        if ($validator->fails()) {
            Log::warning('No se han proporcionado los campos necesarios');
            return response()->json([
                "message" => "No se han proporcionado los campos necesarios",
                "status" => 400
            ]);
        }

        $productId = $request->input('productId');

        Log::info('Buscando el carrito en la sesión');
        $cart = session()->get('carrito', new Carrito([
            'lineasCarrito' => [],
            'precioTotal' => 0,
            'itemAmount' => 0
        ]));

        $lineas = $cart->lineasCarrito;

        $found = false;
        $lineaPrice = 0;

        foreach ($lineas as &$linea) {
            if ($linea->producto->guid == $productId) {
                Log::info("Producto encontrado en el carrito, aumentando la cantidad");
                if ($linea->cantidad + 1 > $linea->producto->stock){
                    Log::warning("El producto no tiene stock suficiente para agregar más");
                    return response()->json([
                        "status" => 400,
                        "message" => "No hay stock suficiente para agregar más productos"
                    ]);
                }
                $cart->precioTotal += $linea->producto->precio;
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
        return response()->json([
                "message" => "No se ha encontrado el producto en el carrito, por favor recargue la página",
                "status" => 404
            ]
        );
    }


    public function addToCartOrEditSetProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'productId' => 'required',
            'amount'  => 'required|numeric',
        ]);


        if ($validator->fails()) {
            Log::warning('No se han proporcionado los campos necesarios');
            return response()->json([
                "message" => "No se han proporcionado los campos necesarios",
                "status" => 400
            ]);
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

        Log::info('Buscando el producto en la BBDD');
        $producto = Producto::where('guid', $productId)->first();

        if ($producto == null) {
            Log::warning("Producto no encontrado en la BBDD");
            return response()->json([
                "message" => "No se ha encontrado el producto, por favor recargue la página",
                "status" => 404
            ]);
        }

        $possibleError = $this->verifyProductDoesntBelongToCurrentUser($producto);

        if ($possibleError ) {
            return $possibleError;
        };

        Log::info('Verificando que el producto tiene suficiente stock');
        if ($amount > $producto->stock) {
            Log::warning("El producto no tiene stock suficiente para agregar más");
            return response()->json([
                "message" => "No hay stock suficiente para agregar más productos",
                "status" => 400
            ]);
        }

        $lineas = $cart->lineasCarrito;
        $found = false;
        $lineaPrice = 0;

        foreach ($lineas as &$linea) {
            if ($linea->producto->id == $producto->id) {
                Log::info("Producto encontrado en el carrito, editándolo");
                if ($linea->cantidad + $amount > $linea->producto->stock){
                    Log::warning("El producto no tiene stock suficiente para agregar más");
                    return response()->json([
                        "message" => "No hay stock suficiente para agregar más productos",
                        "status" => 400
                    ]);
                }
                $cart->precioTotal -= $linea->precioTotal;
                $linea->cantidad += $amount;
                $linea->precioTotal = $linea->cantidad * $linea->producto->precio;
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


        Log::info('Buscando el perfil del cliente en la base de datos');
        $cliente = Cliente::where('usuario_id', $usuario->id)->first();

        if ($cliente == null) {
            Log::warning("El cliente no se ha encontrado en la base de datos");
            return redirect()->route('carrito')->with('error', 'No se ha encontrado un cliente asociado al usuario');
        }

        Log::info('Devolviendo la vista con el carrito');
        return view('pages.orderSummary', compact('cart', 'cliente', 'usuario'));
    }

    public function verifyProductDoesntBelongToCurrentUser($producto){
        Log::info('Verificando el producto no le pertence al cliente que ha iniciado sesión');

        $user = Auth::user();

        if ($user == null) {
            return false;
        }else  $cliente = Cliente::where('usuario_id', $user->id)->first();

        if ($cliente->id == $producto->vendedor_id) {
            Log::warning('El producto le pertenece al cliente que ha iniciado sesión');
            return response()->json([
               'message' => 'No puedes añadir tus propios productos al carrito',
               'status' => 400
            ]);
        }

        return false;
    }
}
