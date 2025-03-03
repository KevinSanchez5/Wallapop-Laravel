<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Utils\GuidGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
                'estado' => $venta->estado,
                'comprador' => $venta->comprador,
                'lineaVentas' => $venta->lineaVentas,
                'precioTotal' => $venta->precioTotal,
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
        $venta = Venta::where('guid',$guid)->first();

        if (!$venta) {
            return response()->json(['message' => 'Venta no encontrada'], 404);
        }

        $data = [
            'id' => $venta->id,
            'guid' => $venta->guid,
            'estado' => $venta->estado,
            'comprador' => $venta->comprador,
            'lineaVentas' => $venta->lineaVentas,
            'precioTotal' => $venta->precioTotal,
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
            'estado' =>'required|string|max:255',
            'comprador' => 'required|array',
            'lineaVentas' => 'required|array',
            'precioTotal' => 'required|numeric|min:0',
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
        $venta = Venta::where('guid',$guid)->first();

        if(!$venta) {
            Log::warning('Venta no encontrada: ' . $guid);
            return response()->json(['message' => 'Venta no encontrada'], 404);
        }
        if(Redis::exists('venta_' . $guid)){
            Log::info('Eliminando venta de la cache');
            Redis::del('venta_'. $guid);
        }
        Log::info('Eliminando venta de la base de datos');
        $venta->delete();

        Log::info('Venta eliminada correctamente');

        return response()->json(['message' => 'Venta eliminada correctamente']);
    }

    public function validandoVentaAPartirDeCarrito()
    {
        Log::info('Creando venta a partir de carrito');

        $carrito = session()->get('carrito');
        if (!$carrito || empty($carrito) || empty($carrito->lineasCarrito)) {
            return redirect()->route('payment.error')->with('error', 'No hay productos en el carrito');
        }
        $usuario=Auth::user();

        if (!$usuario){
            return redirect()->route('payment.error')->with('error', 'No hay usuario registrado');
        }
        $cliente= Cliente::where('usuario_id', $usuario->id)->first();
        if(!$cliente){
            return redirect()->route('payment.error')->with('error', 'No se encontro cliente registrado');
        }

        Log::info('Creando lineas de venta a partir de lineas de carrito');
        $lineasVenta = [];
        $precioDeTodo = 0;
        $productosDisponibles = [];
        foreach($carrito->lineasCarrito as $linea){
            Log::info('Validando disponibilidad de productos y precios en base de datos');
            $producto = Producto::find($linea->producto->id);
            if(!$producto || $producto->stock < $linea->cantidad ||$producto->estado !== 'Disponible') {
                Log::warning('El producto ya no está disponible: ' . ($producto ? $producto->nombre : 'Producto desconocido'));
                $productosDisponibles = array_filter($carrito->lineasCarrito, function ($item) use ($linea) {
                    return $item->producto->id !== $linea->producto->id;
                });
                $nuevoPrecioTotal = array_reduce($productosDisponibles, function ($carry, $item) {
                    return $carry + ($item->producto->precio * $item->cantidad);
                }, 0);
                $nuevaCantidad = array_reduce($productosDisponibles, function ($carry, $item) {
                    return $carry + $item->cantidad;
                });
                session()->put('carrito', (object)['lineasCarrito' => $productosDisponibles,
                    'precioTotal' => $nuevoPrecioTotal,
                    'itemAmount' => $nuevaCantidad]);
                return redirect()->route('payment.error')->with('message', 'El producto ya no está disponible: '.  ($producto ? $producto->nombre : 'Producto desconocido'));
            }
            $precioLinea = $producto->precio * $linea->cantidad;
            $precioDeTodo+= $precioLinea;
            $lineasVenta[] = [
                'vendedor' => [
                    'id' => $linea->producto->vendedor->id,
                    'guid' => $linea->producto->vendedor->guid,
                    'nombre' => $linea->producto->vendedor->nombre,
                    'apellido' => $linea->producto->vendedor->apellido,
                    ],
                'cantidad' => $linea->cantidad,
                'producto' => [
                    'id' => $linea->producto->id,
                    'guid' => $linea->producto->guid,
                    'nombre' => $linea->producto->nombre,
                    'descripcion' => $linea->producto->descripcion,
                    'estadoFisico' => $linea->producto->estadoFisico,
                    'precio' => $linea->producto->precio,
                    'categoria' => $linea->producto->categoria,
                ],
                'precioTotal' => $precioLinea,
            ];
        }
        if (empty($lineasVenta)) {
            session()->forget('carrito');
            return redirect()->route('payment.error')->with('error', 'Todos los productos han sido eliminados del carrito.');
        }

        $venta = [
            'guid' => GuidGenerator::generarId(),
            'comprador' => [
                'id' => $usuario->id,
                'guid' => $usuario->guid,
                'nombre' => $cliente->nombre,
                'apellido' =>$cliente->apellido,
            ],
            'lineaVentas' => $lineasVenta,
            'precioTotal' => $precioDeTodo,
            'estado' => 'Pendiente',
            'created_at' => now(),
        ];

        $validator = Validator::make($venta, [
            'guid' => 'required|string|max:255|unique:ventas',
            'comprador' => 'required|array',
            'lineaVentas' => 'required|array',
            'precioTotal' => 'required|numeric|min:0',
            'estado' => 'required|string|max:25',
        ]);
        if ($validator->fails()) {
            return redirect()->route('payment.error')->with('error', 'Hubo un error en los datos de la venta.');
        }


        Log::info('Carrito validado, se procede al pago de este');
        return $venta;
    }

    public function procesarCompra(){
        Log::info('Iniciando proceso de compra');

        $venta= $this->validandoVentaAPartirDeCarrito();

        if($venta instanceof RedirectResponse){
            return $venta;// Retorna si hay errores al crear la venta
        }
        session(['venta' => $venta]);
        $precioStripe = $this->crearPrecioStripe($venta['guid'], $venta['precioTotal']);

        if ($precioStripe instanceof JsonResponse) {
            return $precioStripe; // Retorna si hay errores al crear el precio
        }

        $checkoutSession = $this->crearSesionPago($precioStripe->id);
        if ($checkoutSession instanceof JsonResponse) {
            return $checkoutSession;
        }

        return redirect()->away($checkoutSession->url);
    }

    public function guardarVenta(array $venta)
    {
        Log::info('Creando venta en la base de datos');
        $ventaModel = new Venta($venta);
        $ventaModel->save();
        Log::info('Venta guardada correctamente');
    }

    public function pagoSuccess(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        Log::info("Pago correcto iniciando el almacenamiento de la venta");

        try {
            $sessionId = $request->query('session_id') ?? session('stripe_session_id');
            $checkoutSession = Session::retrieve($sessionId);

            // Verificar el estado del pago (payment_intent)
            $paymentIntent = PaymentIntent::retrieve($checkoutSession->payment_intent);

            if ($paymentIntent->status == 'succeeded') {
                $venta = session('venta');
                if(!$venta){
                    return redirect()-> route('pago.error')->with('error', 'Venta no encontrada');
                }
                //TODO nuevo campo en caso de reembolso
                //$venta['payment_intent_id'] = $checkoutSession->payment_intent;
                $venta['payment_intent_id'] = $checkoutSession->payment_intent; // Asignar el payment_intent

                $this->guardarVenta($venta);

                session()->forget('carrito'); // Eliminar el carrito de la sesión
                session()->forget('venta');   // Eliminar la venta de la sesión
                session()->forget('stripe_session_id');

                return redirect()->route('payment.success'); // Ruta donde el usuario ve el mensaje de éxito

            } else {

                return redirect()->route('payment.error'); // Ruta para error de pago
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
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
        Log::info('Creando sesion para el pago en stripe');
        try {
            $checkoutSession = Session::create([
                'line_items' => [[
                    'price'=> $precioId,
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $OUR_DOMAIN . '/pago/save',
                'cancel_url' => $OUR_DOMAIN . '/pago/cancelled',
            ]);

            Log::info('Usuario redirigido y pagando en stripe');
            session(['stripe_session_id' => $checkoutSession->id]);
            return $checkoutSession;

        } catch (\Exception $e){
            Log::warning('Fallo al crear pago en stripe');
            return response()-> json(['error' => $e->getMessage()]);
        }
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
