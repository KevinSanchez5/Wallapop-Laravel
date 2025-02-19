<?php

namespace Tests\Http\Controllers;

use App\Http\Controllers\ClienteController;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class ClienteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_paginated_clients()
    {
        $user = User::create([
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
            'usuario_id' => $user->id,
        ]);


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
