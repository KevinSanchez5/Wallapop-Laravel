<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase; // Limpia la BD entre pruebas

    #[Test]
    public function admin_puede_acceder_a_ruta_protegida()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/reviews');

        $response->assertStatus(200); // Debe permitir acceso
    }

    #[Test]
    public function usuario_normal_no_puede_acceder_a_ruta_admin()
    {
        $user = User::factory()->create(['role' => 'cliente']);

        $response = $this->actingAs($user)->get('/admin/reviews');

        $response->assertStatus(403); // Debe bloquear acceso
    }

    #[Test]
    public function cliente_puede_acceder_a_ruta_cliente()
    {
        $cliente = User::factory()->create(['role' => 'cliente']);
        $client = Cliente::factory()->create(['usuario_id' => $cliente->id]);

        $response = $this->actingAs($cliente)->get('/profile');

        $response->assertStatus(200); // Debe permitir acceso
    }

    #[Test]
    public function admin_no_puede_acceder_a_ruta_cliente()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/profile');

        $response->assertStatus(403); // Debe bloquear acceso
    }
}
