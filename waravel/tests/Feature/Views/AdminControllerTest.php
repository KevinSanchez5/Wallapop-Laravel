<?php

namespace Tests\Feature\Views;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use App\Models\Valoracion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_muestra_los_datos_correctos()
    {
        User::factory()->count(5)->create(['role' => 'cliente']);
        Producto::factory()->count(10)->create();
        Valoracion::factory()->count(15)->create();

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas(['totalUsers', 'totalProducts', 'puntuaciones', 'admins', 'latestProducts', 'latestClients']);
    }

    public function test_backupDatabase_crea_y_descarga_el_respaldo()
    {
        Storage::fake('local');

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Mock the storage and exec functions to simulate backup creation
        Storage::shouldReceive('path')
            ->once()
            ->andReturn(storage_path('app/backups/backup_test.sql'));

        $response = $this->get(route('admin.backup'));
        $response->assertStatus(200);
    }

    public function test_listClients_devuelve_clientes_correctamente()
    {
        Cliente::factory()->count(10)->create();

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.clients'));
        $response->assertStatus(200);
        $response->assertViewHas('clientes');
    }

    public function test_listReviews_filtra_correctamente()
    {
        Valoracion::factory()->count(10)->create();

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.reviews', ['search' => 'bueno']));
        $response->assertStatus(200);
        $response->assertViewHas('valoraciones');
    }

    public function test_store_crea_administrador_correctamente()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.store'), [
            'name' => 'Nuevo Admin',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'admin@example.com']);
    }

    public function test_banProduct_cambia_estado_producto()
    {
        $producto = Producto::factory()->create(['estado' => 'Disponible']);

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->patch(route('admin.ban.product', $producto->guid));
        $response->assertRedirect(route('admin.products'));
        $this->assertDatabaseHas('productos', ['guid' => $producto->guid, 'estado' => 'Baneado']);
    }
}

