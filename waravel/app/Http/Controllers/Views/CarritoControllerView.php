<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Models\Carrito;
use App\Models\LineaCarrito;
use App\Models\Producto;
use Illuminate\Http\Request;

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
        $producto = response();
        $carrito = session('carrito', new Carrito([
            'guid' => uniqid(),
            'cliente' => null,
            'lineasCarrito' => [],
            'precioTotal' => 0
        ]));

        foreach ($carrito->lineasCarrito as $key => &$linea) {
            if ($linea['producto_id'] == $producto->id) {
                unset($carrito->lineasCarrito[$key]);
                break;
            }
        }

        $carrito->lineasCarrito = array_values($carrito->lineasCarrito);

        session(['carrito' => $carrito]);

        return redirect()->back();
    }


    public function addToCartOrEditSetProduct(Request $request)
    {
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
