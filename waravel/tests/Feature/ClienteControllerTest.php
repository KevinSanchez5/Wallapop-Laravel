<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
