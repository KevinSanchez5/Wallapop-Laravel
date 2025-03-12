<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\User;
use App\Models\Producto;
use App\Models\Venta;
use App\Utils\GuidGenerator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Stripe\Charge;
use Stripe\PaymentIntent;
use Stripe\Price;
use Stripe\Product;
use Stripe\Refund;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class VentaController extends Controller
{

    /**
     * Obtiene todas las ventas de la base de datos, con paginación.
     *
     * @return JsonResponse Respuesta JSON con las ventas y la información de paginación.
     */
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
                'paymentIntentId' => $venta->payment_intent_id,
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

    /**
     * Obtiene los detalles de una venta específica a partir de su GUID.
     *
     * @param string $guid El GUID de la venta.
     * @return JsonResponse Respuesta JSON con los detalles de la venta.
     */

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

    /**
     * Crea una nueva venta, validando los datos proporcionados.
     *
     * @param Request $request Los datos de la venta a crear.
     * @return JsonResponse Respuesta JSON con los detalles de la venta creada o errores de validación.
     */

    public function store(Request $request)
    {
        Log::info('Validando venta');

        $validator = Validator::make($request->all(), [
            'comprador' => 'required|array',
            'lineaVentas' => 'required|array',
            'precioTotal' => 'required|numeric|min:0',
            'estado' =>'required|string|in:Pendiente,Procesando,Enviado,Entregado,Cancelado',
            'payment_intent_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Log::info('Guardando venta en base de datos');

        $venta = Venta::create($request->all());
        Log::info('Venta creada exitosamente');

        return response()->json($venta, 201);
    }

    /**
     * Elimina una venta de la base de datos, identificada por su GUID.
     * También elimina la venta de la caché Redis si existe.
     *
     * @param string $guid El GUID de la venta a eliminar.
     * @return JsonResponse Respuesta JSON indicando si la eliminación fue exitosa.
     */

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

    /**
     * Valida los datos del carrito del usuario y prepara la venta para su procesamiento.
     *
     * @return mixed Redirige en caso de error o retorna los datos de la venta para su pago.
     */

    public function validandoVentaAPartirDeCarrito()
    {
        Log::info('Creando venta a partir de carrito');

        $carrito = $this->validarCarrito();
        if (!$carrito) {
            return redirect()->route('payment.error')->with('error', 'No hay productos en el carrito');
        }

        $cliente = $this->validarUsuario();
        if (!$cliente) {
            return redirect()->route('payment.error')->with('error', 'No hay usuario registrado o no se encontró cliente.');
        }

        $lineasVenta = [];
        $precioDeTodo = 0;

        foreach ($carrito->lineasCarrito as $linea) {
            $resultado = $this->validarProducto($linea);
            if ($resultado instanceof RedirectResponse) {
                return $resultado;
            }

            $lineasVenta[] = $resultado['lineaVenta'];
            $precioDeTodo += $resultado['precioLinea'];
        }

        if (empty($lineasVenta)) {
            session()->forget('carrito');
            return redirect()->route('payment.error')->with('error', 'Todos los productos han sido eliminados del carrito.');
        }

        $venta = $this->crearVenta($cliente, $lineasVenta, $precioDeTodo);

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

    private function validarCarrito()
    {
        $carrito = session()->get('carrito');
        return ($carrito && !empty($carrito->lineasCarrito)) ? $carrito : null;
    }

    private function validarUsuario()
    {
        $usuario = Auth::user();
        if (!$usuario) return null;

        return Cliente::where('usuario_id', $usuario->id)->first();
    }

    public function validarProducto($linea)
    {
        $producto = Producto::find($linea->producto->id);
        if (!$producto || $producto->estado !== 'Disponible' || $producto->stock == 0) {
            Log::warning('El producto ya no está disponible: ' . ($producto ? $producto->nombre : 'Producto desconocido'));

            // Eliminar producto del carrito y recalcular precios
            $carrito = session()->get('carrito');
            $carrito->lineasCarrito = array_filter($carrito->lineasCarrito, fn($item) => $item->producto->id !== $linea->producto->id);
            $carrito->precioTotal = array_reduce($carrito->lineasCarrito, fn($carry, $item) => $carry + ($item->producto->precio * $item->cantidad), 0);
            $carrito->itemAmount = array_reduce($carrito->lineasCarrito, fn($carry, $item) => $carry + $item->cantidad);

            session()->put('carrito', $carrito);
            return redirect()->route('payment.error')->with('message', 'El producto ya no está disponible: ' . ($producto ? $producto->nombre : 'Producto desconocido'));
        }

        if ($producto->stock < $linea->cantidad) {
            Log::warning("Producto pedido mayor al stock en venta, se cambia la cantidad a los máximos posibles");
            $linea->cantidad = $producto->stock;
        }

        return [
            'lineaVenta' => [
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
                    'imagenes' => $linea->producto->imagenes,
                ],
                'precioTotal' => $producto->precio * $linea->cantidad,
            ],
            'precioLinea' => $producto->precio * $linea->cantidad
        ];
    }

    public function crearVenta($cliente, $lineasVenta, $precioDeTodo)
    {
        return [
            'guid' => GuidGenerator::generarId(),
            'comprador' => [
                'id' => $cliente->id,
                'guid' => $cliente->guid,
                'nombre' => $cliente->nombre,
                'apellido' => $cliente->apellido,
            ],
            'lineaVentas' => $lineasVenta,
            'precioTotal' => $precioDeTodo,
            'estado' => 'Pendiente',
            'created_at' => now(),
        ];
    }

    /**
     * Procesa la compra, valida el carrito, crea el precio en Stripe y genera la sesión de pago.
     *
     * @return mixed Redirige al usuario a Stripe para procesar el pago o muestra errores si algo falla.
     */

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
    /**
     * Guarda una venta en la base de datos.
     *
     * Este método recibe un arreglo con los datos de la venta y los guarda en la base de datos.
     *
     * @param array $venta Los datos de la venta a guardar.
     * @return void
     */

    public function guardarVenta(array $venta)
    {
        Log::info('Creando venta en la base de datos');
        $ventaModel = new Venta($venta);
        $ventaModel->save();
        Log::info('Venta guardada correctamente');
    }

    public function restandoStockDeProductosComprados($venta)
    {
        Log::info('Restando el stock de los productos comprados');
        foreach($venta['lineaVentas'] as $lineaVenta){
            $producto = Producto::find($lineaVenta['producto']['id']);

            $producto->stock -= $lineaVenta['cantidad'];
            $producto->save();
        }
    }

    /**
     * Procesa el pago exitoso de Stripe y guarda la venta en la base de datos.
     *
     * Este método se ejecuta después de que el pago haya sido completado con éxito en Stripe.
     * Verifica el estado del pago, guarda la venta y limpia la sesión.
     *
     * @param Request $request La solicitud de pago con los datos necesarios.
     * @return \Illuminate\Http\RedirectResponse Redirige al usuario según el resultado del pago.
     */

    public function pagoSuccess(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        Log::info("Pago correcto iniciando el almacenamiento de la venta");

        try {
            $sessionId = $request->query('session_id') ?? session('stripe_session_id');

            if (!$sessionId) {
                Log::error("Error en pagoSuccess: No se encontró session_id");
                return redirect()->route('payment.error')->with('error', 'Error en el pago: Sesión de Stripe no encontrada.');
            }

            $checkoutSession = Session::retrieve($sessionId);
            $paymentIntent = PaymentIntent::retrieve($checkoutSession->payment_intent);

            if ($paymentIntent->status == 'succeeded') {
                $venta = session('venta');
                if(!$venta){
                    return redirect()-> route('pago.error')->with('error', 'Venta no encontrada');
                }
                $venta['payment_intent_id'] = $checkoutSession->payment_intent; // Asignar el payment_intent, campo para el reembolso

                $this->restandoStockDeProductosComprados($venta);
                $this->guardarVenta($venta);

                session()->forget(['carrito','venta','stripe_session_id']);

                return redirect()->route('payment.success');

            } else {
                Log::warning("El pago no se completó correctamente. Estado: " . $paymentIntent->status);
                return redirect()->route('payment.error')->with('error', 'Lo sentimos un error durante el pago de su carrito');
            }
        } catch (\Exception $e) {
            Log::error("Excepción en pagoSuccess: " . $e->getMessage());
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

    public function cancelarVenta($guid)
    {
        Log::info('Cancelando venta con guid: ' . $guid);

        $venta = Venta::where('guid', $guid)->first();

        if(!$venta){
            Log::warning('Venta no encontrada');
            return redirect()->route('profile')->with('error', 'Venta no encontrada');
        }

        Log::info('Buscando el perfil del cliente en la base de datos y comprobando si le pertenece la venta');
        $cliente = $this->validarUsuario();

        if ($cliente->guid !== $venta->comprador->guid) {
            Log::warning('Usuario no autorizado para cancelar la venta', ['guid' => $guid]);
            return redirect()->route('profile')->with('error', 'No tienes permiso para cancelar esta venta');
        }

        if($venta['estado'] == 'Pendiente'){
            $reembolsoResult = $this->reembolsarPago($venta['payment_intent_id'], $venta['precioTotal']);
            if($reembolsoResult['status'] != 'success') {
                Log::error("Reembolso fallido, cancelación no realizada.");
                return redirect()->route('profile')->with('error', 'No se pudo realizar el reembolso. La venta no ha sido cancelada.');
            }
            $this->devolviendoProductos($venta);
            $venta->update(['estado' => 'Cancelado']);
            $venta->save();
            return redirect()->route('profile')->with('success', 'Venta Cancelada');
        } else {
            Log::warning('No se puede cancelar la venta ya que su estado no es Pendiente');
            //Probablemente cambiar la ruta?
            return redirect()->route('payment.error')->with('error', 'No se puede cancelar la venta ya que el estado de su compra es: ' . $venta['estado']);
        }
    }

    public function devolviendoProductos($venta){
        Log::info('Devolviendo al stock los productos de venta que se quiere cancelar');
        foreach($venta['lineaVentas'] as $lineaVenta){
            $producto = Producto::find($lineaVenta['producto']['id']);

            $producto->stock += $lineaVenta['cantidad'];
            $producto->save();
        }
    }
    /**
     * Reembolsa un pago realizado a través de Stripe.
     *
     * Este método procesa un reembolso si el pago aún no ha sido procesado.
     *
     * @param string $paymentIntentId El ID del PaymentIntent que se desea reembolsar.
     * @return \Illuminate\Http\JsonResponse El resultado del reembolso o un error si ocurre.
     */
    public function reembolsarPago($paymentIntentId, $coste){
        Stripe::setApiKey(env('STRIPE_SECRET'));
        Log::info('Reembolsando pago en stripe');
        try{
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            $chargeId = $paymentIntent->latest_charge;
            $charge = Charge::retrieve($chargeId);
                if ($paymentIntent->status != 'succeeded' || $charge->refunded==true) {
                Log::error("Intento de reembolso fallido: ya se ha reembolsado ese pago " . $paymentIntentId);
                return [ 'status'=> 'error','message' => 'Esa venta no ha sido pagada o ya ha sido reembolsada'];
            }

            $reembolso = Refund::create([
                'charge' => $chargeId,
                'amount' => $coste*100,
            ]);
            return [
                'status' => 'success',
                'message'=> 'Reembolso realizado',
                'reembolso' => $reembolso
            ];
        } catch (\Exception $e){
            Log::error("Error en reembolsarPago: " . $e->getMessage());
            return ['status' => 'error',  'message' => $e->getMessage()];
        }
    }

    /**
     * Genera un archivo PDF con los detalles de la venta.
     *
     * Este método obtiene los datos de la venta (desde Redis o la base de datos) y genera un PDF.
     *
     * @param string $guid El GUID de la venta para generar el PDF.
     * @return \Illuminate\Http\Response El archivo PDF generado.
     */

    public function generatePdf($guid)
    {
        Log::info('Generando PDF de la venta');
        $ventaRedis = Redis::get('venta_'.$guid);

        if ($ventaRedis) {
            Log::info('Venta obtenida desde Redis');
            $venta = json_decode($ventaRedis);
        } else {
            Log::info('Buscando venta de la base de datos');
            $venta = Venta::where('guid', $guid)->first();

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
        }

        $pdf = Pdf::loadView('pdf.venta', compact('venta'));

        return $pdf->stream('venta.pdf');
    }

    public function updateVentaEstado(Request $request, $guid)
    {
        $request->validate([
            'estado' => 'required|in:Pendiente,Procesando,Enviado,Entregado,Cancelado'
        ]);

        $venta = Venta::where('guid', $guid)->firstOrFail();

        $venta->estado = $request->estado;
        $venta->save();

        return redirect()->route('admin.sells')->with('success', 'Estado de la venta actualizado con éxito');
    }

    public function deleteVentaAdmin($guid)
    {
        // Buscar la venta por su GUID
        $venta = Venta::where('guid', $guid)->firstOrFail();

        $this->reembolsarPago($venta->payment_intent_id, $venta->precioTotal);
        // Eliminar la venta
        $venta->estado = 'Cancelado';
        $venta->save();

        // Redirigir con un mensaje de éxito
        return redirect()->route('admin.sells')->with('success', 'Venta eliminada con éxito');
    }

}
