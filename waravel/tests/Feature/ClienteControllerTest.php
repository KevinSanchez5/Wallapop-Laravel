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

    public function test_index()
    {

        $user = User::create([
            'name' => 'Cliente',
            'email' => 'cliente@example.com',
            'password' => bcrypt('password'),
            'role' => 'cliente',
        ]);


        for ($i = 1; $i <= 10; $i++) {
            Cliente::create([
                'guid' => 'guid-' . $i,
                'nombre' => "Cliente $i",
                'apellido' => "Apellido $i",
                'avatar' => "avatar$i.png",
                'telefono' => "123456789$i",
                'direccion' => "Dirección $i",
                'activo' => true,
                'usuario_id' => $user->id,
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
        $this->assertEquals(10, $responseData['paginacion']['elementos_totales']);
    }

    public function test_show(): void
    {
        $usuario = User::create([
            'name' => 'Cliente',
            'email' => 'cliente@example.com',
            'password' => bcrypt('secret'),
            'role' => 'cliente',
        ]);

        $cliente = Cliente::create([
            'guid' => Str::uuid(),
            'nombre' => 'Pepe',
            'apellido' => 'Perez',
            'avatar' => 'avatar.png',
            'telefono' => '1234567890',
            'direccion' => json_encode([
                'calle' => 'Avenida Siempre Viva',
                'numero' => 742,
                'piso' => 1,
                'letra' => 'A',
                'codigoPostal' => 28001
            ]),
            'activo' => true,
            'usuario_id' => $usuario->id,
        ]);

        Redis::set('cliente_' . $cliente->id, json_encode($cliente));

        $response = $this->getJson("/api/clientes/{$cliente->id}");
        $response->assertStatus(200);
        $responseData = $response->json();
        $direccionDecodificada = json_decode($responseData['direccion'], true);

        $response->assertJson([
            'id' => $cliente->id,
            'nombre' => $cliente->nombre,
            'apellido' => $cliente->apellido,
            'avatar' => $cliente->avatar,
            'telefono' => $cliente->telefono,
            'direccion' => $direccionDecodificada,
            'activo' => true,
            'usuario_id' => $usuario->id,
        ]);


        $clienteRedis = json_decode(Redis::get('cliente_' . $cliente->id), true);
        $this->assertEquals($cliente->id, $clienteRedis['id']);
        $this->assertEquals($cliente->nombre, $clienteRedis['nombre']);
        $this->assertEquals($cliente->apellido, $clienteRedis['apellido']);
    }

    public function test_store(): void
    {

        $usuario = User::create([
            'name' => 'Cliente',
            'email' => 'cliente@example.com',
            'password' => bcrypt('secret'),
            'role' => 'cliente',
        ]);


        $data = [
            'guid' => Str::uuid(),
            'nombre' => 'Pepe',
            'apellido' => 'Perez',
            'avatar' => 'http://example.com/avatar.png',
            'telefono' => '1234567890',
            'direccion' => [
                'calle' => 'Avenida Siempre Viva',
                'numero' => 742,
                'piso' => 1,
                'letra' => 'A',
                'codigoPostal' => 28001
            ],
            'activo' => true,
            'usuario_id' => $usuario->id,
        ];

        $response = $this->postJson('/api/clientes', $data);
        $response->assertStatus(201);

        $this->assertDatabaseHas('clientes', [
            'nombre' => 'Pepe',
            'apellido' => 'Perez',
            'telefono' => '1234567890',
            'usuario_id' => $usuario->id,
        ]);
    }



    public function test_update(): void
    {
        $usuario = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('secret'),
            'role' => 'admin',
        ]);

        $cliente = Cliente::create([
            'guid' => Str::uuid(),
            'nombre' => 'Pepe',
            'apellido' => 'Perez',
            'avatar' => 'avatar.png',
            'telefono' => '1234567890',
            'direccion' => [
                'calle' => 'Avenida Siempre Viva',
                'numero' => 742,
                'piso' => 1,
                'letra' => 'A',
                'codigoPostal' => 28001
            ],
            'activo' => true,
            'usuario_id' => $usuario->id,
        ]);

        Redis::set('cliente_' . $cliente->id, json_encode($cliente->toArray()));

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

        $response = $this->putJson("/api/clientes/{$cliente->id}", $data);
        $response->assertStatus(200);

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'nombre' => 'Juan',
            'apellido' => 'Gómez',
            'avatar' => 'nuevo_avatar.png',
            'telefono' => '9876543210',
            'activo' => false,
        ]);

        $clienteActualizado = json_decode(Redis::get('cliente_' . $cliente->id), true);

        $this->assertEquals($data['nombre'], $clienteActualizado['nombre']);
        $this->assertEquals($data['apellido'], $clienteActualizado['apellido']);
        $this->assertEquals($data['avatar'], $clienteActualizado['avatar']);
        $this->assertEquals($data['telefono'], $clienteActualizado['telefono']);
        $this->assertEquals($data['activo'], $clienteActualizado['activo']);
        $this->assertEquals($data['direccion'], json_decode($clienteActualizado['direccion'], true)); // Convertimos `direccion` a array
    }

    public function test_update_not_found(): void
    {
        $idInexistente = 9999;

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

        $response = $this->putJson("/api/clientes/{$idInexistente}", $data);
        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Cliente no encontrado',
        ]);
    }

    public function test_destroy_cliente_existe(): void
    {
        // ✅ Crear un usuario
        $usuario = User::create([
            'name' => 'Cliente',
            'email' => 'cliente@example.com',
            'password' => bcrypt('secret'),
            'role' => 'cliente',
        ]);


        $cliente = Cliente::create([
            'guid' => Str::uuid(),
            'nombre' => 'Pepe',
            'apellido' => 'Perez',
            'avatar' => 'http://example.com/avatar.png',
            'telefono' => '1234567890',
            'direccion' => json_encode([
                'calle' => 'Avenida Siempre Viva',
                'numero' => 742,
                'piso' => 1,
                'letra' => 'A',
                'codigoPostal' => 28001
            ]),
            'activo' => true,
            'usuario_id' => $usuario->id,
        ]);

        $response = $this->deleteJson("/api/clientes/{$cliente->id}");

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Cliente eliminado correctamente']);

        $this->assertDatabaseMissing('clientes', ['id' => $cliente->id]);
    }

    public function test_destroy_cliente_no_existe(): void
    {
        $response = $this->deleteJson("/api/clientes/9999");
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Cliente no encontrado']);
    }

    public function test_search_favorites_cliente_existe(): void
    {
        $cliente = Cliente::factory()->create();
        $producto = Producto::factory()->create();

        $cliente->favoritos()->attach($producto->id);

        $response = $this->getJson("/api/clientes/{$cliente->id}/favoritos");

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $producto->id]);
    }

    public function test_search_favorites_cliente_no_existe(): void
    {
        $response = $this->getJson("/api/clientes/9999/favoritos");

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Cliente no encontrado']);
    }

    public function test_add_to_favorites_exitoso(): void
    {
        $cliente = Cliente::factory()->create();
        $producto = Producto::factory()->create();

        $response = $this->postJson("/api/clientes/{$cliente->id}/favoritos", [
            'producto_id' => $producto->id
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Producto agregado a favoritos']);

        $this->assertDatabaseHas('cliente_producto', [
            'cliente_id' => $cliente->id,
            'producto_id' => $producto->id
        ]);
    }

    public function test_add_to_favorites_cliente_no_existe(): void
    {
        $producto = Producto::factory()->create();

        $response = $this->postJson("/api/clientes/9999/favoritos", [
            'producto_id' => $producto->id
        ]);

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Cliente no encontrado']);
    }

    public function test_add_to_favorites_producto_no_existe(): void
    {
        $cliente = Cliente::factory()->create();

        $response = $this->postJson("/api/clientes/{$cliente->id}/favoritos", [
            'producto_id' => 9999
        ]);

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Producto no encontrado']);
    }

    public function test_remove_from_favorites_exitoso(): void
    {
        $cliente = Cliente::factory()->create();
        $producto = Producto::factory()->create();

        $cliente->favoritos()->attach($producto->id);

        $response = $this->deleteJson("/api/clientes/{$cliente->id}/favoritos", [
            'producto_id' => $producto->id
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Producto eliminado de favoritos']);

        $this->assertDatabaseMissing('cliente_producto', [
            'cliente_id' => $cliente->id,
            'producto_id' => $producto->id
        ]);
    }

    public function test_remove_from_favorites_cliente_no_existe(): void
    {
        $producto = Producto::factory()->create();

        $response = $this->deleteJson("/api/clientes/9999/favoritos", [
            'producto_id' => $producto->id
        ]);

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Cliente no encontrado']);
    }

    public function test_remove_from_favorites_producto_no_existe(): void
    {
        $cliente = Cliente::factory()->create();

        $response = $this->deleteJson("/api/clientes/{$cliente->id}/favoritos", [
            'producto_id' => 9999
        ]);

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Producto no encontrado']);
    }


}
