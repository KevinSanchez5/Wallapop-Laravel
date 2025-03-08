<?php

namespace Tests\Feature\View;

use App\Models\Cliente;
use App\Models\User;
use App\Models\Valoracion;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileControllerViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_show()
    {
        $user = User::factory()->create(['role' => 'cliente']);
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
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        Valoracion::factory()->create(['clienteValorado_id' => $cliente->id]);
        $this->actingAs($user);

        $response = $this->get(route('profile.reviews'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.partials.valoraciones');
    }

    public function test_show_reviews_no_autenticado()
    {
        $response = $this->get(route('profile.reviews'));
        $response->assertRedirect(route('login'));
    }

    public function test_show_orders()
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        Venta::factory()->create(['comprador' => json_encode(['id' => $cliente->id])]);
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
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        Venta::factory()->create([
            'lineaVentas' => json_encode([['vendedor' => ['id' => $cliente->id]]])
        ]);
        $this->actingAs($user);

        $response = $this->get(route('profile.sales'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.partials.mis-ventas');
    }

    public function test_show_sales_no_autenticado()
    {
        $response = $this->get(route('profile.sales'));
        $response->assertRedirect(route('login'));
    }

    public function test_show_sale()
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        $venta = Venta::factory()->create([
            'guid' => 'guidventa-1',
            'comprador' => json_encode(['id' => $cliente->id]),
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'id' => $cliente->id,
                        'nombre' => 'Juan',
                        'apellido' => 'Perez'
                    ]
                ]
            ]
        ]);
        $this->actingAs($user);

        $response = $this->get(route('sale.detail', ['guid' => $venta->guid])); // Usa 'sale.detail'

        $response->assertStatus(200);
        $response->assertViewIs('profile.ver-venta');
    }

    public function test_show_sale_no_autenticado()
    {
        $venta = Venta::factory()->create(['guid' => 'guidventa-1']);
        $response = $this->get(route('sale.detail', ['guid' => $venta->guid])); // Usa 'sale.detail'
        $response->assertRedirect(route('login'));
    }

    public function test_show_order()
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        $venta = Venta::factory()->create([
            'guid' => 'guidventa-1',
            'comprador' => json_encode(['id' => $cliente->id])
        ]);
        $this->actingAs($user);

        $response = $this->get(route('order.detail', ['guid' => $venta->guid]));

        $response->assertStatus(200);
        $response->assertViewIs('profile.ver-pedido');
    }

    public function test_show_order_no_autenticado()
    {
        $venta = Venta::factory()->create(['guid' => 'guidventa-1']);
        $response = $this->get(route('order.detail', ['guid' => $venta->guid])); // Usa 'order.detail'
        $response->assertRedirect(route('login'));
    }

    public function test_show_favorites()
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->get(route('profile.favorites'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.partials.mis-favoritos');
    }

    public function test_show_favorites_no_autenticado()
    {
        $response = $this->get(route('profile.favorites'));
        $response->assertRedirect(route('login'));
    }

    public function test_edit()
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.edit');
    }

    // Test para mostrar el formulario de edición sin autenticación
    public function test_edit_no_autenticado()
    {
        $response = $this->get(route('profile.edit'));
        $response->assertRedirect(route('login'));
    }

    // Test para actualizar el perfil del usuario
    public function test_update()
    {
        $user = User::factory()->create(['role' => 'cliente']);
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

    // Test para actualizar el perfil con un avatar
    public function test_update_with_avatar()
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'cliente']);
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
            ],
            'avatar' => UploadedFile::fake()->image('avatar.jpg')
        ];

        $response = $this->put(route('profile.update'), $data);

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('success', 'Perfil actualizado correctamente.');

        $cliente->refresh();
        $this->assertNotNull($cliente->avatar);
    }

    public function test_update_validation_error()
    {
        $user = User::factory()->create([
            'role' => 'cliente'
        ]);
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        $this->actingAs($user);

        $data = [
            'nombre' => '',
            'apellidos' => '',
            'telefono' => '123',
            'direccion' => [
                'calle' => '',
                'numero' => 'not a number',
                'codigoPostal' => 'not a number'
            ]
        ];

        $response = $this->put(route('profile.update'), $data);

        $response->assertSessionHasErrors([
            'nombre', 'apellidos', 'telefono', 'direccion.calle', 'direccion.numero', 'direccion.codigoPostal'
        ]);
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

    public function test_delete_no_autenticado()
    {
        $response = $this->delete(route('profile'));
        $response->assertRedirect(route('login'));
    }

    public function test_cambio_contrasenya()
    {
        $user = User::factory()->create([
            'role' => 'cliente',
            'password' => Hash::make('oldPassword')
        ]);
        $this->actingAs($user);

        $data = [
            'email' => $user->email,
            'oldPassword' => 'oldPassword',
            'newPassword' => 'newPassword123!',
            'confirmPassword' => 'newPassword123!'
        ];

        $response = $this->patch(route('profile.change.password'), $data);

        $response->assertJson(['success' => true]);
        $this->assertTrue(Hash::check('newPassword123!', $user->fresh()->password));
    }

    public function test_cambio_contrasenya_validation_error()
    {
        $user = User::factory()->create([
            'role' => 'cliente',
            'password' => Hash::make('oldPassword')
        ]);
        $this->actingAs($user);

        $data = [
            'email' => $user->email,
            'oldPassword' => 'wrongPassword',
            'newPassword' => 'newPassword123!',
            'confirmPassword' => 'newPassword123!'
        ];

        $response = $this->patch(route('profile.change.password'), $data);

        $response->assertJson(['success' => false]);
    }

    public function test_eliminar_perfil()
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $this->actingAs($user);

        $response = $this->delete(route('profile.destroy.profile')); // Usa 'profile.destroy.profile'

        $response->assertRedirect('/');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_find_user_by_email()
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $this->actingAs($user);

        $response = $this->get(route('profile.find-user', ['email' => $user->email]));

        $response->assertJson(['id' => $user->id]);
    }

    public function test_find_user_by_email_not_found()
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $this->actingAs($user);

        $response = $this->get(route('profile.find-user', ['email' => 'nonexistent@example.com']));

        $response->assertJson([]);
    }

    public function test_destroy()
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $this->actingAs($user);

        $response = $this->delete(route('profile.destroy.profile'));

        $response->assertRedirect('/');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_destroy_no_autenticado()
    {
        $response = $this->delete(route('profile.destroy.profile'));
        $response->assertRedirect(route('login'));
    }

    public function test_show_filtered_orders()
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        $this->actingAs($user);

        Venta::factory()->create([
            'comprador' => json_encode(['id' => $cliente->id]),
            'estado' => 'Pendiente',
        ]);
        Venta::factory()->create([
            'comprador' => json_encode(['id' => $cliente->id]),
            'estado' => 'Entregado',
        ]);

        $response = $this->get(route('profile.orders.search', ['estado' => 'Pendiente']));

        $response->assertStatus(200);
        $response->assertViewIs('profile.partials.mis-pedidos');
        $response->assertViewHas('pedidos', function ($pedidos) {
            return $pedidos->every(function ($pedido) {
                return $pedido->estado === 'Pendiente';
            });
        });
    }

    public function test_show_filtered_orders_no_autenticado()
    {
        $response = $this->get(route('profile.orders.search', ['estado' => 'Pendiente']));
        $response->assertRedirect(route('login'));
    }

    public function test_show_filtered_sales()
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        $this->actingAs($user);

        // Crear ventas con diferentes estados
        Venta::factory()->create([
            'lineaVentas' => json_encode([['vendedor' => ['id' => $cliente->id]]]),
            'estado' => 'Pendiente',
        ]);
        Venta::factory()->create([
            'lineaVentas' => json_encode([['vendedor' => ['id' => $cliente->id]]]),
            'estado' => 'Entregado',
        ]);

        // Filtrar por estado 'Pendiente'
        $response = $this->get(route('profile.sales.search', ['estado' => 'Pendiente']));

        $response->assertStatus(200);
        $response->assertViewIs('profile.partials.mis-ventas');
        $response->assertViewHas('ventas', function ($ventas) {
            return $ventas->every(function ($venta) {
                return $venta->estado === 'Pendiente';
            });
        });
    }

    public function test_show_filtered_sales_no_autenticado()
    {
        $response = $this->get(route('profile.sales.search', ['estado' => 'Pendiente']));
        $response->assertRedirect(route('login'));
    }
}
