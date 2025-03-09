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

    /**
     * Muestra el carrito de compras.
     *
     * Este método busca el carrito de compras almacenado en la sesión y lo pasa a la vista correspondiente.
     * Si no existe un carrito en la sesión, se crea uno por defecto.
     *
     * @return \Illuminate\View\View Vista con el carrito de compras.
     */
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

    /**
     * Elimina un producto del carrito de compras.
     *
     * Este método elimina el producto cuyo ID es enviado en la solicitud del carrito de compras.
     * Si el producto es encontrado, se actualiza el carrito y se devuelve una respuesta con el carrito actualizado.
     * Si no se encuentra el producto, se devuelve un error.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud, que incluye el ID del producto.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el estado de la operación y los datos del carrito actualizado.
     */

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

    /**
     * Elimina una unidad de un producto del carrito de compras.
     *
     * Este método disminuye la cantidad de un producto en el carrito o lo elimina si su cantidad llega a 0.
     * Si se encuentra el producto, se actualiza el carrito y se devuelve una respuesta con los datos actualizados.
     * Si el producto no se encuentra, se devuelve un error.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud, que incluye el ID del producto.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el estado de la operación y los datos del carrito actualizado.
     */

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

    /**
     * Añade una unidad de un producto al carrito de compras.
     *
     * Este método aumenta la cantidad de un producto en el carrito.
     * Si no hay suficiente stock, se devuelve un error.
     * Si se encuentra el producto, se actualiza el carrito y se devuelve una respuesta con los datos actualizados.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud, que incluye el ID del producto.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el estado de la operación y los datos del carrito actualizado.
     */

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

    /**
     * Añade un producto al carrito o edita un producto ya existente.
     *
     * Este método permite agregar un producto al carrito o modificar su cantidad, validando que haya stock suficiente y que el producto no le pertenezca al usuario autenticado.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud, que incluye el ID del producto y la cantidad.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el estado de la operación y los datos del carrito actualizado.
     */


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

    /**
     * Muestra la vista de resumen del pedido.
     *
     * Este método recupera el carrito de compras y los detalles del cliente asociado al usuario autenticado, para mostrar la vista de resumen del pedido.
     *
     * @return \Illuminate\View\View Vista con el resumen del pedido y los detalles del cliente.
     */

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

        if ($cart->lineasCarrito == []){
            Log::warning('No hay productos en el carrito');
            return redirect()->route('carrito')->with('error', 'Añada productos al carrito antes de realizar una compra');
        }
        Log::info('Devolviendo la vista con el carrito');
        return view('pages.orderSummary', compact('cart', 'cliente', 'usuario'));
    }

    /**
     * Verifica si un producto pertenece al usuario que ha iniciado sesión.
     *
     * Este método verifica si el producto añadido al carrito pertenece al usuario autenticado.
     * Si el producto es del usuario, se devuelve un error.
     *
     * @param \App\Models\Producto $producto Producto a verificar.
     * @return \Illuminate\Http\JsonResponse|bool Respuesta JSON con el error si el producto pertenece al usuario, o false si no.
     */

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
