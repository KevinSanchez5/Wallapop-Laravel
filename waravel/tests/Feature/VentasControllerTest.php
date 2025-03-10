<?php

namespace Tests\Feature;

use App\Http\Controllers\VentaController;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use App\Utils\GuidGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Stripe\Charge;
use Stripe\PaymentIntent;
use Stripe\Price;
use Stripe\Refund;
use Stripe\Stripe;
use Stripe\Product as StripeProduct;
use Stripe\Checkout\Session as StripeSession;
use Tests\TestCase;
use Mockery;

class VentasControllerTest extends TestCase
{
    use RefreshDatabase;

    private $controller;

    private User $usuario1;
    private User $usuario2;
    private Cliente $vendedor;
    private Cliente $comprador;
    private Producto $producto;

    protected function setUp(): void
    {
        parent::setUp();
        Redis::isFake();

        $this->usuario1 = User::factory()->create(['name' => 'Juan']);
        $this->usuario2 = User::factory()->create(['name' => 'Pedro']);

        $this->vendedor = Cliente::factory()->create([
            'nombre' => 'Vendedor',
            'usuario_id' => $this->usuario1->id,
        ]);

        $this->comprador = Cliente::factory()->create([
            'nombre' => 'Comprador',
            'usuario_id' => $this->usuario2->id,
        ]);

        $this->producto = Producto::factory()->create([
            'nombre' => 'Producto',
            'vendedor_id' => $this->vendedor->id,
            'precio' => 970.03,
            'stock' => 100,
            'estado' => 'Disponible',
        ]);
        $this->controller = new VentaController();
        Session::start();
    }


