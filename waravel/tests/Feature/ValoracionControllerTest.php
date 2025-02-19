<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use App\Models\Valoracion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Tests\TestCase;

class ValoracionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $usuario;
    protected $cliente;
    protected $usuario2;
    protected $cliente2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usuario = User::create([
            'name'     => 'usuario',
            'email'    => 'usuario@example.com',
            'password' => bcrypt('secret'),
            'role'     => 'cliente',
        ]);

        $this->cliente = Cliente::create([
            'guid'      => 'cliente-guid',
            'nombre'    => 'Pepe',
            'apellido'  => 'Perez',
            'avatar'    => 'avatar.png',
            'telefono'  => '123456789',
            'direccion' => json_encode([
                'calle'         => 'Avenida Siempre Viva',
                'numero'        => 742,
                'piso'          => 1,
                'letra'         => 'A',
                'codigoPostal'  => 28001,
            ]),
            'activo'     => true,
            'usuario_id' => $this->usuario->id,
        ]);

        $this->usuario2 = User::create([
            'name'     => 'usuario2',
            'email'    => 'usuario2@example.com',
            'password' => bcrypt('secret'),
            'role'     => 'cliente',
        ]);

        $this->cliente2 = Cliente::create([
            'guid'      => 'cliente2-guid',
            'nombre'    => 'Maria',
            'apellido'  => 'Garcia',
            'avatar'    => 'avatar.png',
            'telefono'  => '987654321',
            'direccion' => json_encode([
                'calle'         => 'Avenida Test',
                'numero'        => 456,
                'piso'          => 2,
                'letra'         => 'B',
                'codigoPostal'  => 28990,
            ]),
            'activo'     => true,
            'usuario_id' => $this->usuario2->id,
        ]);
    }


    public function test_index(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            Valoracion::create([
                'guid' => 'guid-' . $i,
                'comentario' => 'Comentario de la valoracion' . $i,
                'puntuacion' => 4,
                'clienteValorado_id' => $this->cliente->id,
                'autor_id' => $this->cliente2->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $response = $this->getJson('/api/valoraciones');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'valoraciones' => [
                '*' => [
                    'id', 'guid', 'comentario', 'puntuacion', 'clienteValorado_id',
                    'autor_id', 'created_at', 'updated_at'
                ]
            ],
            'paginacion' => [
                'pagina_actual', 'elementos_por_pagina', 'ultima_pagina', 'elementos_totales'
            ]
        ]);

        $this->assertCount(5, $response->json('valoraciones'));
    }

    public function test_show(): void
    {
        $valoracion = Valoracion::create([
            'guid' => 'guid-1',
            'comentario' => 'Comentario de la valoracion',
            'puntuacion' => 5,
            'clienteValorado_id' => $this->cliente->id,
            'autor_id' => $this->cliente2->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Redis::set('valoracion_' . $valoracion->id, json_encode($valoracion));

        $response = $this->getJson("/api/valoraciones/{$valoracion->id}");

        $response->assertStatus(200);

        $response->assertJson([
            'id' => $valoracion->id,
            'guid' => $valoracion->guid,
            'comentario' => $valoracion->comentario,
            'puntuacion' => $valoracion->puntuacion,
            'clienteValorado_id' => $valoracion->clienteValorado_id,
            'autor_id' => $valoracion->autor_id,
        ]);

        $valoracionRedis = json_decode(Redis::get('valoracion_' . $valoracion->id), true);
        $this->assertEquals($valoracion->id, $valoracionRedis['id']);
        $this->assertEquals($valoracion->guid, $valoracionRedis['guid']);
        $this->assertEquals($valoracion->comentario, $valoracionRedis['comentario']);
        $this->assertEquals($valoracion->puntuacion, $valoracionRedis['puntuacion']);
        $this->assertEquals($valoracion->clienteValorado_id, $valoracionRedis['clienteValorado_id']);
        $this->assertEquals($valoracion->autor_id, $valoracionRedis['autor_id']);
    }

    public function test_show_not_found(): void
    {
        $response = $this->getJson("/api/valoraciones/999");

        $response->assertStatus(404);

        $response->assertJson([
            'message' => 'Valoracion no encontrada'
        ]);
    }

    public function test_store(): void
    {
        $data = [
            'guid' => 'guid-1',
            'comentario' => 'Comentario de la valoracion',
            'puntuacion' => 5,
            'clienteValorado_id' => $this->cliente->id,
            'autor_id' => $this->cliente2->id
        ];

        $response = $this->postJson('/api/valoraciones', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('valoraciones', [
            'comentario' => 'Comentario de la valoracion',
            'puntuacion' => 5
        ]);
    }

    public function test_store_invalid(): void
    {
        $data = [
            'guid' => 'guid-1',
            'comentario' => 'Comentario de la valoracion',
            'puntuacion' => 7,
            'clienteValorado_id' => $this->cliente->id,
            'autor_id' => $this->cliente2->id
        ];

        $response = $this->postJson('/api/valoraciones', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['puntuacion']);
    }

    public function test_destroy(): void
    {
        $valoracion = Valoracion::create([
            'guid' => 'guid-1',
            'comentario' => 'Comentario de la valoracion',
            'puntuacion' => 5,
            'clienteValorado_id' => $this->cliente->id,
            'autor_id' => $this->cliente2->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Redis::set('valoracion_' . $valoracion->id, json_encode($valoracion));

        $response = $this->deleteJson("/api/valoraciones/{$valoracion->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('valoraciones', [
            'id' => $valoracion->id,
        ]);

        $this->assertNull(Redis::get('valoracion_' . $valoracion->id));
    }

    public function test_destroy_not_found(): void
    {
        $response = $this->deleteJson("/api/valoraciones/999");

        $response->assertStatus(404);

        $response->assertJson([
            'message' => 'Valoracion no encontrada',
        ]);
    }
}
