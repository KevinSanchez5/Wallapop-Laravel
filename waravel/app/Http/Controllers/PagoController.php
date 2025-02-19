<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\StripeClient;

class PagoController extends Controller
{
    /*
     * TODO
     * Añadir desde el carrito los productos a comprar
     * y ajustar precios cantidades ...
     */
    public function crearSesionPago(Request $request)
    {
        $stripeSecretKey = env('STRIPE_SECRET');
        $stripe = new StripeClient($stripeSecretKey);

        $OUR_DOMAIN = env('APP_URL');

        try {
            $checkoutSession = $stripe->checkout->sessions->create([
                'ui_mode' => 'embedded',
                'line_items' => [[
                    'price'=> 1,
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'return_url'=> $OUR_DOMAIN . '/return.html?session_id={CHECKOUT_SESSION_ID}',
            ]);

            return response()-> json(['clientSecret' => $checkoutSession->client_secret]);

        } catch (\Exception $e){
            return response()-> json(['error' => $e->getMessage()]);
        }
    }
}
