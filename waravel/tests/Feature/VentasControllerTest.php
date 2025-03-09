<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class VentasControllerTest extends TestCase
{
    use RefreshDatabase;
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
    }

    protected function tearDown(): void
    {
        Redis::isFake();
        parent::tearDown();
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
            ->with('venta_'.$venta->guid)
            ->andReturn(null);

        Redis::shouldReceive('set')
            ->once()
            ->with('venta_'.$venta->guid, \Mockery::type('string'), 'EX', 1800);

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
                'vendedor'=>[
                    'nombre' => 'vendedor',
                    'apellido' => 'apellido',
                ] ,
                'producto' => [
                    'nombre'=>'Laptop',
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
            'estado'=> ''
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

    public function testProcesarCompra()
    {

        $producto = Producto::factory()->create([
            'estado' => 'Disponible',
            'stock' => 10,
            'precio' => 100,
        ]);

        Auth::loginUsingId(1); // Simulando el login del usuario con ID 1

        session()->put('carrito', (object)[
            'lineasCarrito' => [
                (object)[
                    'producto' => $producto,
                    'cantidad' => 2
                ]
            ]
        ]);

        $response = $this->postJson(route('pagarcarrito'));

        $response->assertStatus(302); // Redirección al proceso de pago, ajusta según sea necesario

        $response->assertSessionHas('stripe_session_id');
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
        // Crear cliente y productos de prueba
        $producto1 = Producto::factory()->create(['estado' => 'Disponible', 'stock' => 10, 'precio' => 50]);
        $producto2 = Producto::factory()->create(['estado' => 'Disponible', 'stock' => 5, 'precio' => 30]);

        // Simular inicio de sesión
        Auth::loginUsingId(1);

        // Simular productos en el carrito
        session()->put('carrito', (object)[
            'lineasCarrito' => [
                (object)['producto' => $producto1, 'cantidad' => 2],
                (object)['producto' => $producto2, 'cantidad' => 1]
            ]
        ]);

        // Enviar petición para procesar la compra
        $response = $this->postJson(route('pagarcarrito'));

        // Verificar redirección y sesión
        $response->assertStatus(302);
        $response->assertSessionHas('stripe_session_id');
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

        // Verificar que no se puede procesar la compra
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

        // Verificar que redirige al login
        $response->assertStatus(302);
        $response->assertRedirect(route('pago/error'));
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

        // Verificar que redirige a error
        $response->assertStatus(302);
        $response->assertRedirect(route('payment.error'));
    }



}
