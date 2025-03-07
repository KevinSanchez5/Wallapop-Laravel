<?php

namespace Tests\Feature\View;

use App\Models\User;
use App\Models\Producto;
use App\Models\Valoracion;
use App\Models\Cliente;
use App\Utils\GuidGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductoControllerViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_vista()
    {
        // Crear un usuario autenticado
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear algunos productos con vendedores diferentes
        $producto1 = Producto::factory()->create(['vendedor_id' => 1, 'estado' => 'Disponible']);
        $producto2 = Producto::factory()->create(['vendedor_id' => 2, 'estado' => 'Disponible']);

        // Simular productos en la vista
        $response = $this->get(route('productos.index'));

        // Verificar que el estado de la respuesta es 200 y la vista correcta
        $response->assertStatus(200);
        $response->assertViewIs('pages.home');
        $response->assertViewHas('productos');  // Verificar que los productos están en la vista

        // Asegurarse de que el producto del vendedor autenticado se excluye
        $response->assertDontSee($producto1->nombre);
        $response->assertSee($producto2->nombre);
    }

    public function test_search()
    {
        // Crear un usuario autenticado
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear productos para buscar
        $producto1 = Producto::factory()->create(['estado' => 'Disponible', 'nombre' => 'Producto A', 'categoria' => 'Electrónica']);
        $producto2 = Producto::factory()->create(['estado' => 'Disponible', 'nombre' => 'Producto B', 'categoria' => 'Ropa']);
        $producto3 = Producto::factory()->create(['estado' => 'Disponible', 'nombre' => 'Producto C', 'categoria' => 'Electrónica']);

        // Realizar la búsqueda
        $response = $this->get(route('productos.search', ['search' => 'Producto A', 'categoria' => 'Electrónica']));

        // Verificar que el estado de la respuesta es 200 y la vista correcta
        $response->assertStatus(200);
        $response->assertViewIs('pages.home');

        // Asegurarse de que solo el producto de la categoría 'Electrónica' se muestre
        $response->assertSee($producto1->nombre);
        $response->assertSee($producto3->nombre);
        $response->assertDontSee($producto2->nombre);
    }
    public function test_show_vista()
    {
        // Crear un producto
        $producto = Producto::factory()->create();

        // Hacer la solicitud al producto
        $response = $this->get(route('productos.show', ['guid' => $producto->guid]));

        // Verificar que la respuesta sea correcta
        $response->assertStatus(200);
        $response->assertViewIs('pages.ver-producto');
        $response->assertViewHas('producto', $producto);
    }

    public function test_store()
    {
        // Crear un usuario autenticado
        $user = User::factory()->create();
        $this->actingAs($user);

        // Simular un cliente para el usuario
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);

        // Enviar datos para crear un producto
        $response = $this->post(route('productos.store'), [
            'nombre' => 'Nuevo Producto',
            'descripcion' => 'Descripción del producto',
            'estadoFisico' => 'Nuevo',
            'precio' => 100,
            'stock' => 10,
            'categoria' => 'Electrónica',
            'imagen1' => UploadedFile::fake()->image('producto.jpg')
        ]);

        // Verificar que el producto fue creado y redirigido al perfil
        $response->assertRedirect(route('profile'));
        $this->assertDatabaseHas('productos', ['nombre' => 'Nuevo Producto']);
    }

    public function test_edit()
    {
        // Crear un usuario autenticado
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear un producto
        $producto = Producto::factory()->create(['vendedor_id' => $user->id]);

        // Hacer la solicitud al formulario de edición
        $response = $this->get(route('productos.edit', ['guid' => $producto->guid]));

        // Verificar que la respuesta sea correcta
        $response->assertStatus(200);
        $response->assertViewIs('profile.edit-producto');
        $response->assertViewHas('producto', $producto);
    }


    public function test_update()
    {
        // Crear un usuario autenticado
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear un producto
        $producto = Producto::factory()->create(['vendedor_id' => $user->id]);

        // Enviar datos para actualizar el producto
        $response = $this->put(route('productos.update', ['guid' => $producto->guid]), [
            'nombre' => 'Producto Actualizado',
            'descripcion' => 'Descripción actualizada',
            'estadoFisico' => 'Usado',
            'precio' => 200,
            'stock' => 5,
            'categoria' => 'Hogar',
        ]);

        // Verificar que el producto fue actualizado
        $response->assertRedirect(route('profile'));
        $this->assertDatabaseHas('productos', ['nombre' => 'Producto Actualizado']);
    }

    public function test_destroy()
    {
        // Crear un usuario autenticado
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear un producto
        $producto = Producto::factory()->create(['vendedor_id' => $user->id]);

        // Hacer la solicitud para eliminar el producto
        $response = $this->delete(route('productos.destroy', ['guid' => $producto->guid]));

        // Verificar que el producto fue eliminado
        $response->assertRedirect(route('profile'));
        $this->assertDatabaseMissing('productos', ['guid' => $producto->guid]);
    }

    public function test_changestatus()
    {
        // Crear un usuario autenticado
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear un producto
        $producto = Producto::factory()->create(['vendedor_id' => $user->id, 'estado' => 'Disponible']);

        // Cambiar el estado del producto
        $response = $this->post(route('productos.changestatus', ['guid' => $producto->guid]));

        // Verificar que el estado haya cambiado
        $response->assertRedirect(route('profile'));
        $this->assertDatabaseHas('productos', ['guid' => $producto->guid, 'estado' => 'Desactivado']);
    }
}





