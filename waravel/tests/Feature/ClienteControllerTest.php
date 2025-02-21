<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Tests\TestCase;

class ClienteControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $cliente;

    protected function setUp(): void
    {
        parent::setUp();
        Redis::flushall();

        $this->user = User::create([
            'name' => 'usuario',
            'email' => 'usuario@example.com',
            'password' => bcrypt('secret'),
            'role' => 'cliente',
        ]);

        $this->cliente = Cliente::create([
            'guid' => 'cliente-guid',
            'nombre' => 'Pepe',
            'apellido' => 'Perez',
            'avatar' => 'avatar.png',
            'telefono' => '123456789',
            'direccion' => [
                'calle' => 'Avenida Siempre Viva',
                'numero' => 742,
                'piso' => 1,
                'letra' => 'A',
                'codigoPostal' => 28001
            ],
            'activo' => true,
            'usuario_id' => $this->user->id,
        ]);
    }

    public function test_index()
    {
        for ($i = 1; $i <= 10; $i++) {
            Cliente::create([
                'guid' => 'guid-' . $i,
                'nombre' => "Cliente $i",
                'apellido' => "Apellido $i",
                'avatar' => "avatar$i.png",
                'telefono' => "123456789$i",
                'direccion' => "Dirección $i",
                'activo' => true,
                'usuario_id' => $this->user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $response = $this->getJson('api/clientes');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'clientes' => [
                '*' => [
                    'id', 'guid', 'nombre', 'apellido', 'avatar', 'telefono', 'direccion', 'activo', 'usuario_id', 'created_at', 'updated_at'
                ]
            ],
            'paginacion' => [
                'pagina_actual', 'elementos_por_pagina', 'ultima_pagina', 'elementos_totales'
            ]
        ]);

        $responseData = $response->json();
        $this->assertEquals(1, $responseData['paginacion']['pagina_actual']);
        $this->assertEquals(5, $responseData['paginacion']['elementos_por_pagina']);
        $this->assertEquals(11, $responseData['paginacion']['elementos_totales']);
    }

    public function test_show(): void
    {
        Redis::del('cliente_' . $this->cliente->id);

        $response = $this->getJson("/api/clientes/{$this->cliente->id}");

        $response->assertStatus(200);

        $response->assertJson([
            'id' => $this->cliente->id,
            'guid' => $this->cliente->guid,
            'nombre' => $this->cliente->nombre,
            'apellido' => $this->cliente->apellido,
            'avatar' => $this->cliente->avatar,
            'telefono' => $this->cliente->telefono,
            'direccion' => json_decode(json_encode($this->cliente->direccion), true),
            'activo' => true,
            'usuario_id' => $this->user->id,
        ]);

        $clienteRedis = json_decode(Redis::get('cliente_' . $this->cliente->id), true);
        $this->assertEquals($this->cliente->id, $clienteRedis['id']);
        $this->assertEquals($this->cliente->nombre, $clienteRedis['nombre']);
        $this->assertEquals($this->cliente->apellido, $clienteRedis['apellido']);
        $this->assertEquals($this->cliente->avatar, $clienteRedis['avatar']);
        $this->assertEquals($this->cliente->telefono, $clienteRedis['telefono']);
        $this->assertEquals(json_decode(json_encode($this->cliente->direccion), true), $clienteRedis['direccion']);
        $this->assertTrue($clienteRedis['activo']);
        $this->assertEquals($this->cliente->usuario_id, $clienteRedis['usuario_id']);
    }

    public function test_show_from_redis(): void
    {
        Redis::set('cliente_' . $this->cliente->id, json_encode($this->cliente));

        $response = $this->getJson("/api/clientes/{$this->cliente->id}");

        $response->assertStatus(200);

        $response->assertJson([
            'id' => $this->cliente->id,
            'guid' => $this->cliente->guid,
            'nombre' => $this->cliente->nombre,
            'apellido' => $this->cliente->apellido,
            'avatar' => $this->cliente->avatar,
            'telefono' => $this->cliente->telefono,
            'direccion' => json_decode(json_encode($this->cliente->direccion), true),
            'activo' => true,
            'usuario_id' => $this->user->id,
        ]);

        $clienteRedis = json_decode(Redis::get('cliente_' . $this->cliente->id), true);
        $this->assertEquals($this->cliente->id, $clienteRedis['id']);
        $this->assertEquals($this->cliente->nombre, $clienteRedis['nombre']);
        $this->assertEquals($this->cliente->apellido, $clienteRedis['apellido']);
        $this->assertEquals($this->cliente->avatar, $clienteRedis['avatar']);
        $this->assertEquals($this->cliente->telefono, $clienteRedis['telefono']);
        $this->assertEquals(json_decode(json_encode($this->cliente->direccion), true), $clienteRedis['direccion']);
        $this->assertTrue($clienteRedis['activo']);
        $this->assertEquals($this->cliente->usuario_id, $clienteRedis['usuario_id']);
    }

    public function test_show_not_found(): void
    {
        $response = $this->getJson("/api/clientes/999");
        $response->assertStatus(404);

        $response->assertJson([
            'message' => 'Cliente no encontrado'
        ]);
    }

    public function test_store(): void
    {
        $data = [
            'guid' => 'cliente2-guid',
            'nombre' => 'Pepe',
            'apellido' => 'Perez',
            'avatar' => 'avatar.png',
            'telefono' => '123456789',
            'direccion' => [
                'calle' => 'Avenida Siempre Viva',
                'numero' => 742,
                'piso' => 1,
                'letra' => 'A',
                'codigoPostal' => 28001
            ],
            'activo' => true,
            'usuario_id' => $this->user->id,
        ];

        $response = $this->postJson('/api/clientes', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('clientes', [
            'guid' => $data['guid'],
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'avatar' => $data['avatar'],
            'telefono' => $data['telefono'],
            'activo' => $data['activo'],
            'usuario_id' => $data['usuario_id'],
        ]);

        // Comparo dirección aparte para no tener problemas con el tipo json
        $cliente = Cliente::where('guid', $data['guid'])->first();
        $this->assertEquals(json_decode(json_encode($data['direccion'])), $cliente->direccion);
    }

    public function test_store_avatar_null(): void
    {
        $data = [
            'guid' => 'cliente2-guid',
            'nombre' => 'Pepe',
            'apellido' => 'Perez',
            'telefono' => '123456789',
            'direccion' => [
                'calle' => 'Avenida Siempre Viva',
                'numero' => 742,
                'piso' => 1,
                'letra' => 'A',
                'codigoPostal' => 28001
            ],
            'activo' => true,
            'usuario_id' => $this->user->id,
        ];

        $response = $this->postJson('/api/clientes', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('clientes', [
            'guid' => $data['guid'],
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'avatar' => 'avatar.png',
            'telefono' => $data['telefono'],
            'activo' => $data['activo'],
            'usuario_id' => $data['usuario_id'],
        ]);

        // Comparo dirección aparte para no tener problemas con el tipo json
        $cliente = Cliente::where('guid', $data['guid'])->first();
        $this->assertEquals(json_decode(json_encode($data['direccion'])), $cliente->direccion);
    }

    public function test_store_invalid(): void
    {
        $data = [
            'guid' => 'cliente2-guid',
            'nombre' => 1,
            'apellido' => 'Perez',
            'avatar' => 'avatar.png',
            'telefono' => '123456789',
            'direccion' => [
                'calle' => 'Avenida Siempre Viva',
                'numero' => 742,
                'piso' => 1,
                'letra' => 'A',
                'codigoPostal' => 28001
            ],
            'activo' => true,
            'usuario_id' => $this->user->id,
        ];

        $response = $this->postJson('/api/clientes', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['nombre']);
    }

    public function test_update(): void
    {
        $data = [
            'nombre' => 'nombre_update',
            'apellido' => 'apellido_update',
            'avatar' => 'avatar_update.png',
            'telefono' => '123456789',
            'direccion' => [
                'calle' => 'Avenida Update',
                'numero' => 123,
                'piso' => 3,
                'letra' => 'C',
                'codigoPostal' => 28005
            ],
            'activo' => true,
            'usuario_id' => $this->user->id,
        ];

        $response = $this->putJson("/api/clientes/{$this->cliente->id}", $data);

        $this->cliente->refresh();

        $response->assertStatus(200);

        Redis::set('cliente_' . $this->cliente->id, json_encode($data));

        $this->assertDatabaseHas('clientes', [
            'nombre' => 'nombre_update',
            'apellido' => 'apellido_update',
            'avatar' => 'avatar_update.png',
            'telefono' => '123456789',
            'activo' => true,
            'usuario_id' => $this->user->id,
        ]);

        // Comparo dirección aparte para no tener problemas con el tipo json
        $cliente = Cliente::find($this->cliente->id);
        $this->assertEquals(json_decode(json_encode($data['direccion'])), $cliente->direccion);

        $clienteActualizado = json_decode(Redis::get('cliente_' . $this->cliente->id), true);
        $this->assertEquals($data['nombre'], $clienteActualizado['nombre']);
        $this->assertEquals($data['apellido'], $clienteActualizado['apellido']);
        $this->assertEquals($data['avatar'], $clienteActualizado['avatar']);
        $this->assertEquals($data['telefono'], $clienteActualizado['telefono']);
        $this->assertEquals($data['direccion'], $clienteActualizado['direccion']);
        $this->assertEquals($data['activo'], $clienteActualizado['activo']);
        $this->assertEquals($data['usuario_id'], $clienteActualizado['usuario_id']);
    }

    public function test_update_not_found(): void
    {
        $data = [
            'nombre' => 'Juan',
            'apellido' => 'Gómez',
            'avatar' => 'nuevo_avatar.png',
            'telefono' => '9876543210',
            'direccion' => [
                'calle' => 'Calle Nueva',
                'numero' => 123,
                'piso' => 2,
                'letra' => 'B',
                'codigoPostal' => 45000
            ],
            'activo' => false,
        ];

        $response = $this->putJson("/api/clientes/999", $data);
        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Cliente no encontrado',
        ]);
    }

    public function test_update_invalid(): void
    {
        $data = [
            'nombre' => 1,
            'apellido' => 'apellido_update',
            'avatar' => 'avatar_update.png',
            'telefono' => '123456789',
            'direccion' => [
                'calle' => 'Avenida Update',
                'numero' => 123,
                'piso' => 3,
                'letra' => 'C',
                'codigoPostal' => 28005
            ],
            'activo' => true,
            'usuario_id' => $this->user->id,
        ];

        $response = $this->putJson("/api/clientes/{$this->cliente->id}", $data);

        $this->cliente->refresh();

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['nombre']);
    }

    public function test_destroy(): void
    {
        $response = $this->deleteJson("/api/clientes/{$this->cliente->id}");

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Cliente eliminado correctamente']);

        $this->assertDatabaseMissing('clientes', ['id' => $this->cliente->id]);
    }

    public function test_destroy_not_found(): void
    {
        $response = $this->deleteJson("/api/clientes/999");

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Cliente no encontrado',
        ]);
    }

    public function test_search_favorites(): void
    {
        $producto = Producto::create([
            'guid' => 'guid-producto',
            'vendedor_id' => $this->cliente->id,
            'nombre' => 'Producto de prueba',
            'descripcion' => 'Este es un producto de prueba',
            'estadoFisico' => 'Nuevo',
            'precio' => 99.99,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
            'imagenes' => json_encode(['imagenProducto.png']),
        ]);

        $this->cliente->favoritos()->attach($producto->id);

        $response = $this->getJson("/api/clientes/{$this->cliente->id}/favoritos");

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $producto->id]);
    }

    public function test_search_favorites_cliente_not_found(): void
    {
        $response = $this->getJson("/api/clientes/999/favoritos");

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Cliente no encontrado']);
    }

    public function test_add_to_favorites(): void
    {
        $producto = Producto::create([
            'guid' => 'guid-producto',
            'vendedor_id' => $this->cliente->id,
            'nombre' => 'Producto de prueba',
            'descripcion' => 'Este es un producto de prueba',
            'estadoFisico' => 'Nuevo',
            'precio' => 99.99,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
            'imagenes' => json_encode(['imagenProducto.png']),
        ]);

        $response = $this->postJson("/api/clientes/{$this->cliente->id}/favoritos", [
            'producto_id' => $producto->id
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Producto agregado a favoritos']);

        $this->assertDatabaseHas('cliente_favoritos', [
            'cliente_id' => $this->cliente->id,
            'producto_id' => $producto->id
        ]);
    }

    public function test_add_to_favorites_cliente_not_found(): void
    {
        $producto = Producto::create([
            'guid' => 'guid-producto',
            'vendedor_id' => $this->cliente->id,
            'nombre' => 'Producto de prueba',
            'descripcion' => 'Este es un producto de prueba',
            'estadoFisico' => 'Nuevo',
            'precio' => 99.99,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
            'imagenes' => json_encode(['imagenProducto.png']),
        ]);

        $response = $this->postJson("/api/clientes/999/favoritos", [
            'producto_id' => $producto->id
        ]);

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Cliente no encontrado']);
    }

    public function test_add_to_favorites_producto_not_found(): void
    {

        $response = $this->postJson("/api/clientes/{$this->cliente->id}/favoritos", [
            'producto_id' => 999
        ]);

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Producto no encontrado']);
    }

    public function test_remove_from_favorites(): void
    {
        $producto = Producto::create([
            'guid' => 'guid-producto',
            'vendedor_id' => $this->cliente->id,
            'nombre' => 'Producto de prueba',
            'descripcion' => 'Este es un producto de prueba',
            'estadoFisico' => 'Nuevo',
            'precio' => 99.99,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
            'imagenes' => json_encode(['imagenProducto.png']),
        ]);

        $this->cliente->favoritos()->attach($producto->id);

        $response = $this->deleteJson("/api/clientes/{$this->cliente->id}/favoritos", [
            'producto_id' => $producto->id
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Producto eliminado de favoritos']);

        $this->assertDatabaseMissing('cliente_favoritos', [
            'cliente_id' => $this->cliente->id,
            'producto_id' => $producto->id
        ]);
    }

    public function test_remove_from_favorites_cliente_not_found(): void
    {
        $producto = Producto::create([
            'guid' => 'guid-producto',
            'vendedor_id' => $this->cliente->id,
            'nombre' => 'Producto de prueba',
            'descripcion' => 'Este es un producto de prueba',
            'estadoFisico' => 'Nuevo',
            'precio' => 99.99,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
            'imagenes' => json_encode(['imagenProducto.png']),
        ]);

        $response = $this->deleteJson("/api/clientes/999/favoritos", [
            'producto_id' => $producto->id
        ]);

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Cliente no encontrado']);
    }

    public function test_remove_from_favorites_producto_not_found(): void
    {
        $response = $this->deleteJson("/api/clientes/{$this->cliente->id}/favoritos", [
            'producto_id' => 999
        ]);

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Producto no encontrado']);
    }
}
