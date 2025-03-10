<?php

namespace Tests\Feature\View;

use App\Models\User;
use App\Models\Producto;
use App\Models\Valoracion;
use App\Models\Cliente;
use App\Models\Venta;
use App\Utils\GuidGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin);
    }

    public function test_dashboard()
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    public function test_create_backup()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->actingAs($user);

        Storage::fake('local');

        $response = $this->get(route('admin.backup'));
        $response->assertStatus(200);
    }


    public function test_list_backups()
    {
        Storage::fake('local');
        $response = $this->get(route('admin.backups.list'));
        $response->assertStatus(200);
    }


    public function test_list_clients()
    {
        $response = $this->get(route('admin.clients'));
        $response->assertStatus(200);
    }

    public function test_list_reviews()
    {
        $response = $this->get(route('admin.reviews'));
        $response->assertStatus(200);
    }

    public function test_list_products()
    {
        $response = $this->get(route('admin.products'));
        $response->assertStatus(200);
    }

    public function test_store_admin()
    {
        $response = $this->post(route('admin.store'), [
            'name' => 'New Admin',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(201);
    }

    public function test_ban_product()
    {

        $usuario = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
        ]);

        $cliente = Cliente::create([
            'guid' => GuidGenerator::generarId(),
            'nombre' => 'Jettie',
            'apellido' => 'Weissnat',
            'avatar' => 'https://via.placeholder.com/640x480.png/00ddee?text=aut',
            'telefono' => '+17434213369',
            'direccion' => json_encode(['calle' => '843 Koepp Prairie Suite 306', 'ciudad' => 'West Devin', 'pais' => 'Saint Kitts and Nevis']),
            'activo' => true,
            'usuario_id' =>$usuario->id,
        ]);


        $producto =Producto::create([
            'guid' => GuidGenerator::generarId(),
            'vendedor_id' => $cliente->id,
            'nombre' => 'Producto de Prueba',
            'descripcion' => 'Descripción del producto',
            'estadoFisico' => 'Nuevo',
            'precio' => 100.00,
            'stock' => 50,
            'categoria' => 'Cocina',
            'estado' => 'Disponible',
            'imagenes' => json_encode(['imagen1.jpg', 'imagen2.jpg']),
        ]);


        $response = $this->patch(route('admin.ban.product', $producto->guid));
        $response->assertRedirect(route('admin.products'));
    }


    public function test_delete_review()
    {

        $clienteValorado = Cliente::factory()->create([
            'id' => 2,
            'guid' => GuidGenerator::generarId(),
        ]);

        $autor = Cliente::factory()->create([
            'id' => 3,
            'guid' => GuidGenerator::generarId(),
        ]);


        $valoracion = Valoracion::create([
            'guid' =>GuidGenerator::generarId(),
            'comentario' => 'Excelente vendedor, muy amable.',
            'puntuacion' => 5,
            'clienteValorado_id' => $clienteValorado->id,
            'autor_id' => $autor->id,
        ]);

        $response = $this->delete(route('admin.delete.review', $valoracion->guid));
        $response->assertRedirect(route('admin.reviews'));

        $this->assertDatabaseMissing('valoraciones', ['guid' => $valoracion->guid]);
    }



    public function test_delete_client()
    {
        $usuario = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
        ]);

        $cliente = Cliente::create([
            'guid' => GuidGenerator::generarId(),
            'nombre' => 'Jettie',
            'apellido' => 'Weissnat',
            'avatar' => 'https://via.placeholder.com/640x480.png/00ddee?text=aut',
            'telefono' => '+17434213369',
            'direccion' => json_encode(['calle' => '843 Koepp Prairie Suite 306', 'ciudad' => 'West Devin', 'pais' => 'Saint Kitts and Nevis']),
            'activo' => true,
            'usuario_id' =>$usuario->id,
        ]);

        $response = $this->delete(route('admin.delete.client', $cliente->guid));
        $response->assertRedirect(route('admin.clients'));
    }

    public function test_delete_admin()
    {
        $newAdmin = User::factory()->create(['role' => 'admin']);

        $response = $this->delete(route('admin.delete', $newAdmin->id));
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function testUpdateStatusVentasSuccessful()
    {
        Venta::factory()->create(['estado' => 'Pendiente']);
        Venta::factory()->create(['estado' => 'Procesando']);
        Venta::factory()->create(['estado' => 'Enviado']);
        Venta::factory()->create(['estado' => 'Entregado']);

        Log::spy();

        $response = $this->post(route('admin.update.ventas'));

        Log::shouldHaveReceived('info')->with('Iniciando actualiación masiva de ventas')->once();
        Log::shouldHaveReceived('info')->with('Finalizando actualización masiva de ventas')->once();

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Los estados de las ventas se han actualizado correctamente.');

        $this->assertDatabaseHas('ventas', ['estado' => 'Procesando']);
        $this->assertDatabaseHas('ventas', ['estado' => 'Enviado']);
        $this->assertDatabaseHas('ventas', ['estado' => 'Entregado']);
        $this->assertDatabaseCount('ventas', 4); // Verificar que hay 4 registros en total
    }
}
