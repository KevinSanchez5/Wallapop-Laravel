<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Venta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;
use Stripe\Price;
use Stripe\Product;
use Stripe\Refund;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class VentaController extends Controller
{
    public function index(){
        Log::info('Obteniendo todas las ventas');

        $query = Venta::orderBy('id', 'asc');

        $ventas = $query->paginate(5);

        $data = $ventas->getCollection()->transform(function ($venta) {
            return [
                'id' => $venta->id,
                'guid' => $venta->guid,
                'comprador' => $venta->comprador,
                'lineaVentas' => $venta->lineaVentas,
                'precioTotal' => $venta->precioTotal,
                'estado' => $venta->estado,
                'created_at' => $venta->created_at->toDateTimeString(),
                'updated_at' => $venta->updated_at->toDateTimeString(),
            ];
        });

        $customResponse = [
            'ventas' => $data,
            'paginacion' => [
                'pagina_actual' => $ventas->currentPage(),
                'elementos_por_pagina' => $ventas->perPage(),
                'ultima_pagina' => $ventas->lastPage(),
                'elementos_totales' => $ventas->total(),
            ],
        ];

        Log::info('Ventas obtenidas de la base de datos correctamente');
        return response()->json($customResponse);
    }

    public function show($guid)
    {
        Log::info('Obteniendo venta');
        $ventaRedis = Redis::get('venta_'.$guid);

        if ($ventaRedis) {
            Log::info('Venta obtenida desde Redis');
            return response()->json(json_decode($ventaRedis, true));
        }

        Log::info('Buscando venta de la base de datos');
        $venta = Venta::where('guid',$guid)->firstOrFail();

        if (!$venta) {
            return response()->json(['message' => 'Venta no encontrada'], 404);
        }

        $data = [
            'id' => $venta->id,
            'guid' => $venta->guid,
            'comprador' => $venta->comprador,
            'lineaVentas' => $venta->lineaVentas,
            'precioTotal' => $venta->precioTotal,
            'estado' => $venta->estado,
            'created_at' => $venta->created_at->toDateTimeString(),
            'updated_at' => $venta->updated_at->toDateTimeString(),
        ];

        Log::info('Guardando venta en cache redis');
        Redis::set('venta_'. $guid, json_encode($data), 'EX',1800);

        Log::info('Venta obtenida correctamente');
        return response()->json($data);
    }

    public function store(Request $request)
    {
        Log::info('Validando venta');

        $validator = Validator::make($request->all(), [
            'guid' => 'required|string|max:255|unique:ventas',
            'comprador' => 'required|array',
            'lineaVentas' => 'required|array',
            'precioTotal' => 'required|numeric|min:0',
            'estado' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Log::info('Guardando venta en base de datos');

        $venta = Venta::create($request->all());
        Log::info('Venta creada exitosamente');

        return response()->json($venta, 201);
    }

    public function destroy($guid)
    {
        Log::info('Intentando eliminar venta');
        $venta = Redis::get('venta_' . $guid);
        if(!$venta) {
            $venta = Venta::where('guid',$guid)->firstOrFail();
        }

        if(!$venta) {
            return response()->json(['message' => 'Venta no encontrada'], 404);
        }
        Log::info('Eliminando venta de la base de datos');
        $venta->delete();
        Log::info('Eliminando venta de la cache');
        Redis::del('venta_'. $guid);
        Log::info('Venta eliminada correctamente');

        return response()->json(['message' => 'Venta eliminada correctamente']);
    }

    //TODO generar guid mejor
    public function validandoVentaAPartirDeCarrito()
    {
        Log::info('Creando venta a partir de carrito');

        $carrito = session()->get('carrito');
        if (!$carrito || empty($carrito) || empty($carrito->lineasCarrito)) {
            return redirect()->back()->with('error', 'No hay productos en el carrito');
        }
        $guid = uniqid();
        $usuario=Auth::user();

        if (!$usuario){
            return redirect()->back()->with('error', 'No hay usuario registrado');
        }
        $cliente= Cliente::where('usuario_id', $usuario->id)->first();
        if(!$cliente){
            return redirect()->back()->with('error', 'No se encontro cliente registrado');
        }

        $lineasVenta = [];
        foreach($carrito->lineasCarrito as $linea){
            $lineasVenta[] = [
                'vendedor' => [
                    'id' => $linea->producto->vendedor->id,
                    'guid' => $linea->producto->vendedor->guid,
                    'nombre' => $linea->producto->vendedor->nombre,
                    'apellido' => $linea->producto->vendedor->apellido,
                    ],
                'cantidad' => $linea->cantidad,
                'producto' => [
                    'id' => $linea->producto['id'],
                    'guid' => $linea->producto['guid'],
                    'nombre' => $linea->producto['nombre'],
                    'descripcion' => $linea->producto['descripcion'],
                    'estadoFisico' => $linea->producto['estadoFisico'],
                    'precio' => $linea->producto['precio'],
                    'categoria' => $linea->producto['categoria'],
                ],
                'precioTotal' => $linea->precioTotal,
            ];
        }

        $venta = [
            'guid' => $guid,
            'comprador' => [
                'id' => $usuario->id,
                'guid' => $usuario->guid,
                'nombre' => $usuario->name,
                'cliente' =>$cliente->apellido,
            ],
            'lineaVenta' => $lineasVenta,
            'precioTotal' => $carrito->precioTotal,
            'estado' => 'Pendiente',
            'created_at' => now(),
        ];

        $validator = Validator::make($venta, [
            'guid' => 'required|string|max:255|unique:ventas',
            'comprador' => 'required|array',
            'lineaVenta' => 'required|array',
            'precioTotal' => 'required|numeric|min:0'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        Log::info('Validando disponibilidad de productos y precios en base de datos');
        foreach ($venta['lineaVenta'] as $linea){
            $producto = Product::find($linea['producto']['id']);
            if(!$producto || $producto->stock < $linea['cantidad'] ||$producto->estado !== 'disponible') {
                return response()->json(['message' => 'Producto no disponible: ' . $linea['producto']['nombre']], 400);
            }
        }
        Log::info('Carrito validado, se procede al pago de este');
        return $venta;
    }

    public function procesarCompra(){
        Log::info('Iniciando proceso de compra');

        $venta= $this->validandoVentaAPartirDeCarrito();

        if($venta instanceof JsonResponse){
            return $venta;
        }

        $precioStripe = $this->crearPrecioStripe($venta['guid'], $venta['precioTotal']);

        if ($precioStripe instanceof JsonResponse) {
            return $precioStripe; // Retorna si hay errores al crear el precio
        }

        $checkoutSession = $this->crearSesionPago($precioStripe->id);
        if ($checkoutSession instanceof JsonResponse) {
            return $checkoutSession;
        }
        //TODO nuevo campo en caso de reembolso
        //$venta['payment_intent_id'] = $checkoutSession->payment_intent;
        $this->guardarVenta($venta);

        return redirect()->away($checkoutSession->url);
    }

    public function guardarVenta(array $venta)
    {
        Log::info('Creando venta en la base de datos');
        $ventaModel = new Venta($venta);
        $ventaModel->save();
        Log::info('Venta guardada correctamente');
    }


    /**
     * Funcion que inserta un nuevo precio a stripe para permitir el pago exacto
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function crearPrecioStripe(string $guid, float $precioTotal)
    {
        Log::info('Creando venta en  stripe');
        Stripe::setApiKey(env('STRIPE_SECRET'));
        try {
            $productoStripe = Product::create([
                'name' => $guid,
                'description' => $guid . ' venta',
            ]);

            Log::info('Creando precio stripe');
            $precioStripe = Price::create([
                'unit_amount' => $precioTotal * 100,
                'currency' => 'eur',
                'product' => $productoStripe->id,
            ]);

            return $precioStripe;
        } catch (\Exception $e) {
            Log::warning('Fallo al crear precio stripe ' . $e->getMessage() );
            return response()->json(['error' => $e->getMessage()], 500);

        }
    }

    /** Crea una sesion para que stripe pueda gestionar el pago del carrito
     *  y almacenar la nueva venta en la base de datos
     * @param string $precioId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function crearSesionPago(string $precioId)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $OUR_DOMAIN = env('APP_URL');

        try {
            $checkoutSession = Session::create([
                'line_items' => [[
                    'price'=> $precioId,
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

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $event = json_decode($payload);

        if ($event->type === 'checkout.session.completed') {
            $paymentIntentId = $event->data->object->payment_intent;
            $venta = Venta::where('payment_intent_id', $paymentIntentId)->first();

            if ($venta) {
                $venta->estado = 'Pagado';
                $venta->save();
                Log::info('Venta actualizada como pagada. ID: ' . $venta->id);
            }
        }
        return response()->json(['status' => 'success']);
    }


    //TODO posible funcion para reembolsar el pago y cancelar la venta
    // siempre y cuando no alcance un estado diferente a procesado
    public function reembolsarPago($paymentIntentId){
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try{
            $paymentIntentId = PaymentIntent::retrieve($paymentIntentId);
            $chargeId = $paymentIntentId->charges->data[0]->id;

            $reembolso = Refund::create([
                'charge' => $chargeId,
                'amount' => $paymentIntentId->amount,
            ]);
            return response()->json([
                'message'=> 'Reembolso realizado', 'reembolso' => $reembolso
            ]);
        } catch (\Exception $e){
            return response()-> json(['error' => $e->getMessage()]);
        }
    }

}
