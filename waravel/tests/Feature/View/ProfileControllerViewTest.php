<?php

namespace Tests\Feature\View;

use App\Models\Cliente;
use App\Models\User;
use App\Models\Valoracion;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileControllerViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_show()
    {
        $user = User::factory()->create([
            'role' => 'cliente',
        ]);
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->get(route('profile'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.partials.mis-productos');

    }

    public function test_show_no_autenticado()
    {
        $response = $this->get(route('profile'));
        $response->assertRedirect(route('login'));
    }

    public function test_show_reviews()
    {
        $user = User::factory()->create([
            'role' => 'cliente',
        ]);
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        $valoracion = Valoracion::create([
            'guid' => 'guidvalorac',
            'comentario' => 'comentarios',
            'puntuacion' => 4,
            'clienteValorado_id' => $cliente->id,
            'autor_id' => $cliente->id
        ]);
        $this->actingAs($user);

        $response = $this->get(route('profile.reviews'));
        $response->assertViewIs('profile.partials.valoraciones');
    }

    public function test_show_reviews_no_autenticado()
    {
        $response = $this->get(route('profile.reviews'));
        $response->assertRedirect(route('login'));
    }

    public function test_show_orders()
    {
        $user = User::factory()->create([
            'role' => 'cliente'
        ]);
        $cliente = Cliente::create([
            'nombre' => 'John',
            'apellido' => 'Doe',
            'avatar' => 'https://via.placeholder.com/640x480.png/00ddee?text=aut',
            'telefono' => '+17434213369',
            'direccion' => json_encode(['calle' => '456 Calle Verde', 'ciudad' => 'Verde', 'pais' => 'Verdeland']),
            'activo' => true,
            'usuario_id' => $user->id,
        ]);
        $venta = Venta::create([
            'guid' => 'guidventa-1',
            'comprador' => json_encode([
                'guid'=>'DU6jCZtareb',
                'id' => $cliente->id,
                'nombre' => 'John',
                'apellido' => 'Doe'
            ]),
            'estado' => 'Pendiente',
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid'=>'2G6HueqixE5',
                        'id' => 1,
                        'nombre' => 'Juan',
                        'apellido' => 'Perez'
                    ],
                    'cantidad' => 2,
                    'producto' => [
                        'guid'=>'G4YXT9K5QLV',
                        'id' => 1,
                        'nombre' => 'Portatil Gamer',
                        'imagenes' => ['productos/portatil1.webp', 'productos/portatil2.webp'],
                        'descripcion' => 'Portatil gaming de gama alta para trabajos pesados.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 800.00,
                        'categoria' => 'Tecnologia'
                    ],
                    'precioTotal' => 2 * 800.00,
                ]
            ],
            'precioTotal' => 1600.00,
        ]);
        $this->actingAs($user);

        $response = $this->get(route('profile.orders'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.partials.mis-pedidos');
    }

    public function test_show_orders_no_autenticado()
    {
        $response = $this->get(route('profile.orders'));
        $response->assertRedirect(route('login'));
    }

    public function test_show_sales()
    {
        $user = User::factory()->create([
            'role' => 'cliente',
        ]);
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        $venta = Venta::factory()->create([
            'guid' => 'guidventa-1',
            'estado' => 'Pendiente'
        ]);
        $this->actingAs($user);

        $response = $this->get(route('profile.sales'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.partials.mis-ventas');
    }

    public function test_update()
    {
        $user = User::factory()->create([
            'role' => 'cliente'
        ]);
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        $this->actingAs($user);

        $data = [
            'nombre' => 'Nuevo Nombre',
            'apellidos' => 'Nuevo Apellido',
            'telefono' => '612345678',
            'direccion' => [
                'calle' => 'Avenida Siempre Viva',
                'numero' => 742,
                'piso' => 1,
                'letra' => 'A',
                'codigoPostal' => 28001
            ]
        ];

        $response = $this->put(route('profile.update'), $data);

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('success', 'Perfil actualizado correctamente.');

        $cliente->refresh();
        $this->assertEquals('Nuevo Nombre', $cliente->nombre);
        $this->assertEquals('Nuevo Apellido', $cliente->apellido);
    }

    public function test_delete()
    {
        $user = User::factory()->create([
            'role' => 'cliente'
        ]);
        $this->actingAs($user);

        $response = $this->delete(route('profile'));

        $response->assertRedirect('/');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
