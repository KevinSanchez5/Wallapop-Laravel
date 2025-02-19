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



}
