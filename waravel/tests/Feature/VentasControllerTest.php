<?php

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Tests\TestCase;

class VentasControllerTest extends TestCase
{
    private User $usuario1;
    private User $usuario2;
    private Cliente $vendedor;
    private Cliente $comprador;
    private Producto $producto;

    protected function setUp(): void
    {
        parent::setUp();
        Redis::flushall();

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
        parent::setUp();
        Redis::flushAll();
    }

    protected function tearDown(): void
    {
        Redis::flushAll();
        parent::tearDown();
    }

    public function testIndex()
    {
        $lineaVentas = [
            [
                'vendedor' => $this->vendedor->toArray(),
                'cantidad' => 5,
                'producto' => $this->producto->toArray(),
                'precioTotal' => $this->producto->precio * 5,
                'estado' => 'Disponible',
            ],
        ];

        $precioTotalVenta = array_sum(array_column($lineaVentas, 'precioTotal'));

        $venta = Venta::factory()->create([
            'guid' => 'venta-guid',
            'comprador' => $this->comprador->toArray(),
            'lineaVentas' => json_encode($lineaVentas),
            'precioTotal' => $precioTotalVenta,
            'estado' => 'Enviado',
        ]);

        $response = $this->get('/api/ventas?per_page=15');

        $response->assertOk();

        $response->assertJsonFragment([
            'guid' => $venta->guid,
            'precioTotal' => $venta->precioTotal,
        ]);
    }

    public function testShowVentaFromDatabaseAndStoreInRedis()
    {
        $venta = Venta::factory()->create([
            'guid' => 'venta-guid',
            'comprador' => $this->comprador->toArray(),
            'lineaVentas' => json_encode([
                'vendedor' => $this->vendedor->toArray(),
                'cantidad' => 1,
                'producto' => $this->producto->toArray(),
                'precioTotal' => $this->producto->precio,
            ]),
            'precioTotal' => $this->producto->precio,
            'estado' => 'Enviado',
        ]);

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
                'comprador' => $venta->comprador,
                'lineaVentas' => $venta->lineaVentas,
                'precioTotal' => round($venta->precioTotal, 2),
                'estado' => $venta->estado,
            ]);
    }


    public function testShowVentaFromRedis()
    {
        $ventaData = [
            'id' => 1,
            'guid' => 'venta-test-guid',
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
            'guid' => Str::uuid()->toString(),
            'comprador' => ['nombre' => 'Juan Pérez', 'email' => 'juan@example.com'],
            'lineaVentas' => [['producto' => 'Laptop', 'cantidad' => 1, 'precio' => 1000.00]],
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
            'estado'=> 'no valido'
        ];

        $response = $this->post('/api/ventas', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['comprador', 'lineaVentas', 'precioTotal', 'estado']);
    }

    public function testDestroySuccess()
    {
        $venta = Venta::factory()->create([]);

        $this->assertDatabaseHas('ventas', ['id' => $venta->id]);

        $response = $this->delete("/api/ventas/{$venta->id}");

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Venta eliminada correctamente']);

        $this->assertDatabaseMissing('ventas', ['id' => $venta->id]);
    }

    public function testDestroyNotFound()
    {

        $response = $this->delete("/api/ventas/guid-invalido");

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Venta no encontrada']);
    }

}
