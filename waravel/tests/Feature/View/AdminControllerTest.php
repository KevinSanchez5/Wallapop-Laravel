<?php

namespace Tests\Feature\View;

use App\Http\Controllers\Views\AdminController;
use App\Models\User;
use App\Models\Producto;
use App\Models\Valoracion;
use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Mockery;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin); // Autenticar al usuario administrador
    }

    protected function tearDown(): void
    {
        Mockery::close(); // Limpiar mocks
        parent::tearDown();
    }

    public function test_dashboard()
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    public function test_create_backup()
    {
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
        $producto = Producto::factory()->create();
        $response = $this->patch(route('admin.ban.product', $producto->guid));
        $response->assertRedirect(route('admin.products'));
    }

    public function test_delete_review()
    {
        $valoracion = Valoracion::factory()->create();
        $response = $this->delete(route('admin.delete.review', $valoracion->guid));
        $response->assertRedirect(route('admin.reviews'));
        $this->assertDatabaseMissing('valoraciones', ['guid' => $valoracion->guid]);
    }

    public function test_delete_client()
    {
        $cliente = Cliente::factory()->create();
        $response = $this->delete(route('admin.delete.client', $cliente->guid));
        $response->assertRedirect(route('admin.clients'));
    }

    public function test_delete_admin()
    {
        $newAdmin = User::factory()->create(['role' => 'admin']);
        $response = $this->delete(route('admin.delete', $newAdmin->id));
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_dashboard_puntuaciones()
    {
        Valoracion::factory()->create(['puntuacion' => 5]);
        Valoracion::factory()->create(['puntuacion' => 5]);
        Valoracion::factory()->create(['puntuacion' => 4]);
        Valoracion::factory()->create(['puntuacion' => 3]);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $puntuaciones = [1 => 0, 2 => 0, 3 => 1, 4 => 1, 5 => 2];
        $response->assertViewHas('puntuaciones', $puntuaciones);
    }

    /*public function test_create_backup_fails()
    {
        Process::shouldReceive('run')->once()->andReturn(1);

        $response = $this->get(route('admin.backup'));
        $response->assertRedirect()
            ->assertSessionHas('error', 'Error al crear el backup de la base de datos');
    }

    public function test_create_backup_zip_fails()
    {
        $this->mock(AdminController::class, function ($mock) {
            $mock->shouldReceive('createBackup')->andReturn(response()->json(['error' => 'Error al crear el archivo ZIP'], 500));
        });

        $response = $this->get(route('admin.backup'));
        $response->assertRedirect()
            ->assertSessionHas('error', 'Error al crear el archivo ZIP');
    }*/

    public function test_backup_database_fails()
    {
        $this->mock(AdminController::class, function ($mock) {
            $mock->shouldReceive('backupDatabase')->andReturn(redirect()->back()->with('error', 'Error al crear el backup.'));
        });

        $response = $this->get(route('admin.backup'));
        $response->assertRedirect()
            ->assertSessionHas('error', 'Error al crear el backup.');
    }

    public function test_list_clients_with_search()
    {
        $user = User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        $cliente = Cliente::factory()->create(['nombre'=>'John Doe','usuario_id' => $user->id]);

        $response = $this->get(route('admin.clients', ['search' => 'John']));
        $response->assertStatus(200)
            ->assertSee('John Doe');
    }

    public function test_list_reviews_with_search()
    {
        $clienteValorado = Cliente::factory()->create(['nombre' => 'John Doe']);
        $valoracion = Valoracion::factory()->create(['clienteValorado_id' => $clienteValorado->id, 'comentario' => 'Excelente servicio']);

        $response = $this->get(route('admin.reviews', ['search' => 'John']));
        $response->assertStatus(200)
            ->assertSee('John Doe');
    }

    public function test_list_products_with_search()
    {
        $producto = Producto::factory()->create(['nombre' => 'Producto de prueba']);

        $response = $this->get(route('admin.products', ['search' => 'prueba']));
        $response->assertStatus(200)
            ->assertSee('Producto de prueba');
    }

    public function test_store_admin_max_limit()
    {
        User::factory()->count(10)->create(['role' => 'admin']);

        $response = $this->post(route('admin.store'), [
            'name' => 'New Admin',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Demasiados administradores. No se pueden agregar más.']);
    }

    public function test_ban_product_not_found()
    {
        $response = $this->patch(route('admin.ban.product', 'non-existent-guid'));
        $response->assertRedirect(route('admin.products'))
            ->assertSessionHas('error', 'Producto no encontrado');
    }

    public function test_delete_review_not_found()
    {
        $response = $this->delete(route('admin.delete.review', 'non-existent-guid'));
        $response->assertRedirect(route('admin.reviews'))
            ->assertSessionHas('error', 'Valoración no encontrada.');
    }

    public function test_delete_client_not_found()
    {
        $response = $this->delete(route('admin.delete.client', 'non-existent-guid'));
        $response->assertRedirect(route('profile'))
            ->assertSessionHas('error', 'Cliente no encontrado');
    }
}