    public function testIndex()
    {
        $venta = Venta::factory()->count(10)->create();

        $response = $this->get('/api/ventas');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'ventas' => [
                '*' => [
                    'id', 'guid', 'estado', 'comprador', 'lineaVentas', 'precioTotal', 'paymentIntentId', 'created_at', 'updated_at'
                ]
            ],
            'paginacion' => [
                'pagina_actual', 'elementos_por_pagina', 'ultima_pagina', 'elementos_totales'
            ]
        ]);

        $this->assertCount(5, $response->json('ventas'));

    }

    public function testShowVentaFromDatabaseAndStoreInRedis()
    {
        $venta = Venta::factory()->create();

        Redis::shouldReceive('get')
            ->once()
            ->with('venta_' . $venta->guid)
            ->andReturn(null);

        Redis::shouldReceive('set')
            ->once()
            ->with('venta_' . $venta->guid, \Mockery::type('string'), 'EX', 1800);

        $response = $this->getJson('/api/ventas/' . $venta->guid);

        $response->assertOk()
            ->assertJson([
                'id' => $venta->id,
                'guid' => $venta->guid,
                'comprador' => json_decode(json_encode($venta->comprador), true),
                'lineaVentas' => json_decode(json_encode($venta->lineaVentas), true),
                'precioTotal' => round($venta->precioTotal, 2),
                'estado' => $venta->estado,

            ]);
    }

    public function testShowVentaFromRedis()
    {
        $ventaData = [
            'id' => 1,
            'guid' => 'venta-test-guid123',
            'comprador' => ['nombre' => 'Juan Pérez', 'email' => 'juan@example.com'],
            'lineaVentas' => [['producto' => 'Laptop', 'cantidad' => 1, 'precio' => 1000.00]],
            'precioTotal' => 1000.00,
            'estado' => 'Entregado',
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];

        Redis::shouldReceive('get')
            ->once()
            ->with('venta_venta-test-guid')
            ->andReturn(json_encode($ventaData));

        $response = $this->getJson('/api/ventas/venta-test-guid');

        $response->assertOk()
            ->assertJson($ventaData);
    }

    public function testShowNotFound()
    {
        $response = $this->get("/api/ventas/guid-invalido");

        $response->assertStatus(404);

        $response->assertJson([
            'message' => 'Venta no encontrada',
        ]);
    }

    public function testStoreSuccess()
    {
        $data = [
            'comprador' => ['nombre' => 'Juan Pérez', 'email' => 'juan@example.com'],
            'lineaVentas' => [
                'vendedor' => [
                    'nombre' => 'vendedor',
                    'apellido' => 'apellido',
                ],
                'producto' => [
                    'nombre' => 'Laptop',
                    'cantidad' => 1,
                    'precio' => 1000.00
                ],
            ],
            'precioTotal' => 1000.00,
            'estado' => 'Entregado',
        ];

        $response = $this->post('/api/ventas', $data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'precioTotal' => 1000.00,
        ]);
    }

    public function testStoreValidationError()
    {
        $data = [
            'comprador' => [],
            'lineaVentas' => [],
            'precioTotal' => -10,
            'estado' => ''
        ];

        $response = $this->post('/api/ventas', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['comprador', 'lineaVentas', 'precioTotal', 'estado']);
    }

    public function testDestroySuccess()
    {
        $venta = Venta::factory()->create();

        $this->assertDatabaseHas('ventas', ['guid' => $venta->guid]);

        $response = $this->delete("/api/ventas/{$venta->guid}");

        Redis::shouldReceive('exists')
            ->with('venta_' . $venta->guid);


        $response->assertStatus(200);
        $response->assertJson(['message' => 'Venta eliminada correctamente']);

        $this->assertDatabaseMissing('ventas', ['guid' => $venta->guid]);

    }

    public function testDestroyNotFound()
    {
        $response = $this->delete("/api/ventas/guid-inexistente100");

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Venta no encontrada']);
    }

    public function testProcesarCompraCarritoVacio()
    {
        Auth::loginUsingId(1);

        session()->put('carrito', (object)[
            'lineasCarrito' => []
        ]);

        $response = $this->postJson(route('pagarcarrito'));
        $response->assertStatus(302);
        $response->assertRedirect(route('payment.error'));
    }

    public function testProcesarCompraConMultiplesProductos()
    {
        $producto1 = Producto::factory()->create(['estado' => 'Disponible', 'stock' => 10, 'precio' => 50]);
        $producto2 = Producto::factory()->create(['estado' => 'Disponible', 'stock' => 5, 'precio' => 30]);

        Auth::loginUsingId(1);

        session()->put('carrito', (object)[
            'lineasCarrito' => [
                (object)['producto' => $producto1, 'cantidad' => 2],
                (object)['producto' => $producto2, 'cantidad' => 1]
            ]
        ]);

        $response = $this->postJson(route('pagarcarrito'));
        $response->assertStatus(302);
    }

    public function testProcesarCompraConStockInsuficiente()
    {
        $producto = Producto::factory()->create(['estado' => 'Disponible', 'stock' => 1, 'precio' => 100]);

        Auth::loginUsingId(1);

        session()->put('carrito', (object)[
            'lineasCarrito' => [
                (object)['producto' => $producto, 'cantidad' => 5]
            ]
        ]);

        $response = $this->postJson(route('pagarcarrito'));

        $response->assertStatus(302);
        $response->assertRedirect(route('payment.error'));
    }

    public function testProcesarCompraSinAutenticacion()
    {

        $producto = Producto::factory()->create(['estado' => 'Disponible', 'stock' => 5, 'precio' => 50]);

        session()->put('carrito', (object)[
            'lineasCarrito' => [
                (object)['producto' => $producto, 'cantidad' => 2]
            ]
        ]);

        $response = $this->postJson(route('pagarcarrito'));

        $response->assertStatus(302);
        $response->assertRedirect(route('payment.error'));
    }

    public function testProcesarCompraConProductoAgotado()
    {
        $producto = Producto::factory()->create(['estado' => 'Desactivado', 'stock' => 0, 'precio' => 75]);

        Auth::loginUsingId(1);

        session()->put('carrito', (object)[
            'lineasCarrito' => [
                (object)['producto' => $producto, 'cantidad' => 1]
            ]
        ]);

        $response = $this->postJson(route('pagarcarrito'));

        $response->assertStatus(302);
        $response->assertRedirect(route('payment.error'));
    }

    public function testProcesarCompraConProductoNoEncontrado()
    {
        Auth::loginUsingId(1);

        session()->put('carrito', (object)[
            'lineasCarrito' => [
                (object)['producto' => ['id' => '10000'], 'cantidad' => 1]
            ]
        ]);

        $response = $this->postJson(route('pagarcarrito'));

        $response->assertStatus(302);
        $response->assertRedirect(route('payment.error'));
    }

    public function testProcesarCompraSuccess()
    {
        $venta = [
            'guid' => '1234',
            'precioTotal' => 100.0
        ];

        $controllerMock = Mockery::mock(VentaController::class)->makePartial();
        $controllerMock->shouldReceive('validandoVentaAPartirDeCarrito')->andReturn($venta);
        $controllerMock->shouldReceive('crearPrecioStripe')->andReturn((object)['id' => 'price_123']);
        $controllerMock->shouldReceive('crearSesionPago')->andReturn((object)['url' => 'https://checkout.stripe.com']);

        $response = $controllerMock->procesarCompra();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('https://checkout.stripe.com', $response->getTargetUrl());

    }

    public function testValidandoVentaConProductoNoDisponible()
    {
        Auth::loginUsingId(1);
        $producto = Producto::factory()->create(['estado' => 'Desactivado', 'stock' => 10]);

        session()->put('carrito', (object)[
            'lineasCarrito' => [(object)['producto' => ['id' => $producto->id], 'cantidad' => 1]]
        ]);

        $response = $this->postJson(route('pagarcarrito'));

        $response->assertStatus(302);
        $response->assertRedirect(route('payment.error'));
    }

    public function testValidandoVentaConStockInsuficiente()
    {
        Auth::loginUsingId(1);
        $producto = Producto::factory()->create(['estado' => 'Disponible', 'stock' => 2]);

        session()->put('carrito', (object)[
            'lineasCarrito' => [(object)['producto' => $producto, 'cantidad' => 5]]
        ]);

        $response = $this->postJson(route('pagarcarrito'));

        $response->assertStatus(302);
        $carrito = session('carrito');
    }

    public function testValidandoVentaConProductoValido()
    {
        Auth::loginUsingId(1);
        $producto = Producto::factory()->create(['estado' => 'Disponible']);

        session()->put('carrito', (object)[
            'lineasCarrito' => [(object)['producto' => $producto, 'cantidad' => 1]]
        ]);

        $response = $this->postJson(route('pagarcarrito'));

        $response->assertStatus(302);
        $this->assertNotEmpty(session('carrito')->lineasCarrito);
    }

    public function testCrearVenta()
    {
        $cliente = Cliente::factory()->create();
        $lineasVenta = [
            [
                'producto' => ['id' => 1, 'nombre' => 'Producto 1'],
                'cantidad' => 2,
                'precio' => 100
            ],
            [
                'producto' => ['id' => 2, 'nombre' => 'Producto 2'],
                'cantidad' => 1,
                'precio' => 200
            ]
        ];
        $precioDeTodo = 400;

        $venta = $this->controller->crearVenta($cliente, $lineasVenta, $precioDeTodo);

        // Verificar que la venta se haya creado correctamente
        $this->assertEquals($cliente->id, $venta['comprador']['id']);
        $this->assertEquals($cliente->guid, $venta['comprador']['guid']);
        $this->assertEquals($cliente->nombre, $venta['comprador']['nombre']);
        $this->assertEquals($cliente->apellido, $venta['comprador']['apellido']);
        $this->assertEquals($lineasVenta, $venta['lineaVentas']);
        $this->assertEquals($precioDeTodo, $venta['precioTotal']);
        $this->assertEquals('Pendiente', $venta['estado']);
        $this->assertNotNull($venta['created_at']);
    }

        public function testGuardarVenta()
    {
        $producto = Producto::factory()->create();

        $ventaData = [
            'id' => 100,
            'guid' => GuidGenerator::GenerarId(),
            'estado' => 'Pendiente',
            'comprador' => [
                'id' => 1,
            ],
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'id' => 0,
                    ],
                    'cantidad' => 1,
                    'producto' => [
                        'guid' => $producto->guid,
                        'id' => $producto->id,
                        'nombre' => $producto->nombre,
                        'descripcion' => $producto->descripcion,
                        'estadoFisico' => $producto->estadoFisico,
                        'precio' => $producto->precio,
                        'categoria' => $producto->categoria,
                    ],
                    'precioTotal' => $producto->precio,
                ],
            ],
            'precioTotal' => $producto->precio,
        ];
        $this->controller->guardarVenta($ventaData);

        $this->assertDatabaseCount('ventas', 1);
    }

    public function testCrearSesionPagoSuccess()
    {
        $precioId = 'price_123';
        $expectedSessionId = 'session_123';

        Stripe::setApiKey(env('STRIPE_SECRET'));
        $mockedSession = Mockery::mock('alias:' . StripeSession::class);
        $mockedSession->shouldReceive('create')
            ->once()
            ->with([
                'line_items' => [[
                    'price' => $precioId,
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => env('APP_URL') . '/pago/save',
                'cancel_url' => env('APP_URL') . '/pago/cancelled',
            ])
            ->andReturn((object)['id' => $expectedSessionId]);


        $response = $this->controller->crearSesionPago($precioId);

        $this->assertEquals($expectedSessionId, $response->id);
        $this->assertEquals($expectedSessionId, Session::get('stripe_session_id'));
    }


    public function testCrearSesionPagoError()
    {
        $precioId = 'price_123';

        Stripe::setApiKey('Testerror');
        $stripeSessionMock = Mockery::mock('alias:' . StripeSession::class);
        $stripeSessionMock->shouldReceive('create')
            ->once()
            ->andThrow(new \Exception('Stripe error: Precio no válido'));

        $response = $this->controller->crearSesionPago($precioId);

        $this->assertEquals('{"error":"Stripe error: Precio no v\u00e1lido"}', $response->content('error'));

    }

    public function testRestandoStockDeProductosComprados()
    {
        // Crear una venta de prueba con una línea de venta
        $producto = Producto::factory()->create(['stock' => 10]);
        $venta = [
            'lineaVentas' => [
                [
                    'producto' => ['id' => $producto->id],
                    'cantidad' => 3,
                ]
            ]
        ];

        $this->controller->restandoStockDeProductosComprados($venta);

        $producto->refresh();
        $this->assertEquals(7, $producto->stock);
    }

    public function testDevolviendoProductos()
    {
        $producto = Producto::factory()->create(['stock' => 5]);
        $venta = [
            'lineaVentas' => [
                [
                    'producto' => ['id' => $producto->id],
                    'cantidad' => 2,
                ]
            ]
        ];
        $this->controller->devolviendoProductos($venta);

        $producto->refresh();
        $this->assertEquals(7, $producto->stock);
    }

    public function testCrearPrecioStripeSuccess()
    {
        $guid = 'venta_123';
        $precioTotal = 20.50;
        $productoStripeId = 'prod_abc123';
        $precioStripeId = 'price_xyz789';

        Stripe::setApiKey(env('STRIPE_SECRET'));


        $productMock = Mockery::mock('alias:' . StripeProduct::class);
        $productMock->shouldReceive('create')
            ->once()
            ->with([
                'name' => $guid,
                'description' => $guid . ' venta',
            ])
            ->andReturn((object)['id' => $productoStripeId]);

        $priceMock = Mockery::mock('alias:' . Price::class);
        $priceMock->shouldReceive('create')
            ->once()
            ->with([
                'unit_amount' => $precioTotal * 100,
                'currency' => 'eur',
                'product' => $productoStripeId,
            ])
            ->andReturn((object)['id' => $precioStripeId]);

        $response = $this->controller->crearPrecioStripe($guid, $precioTotal);

        $this->assertEquals($precioStripeId, $response->id);
    }

    public function testCrearPrecioStripeError()
    {
        $guid = 'venta_123';
        $precioTotal = 20.50;

        $productMock = Mockery::mock('alias:' . StripeProduct::class);
        $productMock->shouldReceive('create')
            ->once()
            ->andThrow(new \Exception('Stripe error: No se pudo crear el producto'));

        $response = $this->controller->crearPrecioStripe($guid, $precioTotal);

        $this->assertEquals('{"error":"Stripe error: No se pudo crear el producto"}', $response->content('error') );
    }

    public function testReembolsarPagoSuccess()
    {
        $paymentIntentId = 'pi_123456';
        $chargeId = 'ch_789012';
        $coste = 20.00;
        $refundId = 're_345678';

        $paymentIntentMock = Mockery::mock('alias:' . PaymentIntent::class);
        $paymentIntentMock->shouldReceive('retrieve')
            ->once()
            ->with($paymentIntentId)
            ->andReturn((object)[
                'latest_charge' => $chargeId,
                'status' => 'succeeded'
            ]);

        $chargeMock = Mockery::mock('alias:' . Charge::class);
        $chargeMock->shouldReceive('retrieve')
            ->once()
            ->with($chargeId)
            ->andReturn((object)[
                'refunded' => false
            ]);

        $refundMock = Mockery::mock('alias:' . Refund::class);
        $refundMock->shouldReceive('create')
            ->once()
            ->with([
                'charge' => $chargeId,
                'amount' => $coste * 100,
            ])
            ->andReturn((object)['id' => $refundId]);

        $response = $this->controller->reembolsarPago($paymentIntentId, $coste);

        // Verificar que el reembolso fue exitoso
        $this->assertEquals('success', $response['status']);
        $this->assertEquals('Reembolso realizado', $response['message']);
        $this->assertEquals($refundId, $response['reembolso']->id);
    }

    public function testReembolsarPagoPagoYaReembolsado()
    {
        $paymentIntentId = 'pi_123456';
        $chargeId = 'ch_789012';
        $coste = 20.00;

        $paymentIntentMock = Mockery::mock('alias:' . PaymentIntent::class);
        $paymentIntentMock->shouldReceive('retrieve')
            ->once()
            ->with($paymentIntentId)
            ->andReturn((object)[
                'latest_charge' => $chargeId,
                'status' => 'succeeded'
            ]);

        $chargeMock = Mockery::mock('alias:' . Charge::class);
        $chargeMock->shouldReceive('retrieve')
            ->once()
            ->with($chargeId)
            ->andReturn((object)[
                'refunded' => true
            ]);

        $response = $this->controller->reembolsarPago($paymentIntentId, $coste);

        $this->assertEquals('error', $response['status']);
        $this->assertEquals('Esa venta no ha sido pagada o ya ha sido reembolsada', $response['message']);
    }

    public function testReembolsarPagoLanzaExcepcion()
    {
        $paymentIntentId = 'pi_123456';
        $coste = 20.00;

        $paymentIntentMock = Mockery::mock('alias:' . PaymentIntent::class);
        $paymentIntentMock->shouldReceive('retrieve')
            ->once()
            ->andThrow(new \Exception('Stripe error: No se pudo recuperar el pago'));

        $response = $this->controller->reembolsarPago($paymentIntentId, $coste);

        $this->assertEquals('error', $response['status']);
        $this->assertEquals('Stripe error: No se pudo recuperar el pago', $response['message']);
    }

/*
    public function testCancelarVentaVentaNoEncontrada()
    {
        $response = $this->put("api/ventas/cancelar/guid");
        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('error', 'Venta no encontrada');
    }

    public function testCancelarVentaUsuarioNoAutorizado()
    {
        $user = User::factory()->create();
        Auth::loginUsingId(1);

        $venta = Venta::factory()->create(['guid' => GuidGenerator::generarId()]);

        $response = $this->put("api/ventas/cancelar/{$venta->guid}");
        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('error', 'No tienes permiso para cancelar esta venta');
    }

    public function testCancelarVentaEstadoNoPendiente()
    {

        $venta = Venta::factory()->create(['estado'=> 'Entregado']);

        $response = $this->put( "api/ventas/cancelar/{$venta->guid}");
        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('error', 'No se puede cancelar la venta ya que el estado de su compra es: Entregado');
    }

    public function testCancelarVentaReembolsoFallido()
    {
        $venta = Venta::factory()->create();

        $this->controller->reembolsarPago($venta->payment_intent_id, $venta->guid);

        $response = $this->controller->cancelarVenta($venta->guid);
        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('error', 'No se pudo realizar el reembolso. La venta no ha sido cancelada.');
    }
*/

    public function testPagoSuccessSesionValida()
    {
        // Simular el Session de Stripe
        $sessionMock = Mockery::mock('alias:' . StripeSession::class);
        $sessionMock->shouldReceive('retrieve')->andReturn((object)[
            'payment_intent' => 'pi_123456789'
        ]);

        // Simular el PaymentIntent de Stripe
        $paymentIntentMock = Mockery::mock('alias:' .PaymentIntent::class);
        $paymentIntentMock->shouldReceive('retrieve')->andReturn((object)[
            'status' => 'succeeded'
        ]);

        // Simular la sesión de Laravel
        session(['stripe_session_id' => 'sess_123', 'venta' => ['total' => 100]]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $response = $this->get('/pago/save');

        // Verificar que la redirección fue exitosa
        $response->assertRedirect(route('payment.success'));

        // Verificar que la sesión se ha vaciado
        $this->assertNull(session('venta'));
        $this->assertNull(session('stripe_session_id'));
    }

    public function testPagoSuccessSinSession()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $response = $this->get('/pago/save');

        $response->assertRedirect(route('payment.error'));
    }

    public function testPagoSuccessPagoFallido()
    {
        Stripe::setApiKey('sk_false_123');

        $response = $this->get('/pago/save');

        $response->assertRedirect(route('payment.error'));
    }
/*
    public function test_producto_no_disponible()
    {
        // Simular un producto no disponible
        Producto::factory()->create(['estado' => 'No Disponible', 'stock' => 0]);

        $linea = (object)[
            'producto' => (object)[
                'id' => 1,
                'nombre' => 'Producto no disponible',
            ],
        ];

        $response = $this->controller->validarProducto($linea);

        $response->assertRedirect(route('payment.error'));
        $response->assertSessionHas('message', 'El producto ya no está disponible: Producto no disponible');
    }

    public function test_producto_sin_stock()
    {
        // Simular un producto sin stock
        Producto::factory()->create(['estado' => 'Disponible', 'stock' => 0]);

        $linea = (object)[
            'producto' => (object)[
                'id' => 1,
                'nombre' => 'Producto agotado',
            ],
        ];

        $response = $this->controller->validarProducto($linea);

        $response->assertRedirect(route('payment.error'));
        $response->assertSessionHas('message', 'El producto ya no está disponible: Producto agotado');
    }

    public function test_producto_cantidad_mayor_que_stock()
    {
        // Simular un producto con stock limitado
        Producto::factory()->create(['estado' => 'Disponible', 'stock' => 5, 'precio' => 10]);

        $linea = (object)[
            'producto' => (object)[
                'id' => 1,
                'nombre' => 'Producto con stock limitado',
                'precio' => 10,
                'vendedor' => (object)[
                    'id' => 1,
                    'guid' => '123-abc',
                    'nombre' => 'Juan',
                    'apellido' => 'Pérez',
                ],
            ],
            'cantidad' => 10,
        ];

        $result = $this->controller->validarProducto($linea);

        $this->assertEquals(5, $result['lineaVenta']['cantidad']);
        $this->assertEquals(50, $result['precioLinea']);
    }

    public function test_producto_disponible_con_stock()
    {
        // Simular un producto con stock suficiente
        Producto::factory()->create(['estado' => 'Disponible', 'stock' => 10, 'precio' => 20]);

        $linea = (object)[
            'producto' => (object)[
                'id' => 1,
                'nombre' => 'Producto válido',
                'precio' => 20,
                'estadoFisico' => 'Nuevo',
                'descripcion' => 'Descripción del producto',
                'categoria' => 'Electrónica',
                'guid' => '456-def',
                'vendedor' => (object)[
                    'id' => 1,
                    'guid' => '123-abc',
                    'nombre' => 'Juan',
                    'apellido' => 'Pérez',
                ],
            ],
            'cantidad' => 3,
        ];

        $result = $this->controller->validarProducto($linea);

        $this->assertEquals(3, $result['lineaVenta']['cantidad']);
        $this->assertEquals(60, $result['precioLinea']);
        $this->assertEquals('Producto válido', $result['lineaVenta']['producto']['nombre']);
    }
*/
}
