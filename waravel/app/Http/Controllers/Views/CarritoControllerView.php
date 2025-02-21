<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Models\Carrito;
use App\Models\LineaCarrito;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarritoControllerView extends Controller
{
    public function showCart()
    {
        $cart = session()->get('carrito', new Carrito([
            'lineasCarrito' => [],
            'precioTotal' => 0
        ]));
        return view('pages.shoppingCart', compact('cart'));
    }

    public function removeFromCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'productId' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $productId = $request->input('productId');

        $cart = session()->get('carrito', new Carrito([
            'lineasCarrito' => [],
            'precioTotal' => 0
        ]));

        if ($cart->lineasCarrito == null) {
            return response()->json($validator->errors(), 404);
        }

        $lineas = $cart->lineasCarrito;
        $found = false;

        foreach ($lineas as $key => &$linea) {
            if ($linea->producto->id == $productId) {
                $cart->precioTotal -= $linea->precioTotal;
                \Illuminate\Log\log('found');
                unset($lineas[$key]);
                $found = true;
                break;
            }
        }

        if ($found) {
            $cart->lineasCarrito = array_values($lineas);
            session()->put('carrito', $cart);
            return response()->json("Success");
        }

        return response()->json("Error", 404);
    }


    public function addToCartOrEditSetProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'producto' => 'required',
            'amount'  => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $cart = session('carrito', new Carrito([
            'lineasCarrito' => [],
            'precioTotal' => 0
        ]));

        if ($cart->lineasCarrito == null) {
            $cart->lineasCarrito = [];
            $cart->precioTotal = 0;
        }

        $producto = new Producto();
        $producto->fill($request->input('producto'));
        $producto->id = $request->input('producto.id');
        $amount = $request->get('amount');
        $lineas = $cart->lineasCarrito;
        $found = false;

        foreach ($lineas as &$linea) {
            if ($linea->producto->id == $producto->id) {
                $cart->precioTotal -= $linea->precioTotal;
                $linea->cantidad += $amount;
                $linea->precioTotal = $linea->cantidad * (float)$producto->precio;
                $cart->precioTotal += $linea->precioTotal;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $lineas[] = new LineaCarrito([
                'producto' => $producto,
                'cantidad' => $amount,
                'precioTotal' => $producto->precio * $amount
            ]);

            $cart->precioTotal += $producto->precio * $amount;
        }

        $cart->lineasCarrito = $lineas;
        session(['carrito' => $cart]);

        return response()->json("success");
    }
}
