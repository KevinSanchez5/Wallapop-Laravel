<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PagoController extends Controller
{
    /*
     * TODO
     * Añadir desde el carrito los productos a comprar
     * y ajustar precios cantidades ...
     */
    public function crearSesionPago(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $OUR_DOMAIN = env('APP_URL');

        try {
            $checkoutSession = Session::create([
                'line_items' => [[
                    //recuperar los datos de los articulos y precio desde el request
                    'price'=> 'price_1QuYJo7AuwO8CXRNNnR51KKo',
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $OUR_DOMAIN . '/pago/success',
                'cancel_url' => $OUR_DOMAIN . '/pago/cancelled',
            ]);

            return redirect()->away($checkoutSession->url);

        } catch (\Exception $e){
            return response()-> json(['error' => $e->getMessage()]);
        }
    }
}
