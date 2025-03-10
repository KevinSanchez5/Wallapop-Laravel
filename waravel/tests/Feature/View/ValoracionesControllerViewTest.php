<?php

namespace Tests\Feature\View;

use App\Models\Cliente;
use App\Models\User;
use App\Models\Valoracion;
use App\Models\Venta;
use App\Utils\GuidGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

class ValoracionesControllerViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_write_review_redirects_if_not_logged_in()
    {
        $response = $this->get(route('write.review', 'some-guid'));

        $response->assertRedirect(route('login'));
    }

    public function test_write_review_for_non_existing_client_profile()
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $this->actingAs($user);

        $response = $this->get(route('write.review', 'some-guid'));

        $response->assertRedirect(route('pages.home'));
        $response->assertSessionHas('error', 'No se ha encontrado el perfil del cliente.');
    }

    public function test_write_review_for_non_existent_order()
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->get(route('write.review', 'some-guid'));

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('error', 'No se ha encontrado el pedido.');
    }

    public function test_write_review_for_order_not_belonging_to_client()
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        $venta = Venta::create([
            'guid' => GuidGenerator::generarId(),
            'comprador' => [
                'guid'=>'DU6jCZtareb',
                'id' => Cliente::factory()->create()->id,
                'nombre' => 'John',
                'apellido' => 'Doe'
            ],
            'estado' => 'Pendiente',
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid'=>'2G6HueqixE5',
                        'id' => Cliente::factory()->create()->id,
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

        $response = $this->get(route('write.review',$venta->guid));

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('error', 'No tienes permisos para ver este pedido.');
    }

    public function test_write_review_if_already_reviewed()
    {
        $user = User::factory()->create([
            'role' => 'cliente'
        ]);
        $cliente = Cliente::factory()->create([
            'usuario_id' => $user->id,
        ]);
        $venta = Venta::create([
            'guid' => GuidGenerator::generarId(),
            'comprador' => [
                'guid'=>'DU6jCZtareb',
                'id' => $cliente->id,
                'nombre' => 'John',
                'apellido' => 'Doe'
            ],
            'estado' => 'Pendiente',
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid'=>'2G6HueqixE5',
                        'id' => Cliente::factory()->create()->id,
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
        Valoracion::factory()->create(['venta_id' => $venta->id, 'autor_id' => $cliente->id]);
        $this->actingAs($user);

        $response = $this->get(route('write.review',$venta->guid));

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('error', 'Ya has realizado una valoración en este pedido.');
    }

    public function test_write_review_successful()
    {
        $user = User::factory()->create([
            'role' => 'cliente'
        ]);
        $cliente = Cliente::factory()->create([
            'usuario_id' => $user->id,
        ]);
        $venta = Venta::create([
            'guid' => GuidGenerator::generarId(),
            'comprador' => [
                'guid'=>'DU6jCZtareb',
                'id' => $cliente->id,
                'nombre' => 'John',
                'apellido' => 'Doe'
            ],
            'estado' => 'Pendiente',
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid'=>'2G6HueqixE5',
                        'id' => Cliente::factory()->create()->id,
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

        $response = $this->get(route('write.review',$venta->guid));

        $response->assertStatus(200);
        $response->assertViewIs('pages.write-review');
    }

    public function test_store_review_redirect_if_not_logged_in()
    {
        $response = $this->post(route('save.review', ['guid' => 'some-guid']), [
            'comment' => 'Great product!',
            'rating' => 5
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_store_review_for_non_existent_order()
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->post(route('save.review', 'non-existent-guid'), [
            'comment' => 'Great product!',
            'rating' => 5
        ]);

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('error', 'No se ha encontrado el pedido.');
    }

    public function test_store_review_if_already_reviewed()
    {
        $user = User::factory()->create([
            'role' => 'cliente'
        ]);
        $cliente = Cliente::factory()->create([
            'usuario_id' => $user->id,
        ]);
        $venta = Venta::factory()->create([
            'guid' => GuidGenerator::generarId(),
            'comprador' => [
                'guid'=>'DU6jCZtareb',
                'id' => $cliente->id,
                'nombre' => 'John',
                'apellido' => 'Doe'
            ],
            'estado' => 'Pendiente',
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid'=>'2G6HueqixE5',
                        'id' => Cliente::factory()->create()->id,
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
        Valoracion::factory()->create(['venta_id' => $venta->id, 'autor_id' => $cliente->id]);
        $this->actingAs($user);

        $response = $this->post(route('save.review', $venta->guid), [
            'comment' => 'Great product!',
            'rating' => 5
        ]);

        $response->assertRedirect(route('order.detail', $venta->guid));
        $response->assertSessionHas('error', 'Ya has realizado una valoración en este pedido.');
    }

    public function test_store_review_for_order_not_delivered()
    {
        $user = User::factory()->create([
            'role' => 'cliente'
        ]);
        $cliente = Cliente::factory()->create([
            'usuario_id' => $user->id,
        ]);
        $venta = Venta::create([
            'guid' => GuidGenerator::generarId(),
            'comprador' => [
                'guid'=>'DU6jCZtareb',
                'id' => $cliente->id,
                'nombre' => 'John',
                'apellido' => 'Doe'
            ],
            'estado' => 'Pendiente',
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid'=>'2G6HueqixE5',
                        'id' => Cliente::factory()->create()->id,
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

        $response = $this->post(route('save.review', $venta->guid), [
            'comment' => 'Great product!',
            'rating' => 5
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'No puedes escribir una valoración en un pedido que no ha sido entregado.');
    }

    public function test_store_review_successful()
    {
        $user = User::factory()->create([
            'role' => 'cliente'
        ]);
        $cliente = Cliente::factory()->create([
            'usuario_id' => $user->id,
        ]);
        $venta = Venta::create([
            'guid' => GuidGenerator::generarId(),
            'comprador' => [
                'guid'=>'DU6jCZtareb',
                'id' => $cliente->id,
                'nombre' => 'John',
                'apellido' => 'Doe'
            ],
            'estado' => 'Entregado',
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid'=>'2G6HueqixE5',
                        'id' => Cliente::factory()->create()->id,
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

        $response = $this->post(route('save.review', $venta->guid), [
            'comment' => 'Great product!',
            'rating' => 5
        ]);

        $response->assertRedirect(route('order.detail', $venta->guid));
        $this->assertDatabaseHas('valoraciones', [
            'venta_id' => $venta->id,
            'comentario' => 'Great product!',
            'puntuacion' => 5,
        ]);
    }

    public function test_store_review_invalid_data()
    {
        $user = User::factory()->create([
            'role' => 'cliente'
        ]);
        $cliente = Cliente::factory()->create([
            'usuario_id' => $user->id,
        ]);
        $venta = Venta::create([
            'guid' => GuidGenerator::generarId(),
            'comprador' => [
                'guid'=>'DU6jCZtareb',
                'id' => $cliente->id,
                'nombre' => 'John',
                'apellido' => 'Doe'
            ],
            'estado' => 'Entregado',
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid'=>'2G6HueqixE5',
                        'id' => Cliente::factory()->create()->id,
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

        $response = $this->post(route('save.review', $venta->guid),[
            'comment' => 'Too short',
            'rating' => 0
        ]);

        $response->assertRedirect(route('write.review', $venta->guid));
        $response->assertSessionHasErrors(['comment', 'rating']);
    }

    public function test_store_review_when_reviewer_is_not_the_buyer()
    {
        $user = User::factory()->create([
            'role' => 'cliente'
        ]);
        $cliente = Cliente::factory()->create([
            'usuario_id' => $user->id,
        ]);
        $venta = Venta::create([
            'guid' => GuidGenerator::generarId(),
            'comprador' => [
                'guid'=>'DU6jCZtareb',
                'id' => Cliente::factory()->create()->id,
                'nombre' => 'John',
                'apellido' => 'Doe'
            ],
            'estado' => 'Entregado',
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid'=>'2G6HueqixE5',
                        'id' => Cliente::factory()->create()->id,
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

        $response = $this->post(route('save.review', $venta->guid),[
            'comment' => 'Something something',
            'rating' => 1
        ]);

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('error', 'No tienes permisos para escribir una valoración en este pedido.');
    }

    public function test_store_review_when_client_doesnt_exist()
    {
        $user = User::factory()->create([
            'role' => 'cliente'
        ]);
        $this->actingAs($user);

        $response = $this->post(route('save.review', 'some-id'),[
            'comment' => 'Too short',
            'rating' => 0
        ]);

        $response->assertRedirect(route('pages.home'));
        $response->assertSessionHas('error', 'No se ha encontrado el cliente.');
    }
}
