<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
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

        for ($i = 1; $i <= 20; $i++) {
            Producto::create([
                'guid' => 'guid-' . $i,
                'vendedor_id' => 1,
                'nombre' => 'Producto ' . $i,
                'descripcion' => 'Descripción del producto ' . $i,
                'estadoFisico' => 'Nuevo',
                'precio' => rand(10, 100),
                'categoria' => 'Tecnologia',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['imagen' . $i . '.png']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $response = $this->getJson('/api/productos');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'productos' => [
                '*' => [
                    'id', 'guid', 'vendedor_id', 'nombre', 'descripcion',
                    'estadoFisico', 'precio', 'categoria', 'estado',
                    'imagenes', 'created_at', 'updated_at'
                ]
            ],
            'paginacion' => [
                'pagina_actual', 'elementos_por_pagina', 'ultima_pagina', 'elementos_totales'
            ]
        ]);

        $this->assertCount(15, $response->json('productos'));
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

        $producto = Producto::create([
            'guid' => 'guidProductoShow',
            'vendedor_id' => $cliente->id,
            'nombre' => 'Producto de prueba',
            'descripcion' => 'Este es un producto de prueba',
            'estadoFisico' => 'Nuevo',
            'precio' => 99.99,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
            'imagenes' => json_encode(['imagenProducto.png']),
        ]);

        Redis::set('producto_' . $producto->id, json_encode($producto));

        $response = $this->getJson("/api/productos/{$producto->id}");

        $response->assertStatus(200);

        $response->assertJson([
            'id' => $producto->id,
            'nombre' => $producto->nombre,
            'descripcion' => $producto->descripcion,
            'precio' => $producto->precio,
            'categoria' => $producto->categoria,
            'estado' => $producto->estado,
        ]);

        $productoEnRedis = json_decode(Redis::get('producto_' . $producto->id), true);
        $this->assertEquals($producto->id, $productoEnRedis['id']);
        $this->assertEquals($producto->nombre, $productoEnRedis['nombre']);
        $this->assertEquals($producto->descripcion, $productoEnRedis['descripcion']);
    }

    public function test_show_not_found(): void
    {
        $response = $this->getJson("/api/productos/9999");

        $response->assertStatus(404);

        $response->assertJson([
            'message' => 'Producto no encontrado'
        ]);
    }

    public function test_store(): void
    {
        $usuario = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('secret'),
            'role' => 'admin',
        ]);

        $cliente = Cliente::create([
            'guid' => Str::uuid(),
            'nombre' => 'pepe',
            'apellido' => 'perez',
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

        $data = [
            'guid' => 'guidProductoCreado',
            'vendedor_id' => $cliente->id,
            'nombre' => 'Producto de prueba',
            'descripcion' => 'Este es un producto de prueba',
            'estadoFisico' => 'Nuevo',
            'precio' => 99.99,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
            'imagenes' => ['imagenProducto.png'],
        ];

        $response = $this->postJson('/api/productos', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('productos', [
            'nombre' => 'Producto de prueba',
            'descripcion' => 'Este es un producto de prueba',
            'precio' => 99.99,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
        ]);

        $producto = Producto::first();
        $this->assertIsArray($producto->imagenes);
        $this->assertContains('imagenProducto.png', $producto->imagenes);
    }

    public function test_store_invalid(): void
    {
        $data = [
            'guid' => 'guidProductoInvalido',
            'vendedor_id' => 1,
            'descripcion' => 'Este es un producto sin nombre',
            'estadoFisico' => 'Nuevo',
            'precio' => 99.99,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
            'imagenes' => ['imagenProducto.png'],
        ];

        $response = $this->postJson('/api/productos', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['nombre']);
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

        $producto = Producto::create([
            'guid' => 'guidProductoUpdate',
            'vendedor_id' => $cliente->id,
            'nombre' => 'Producto de prueba',
            'descripcion' => 'Este es un producto de prueba',
            'estadoFisico' => 'Nuevo',
            'precio' => 99.99,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
            'imagenes' => json_encode(['imagenProducto.png']),
        ]);

        Redis::set('producto_' . $producto->id, json_encode($producto));

        $data = [
            'nombre' => 'Producto actualizado',
            'descripcion' => 'Este es un producto actualizado',
            'estadoFisico' => 'Nuevo',
            'precio' => 109.99,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
            'imagenes' => ['imagenProductoActualizada.png'],
        ];

        $response = $this->putJson("/api/productos/{$producto->id}", $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'nombre' => 'Producto actualizado',
            'descripcion' => 'Este es un producto actualizado',
            'precio' => 109.99,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
        ]);

        $productoActualizado = json_decode(Redis::get('producto_' . $producto->id), true);
        $this->assertEquals($data['nombre'], $productoActualizado['nombre']);
        $this->assertEquals($data['descripcion'], $productoActualizado['descripcion']);
        $this->assertEquals($data['precio'], (float) $productoActualizado['precio']);
        $imagenes = is_string($productoActualizado['imagenes'])
            ? json_decode($productoActualizado['imagenes'], true)
            : $productoActualizado['imagenes'];

        $this->assertEquals($data['imagenes'], $imagenes);
    }

    public function test_update_not_found(): void
    {
        $response = $this->putJson("/api/productos/9999", [
            'nombre' => 'Producto inexistente',
            'descripcion' => 'Este producto no existe',
            'estadoFisico' => 'Nuevo',
            'precio' => 99.99,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
            'imagenes' => ['imagenProductoInexistente.png'],
        ]);

        $response->assertStatus(404);

        $response->assertJson([
            'message' => 'Producto no encontrado'
        ]);
    }

    public function test_update_invalid(): void
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

        $producto = Producto::create([
            'guid' => 'guidProductoInvalidUpdate',
            'vendedor_id' => $cliente->id,
            'nombre' => 'Producto de prueba',
            'descripcion' => 'Este es un producto de prueba',
            'estadoFisico' => 'Nuevo',
            'precio' => 99.99,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
            'imagenes' => json_encode(['imagenProducto.png']),
        ]);

        $response = $this->putJson("/api/productos/{$producto->id}", [
            'nombre' => '',
            'descripcion' => 'Descripción válida',
            'estadoFisico' => 'Nuevo',
            'precio' => -1,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
            'imagenes' => ['imagenProductoInvalida.png'],
        ]);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'errors' => [
                'nombre',
                'precio',
            ],
        ]);
    }

    public function test_destroy(): void
    {
        $usuario = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('secret'),
            'role' => 'admin',
        ]);

        $cliente = Cliente::create([
            'guid' => Str::uuid(),
            'nombre' => 'pepe',
            'apellido' => 'perez',
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

        $producto = Producto::create([
            'guid' => 'guidProductoDestroy',
            'vendedor_id' => $cliente->id,
            'nombre' => 'Producto de prueba',
            'descripcion' => 'Este es un producto de prueba',
            'estadoFisico' => 'Nuevo',
            'precio' => 99.99,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
            'imagenes' => json_encode(['imagenProducto.png']),
        ]);

        Redis::set('producto_' . $producto->id, json_encode($producto->toArray()));

        $response = $this->deleteJson("/api/productos/{$producto->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('productos', [
            'id' => $producto->id,
        ]);

        $this->assertNull(Redis::get('producto_' . $producto->id));
    }

    public function test_destroy_not_found(): void
    {
        $response = $this->deleteJson("/api/productos/9999");

        $response->assertStatus(404);

        $response->assertJson([
            'message' => 'Producto no encontrado',
        ]);
    }

    public function test_add_listing_photo(): void
    {
        Storage::fake('public');

        $usuario = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('secret'),
            'role' => 'admin',
        ]);

        $cliente = Cliente::create([
            'guid' => Str::uuid(),
            'nombre' => 'pepe',
            'apellido' => 'perez',
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

        $producto = Producto::create([
            'guid' => 'guidProductoFoto',
            'vendedor_id' => $cliente->id,
            'nombre' => 'Producto Test',
            'descripcion' => 'Descripción de prueba',
            'estadoFisico' => 'Nuevo',
            'precio' => 99.99,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
            'imagenes' => [],
        ]);

        $file = UploadedFile::fake()->image('photo.jpg');

        $response = $this->postJson("/api/productos/{$producto->id}/upload", [
            'image' => $file,
        ]);

        $response->assertStatus(200);

        $filePath = "products/{$producto->guid}/" . $file->hashName();
        Storage::disk('public')->assertExists("products/{$producto->guid}/");

        $producto->refresh();

        $this->assertTrue(
            collect($producto->imagenes)->contains(function ($item) use ($producto) {
                return strpos($item, "products/{$producto->guid}/") === 0;
            }),
            "El archivo subido no está en el array de imágenes"
        );

        $response->assertJson([
            'message' => 'Foto añadida',
            'product' => [
                'id' => $producto->id,
                'imagenes' => $producto->imagenes,
            ]
        ]);
    }
}
