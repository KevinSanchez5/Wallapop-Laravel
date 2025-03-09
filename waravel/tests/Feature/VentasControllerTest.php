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

        $response->assertStatus(200);
        $this->assertNotEmpty(session('carrito')->lineasCarrito);
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

    public function testCrearSesionPagoExito()
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



    /*
    public function testCancelarVentaVentaNoEncontrada()
    {
        $response = $this->getJson('/ventas/cancelar/test-guid');
        $response->assertRedirect(route('profile')->with('error', 'Venta no encontrada'));
    }

    public function testCancelarVentaUsuarioNoAutorizado()
    {
        $venta = Venta::factory()->make(['guid' => 'test-guid', 'comprador' => ['guid' => 'other-user-guid']]);
        $user = User::factory()->make(['guid' => 'test-user-guid']);
        Auth::login($user);

        $response = $this->getJson('/ventas/cancelar/test-guid');
        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('error', 'No tienes permiso para cancelar esta venta');
    }

    public function testCancelarVentaEstadoNoPendiente()
    {
        $venta = Venta::factory()->make(['guid' => 'test-guid', 'estado' => 'Entregado']);

        // Hacer la solicitud y verificar la respuesta
        $response = $this->getJson( '/ventas/cancelar/test-guid');
        $response->assertRedirect(route('payment.error'));
        $response->assertSessionHas('error', 'No se puede cancelar la venta ya que el estado de su compra es: Entregado');
    }

    public function testCancelarVentaReembolsoFallido()
    {
        $venta = Venta::factory()->make(['guid' => 'test-guid', 'estado' => 'Pendiente', 'payment_intent_id' => null]);


        // Hacer la solicitud y verificar la respuesta
        $response = $this->getJson( '/ventas/cancelar/test-guid');
        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('error', 'No se pudo realizar el reembolso. La venta no ha sido cancelada.');
    }

    public function testCancelarVentaExito()
    {
        $venta = Venta::factory()->create();


        $this->partialMock(VentaController::class, function ($mock) {
            $mock->shouldReceive('reembolsarPago')
                ->andReturn(['status' => 'success']);
            $mock->shouldReceive('devolviendoProductos')
                ->with(null);
        });

        // Hacer la solicitud y verificar la respuesta
        $response = $this->getJson( '/ventas/cancelar/' . $venta->guid);
        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('success', 'Venta Cancelada');
    }*/
}
