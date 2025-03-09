<?php

namespace Tests\Feature\View;

use App\Models\User;
use App\Models\Producto;
use App\Models\Cliente;
use App\Utils\GuidGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductoControllerViewTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $cliente;
    protected $producto1;
    protected $producto2;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->cliente = Cliente::factory()->create(['usuario_id' => $this->user->id]);

        $this->producto1 = Producto::factory()->create([
            'guid' => GuidGenerator::generarId(),
            'vendedor_id' => $this->cliente->id,
            'nombre' => 'Laptop Gamer',
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
        ]);

        $otroUser = User::factory()->create();
        $otroCliente = Cliente::factory()->create(['usuario_id' => $otroUser->id]);

        $this->producto2 = Producto::factory()->create([
            'guid' => GuidGenerator::generarId(),
            'vendedor_id' => $otroCliente->id,
            'nombre' => 'Arbol',
            'categoria' => 'Cocina',
            'estado' => 'Disponible',
        ]);
    }

    public function test_index_vista()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('pages.home'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.home');
        $response->assertViewHas('productos');

        $productosEnVista = $response->original->getData()['productos'];

        dump($productosEnVista->pluck('id')->toArray());

        $this->assertFalse($productosEnVista->contains('id', $this->producto1->id));
        $this->assertTrue($productosEnVista->contains('id', $this->producto2->id));
    }

    public function test_search()
    {
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        $this->actingAs($user);

        $productoUsuario = Producto::factory()->create([
            'vendedor_id' => $cliente->id,
            'nombre' => 'Laptop Gamer',
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible'
        ]);

        $otroUser = User::factory()->create();
        $otroCliente = Cliente::factory()->create(['usuario_id' => $otroUser->id]);

         Producto::factory()->create([
            'vendedor_id' => $otroCliente->id,
            'nombre' => 'LápTop Gámér',
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible'
        ]);

         Producto::factory()->create([
            'vendedor_id' => $otroCliente->id,
            'nombre' => 'Zapatillas Deportivas',
            'categoria' => 'Deporte',
            'estado' => 'Disponible'
        ]);

        $response = $this->get(route('pages.home', [
            'search' => 'laptop',
            'categoria' => 'Tecnologia'
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('pages.home');
        $response->assertViewHas('productos');

        $productosEnVista = $response->original->getData()['productos'];
        dump($productosEnVista->pluck('id', 'categoria')->toArray());

    }

    public function test_show_vista_con_cache()
    {
        $producto = Producto::factory()->create([
            'imagenes' => ['productos/portatil1.webp', 'productos/portatil2.webp'],
        ]);

        Cache::put("producto_{$producto->guid}", $producto, 60);

        $response = $this->get(route('producto.show', ['guid' => $producto->guid]));

        $response->assertStatus(200);
        $response->assertViewIs('pages.ver-producto');
        $response->assertViewHas('producto', $producto);
    }

    public function test_show_vista_sin_cache()
    {
        $producto = Producto::factory()->create([
            'imagenes' => ['productos/portatil1.webp', 'productos/portatil2.webp'],
        ]);

        Cache::forget("producto_{$producto->guid}");

        $response = $this->get(route('producto.show', ['guid' => $producto->guid]));

        $response->assertStatus(200);
        $response->assertViewIs('pages.ver-producto');
        $response->assertViewHas('producto', $producto);
    }

    public function test_show_vista_con_imagenes_vacias()
    {
        $producto = Producto::factory()->create([
            'imagenes' => [],
        ]);

        $response = $this->get(route('producto.show', ['guid' => $producto->guid]));

        $response->assertStatus(200);
        $response->assertViewIs('pages.ver-producto');
        $response->assertViewHas('producto', $producto);
    }

    public function test_show_vista_con_guid_invalido()
    {
        $this->actingAs($this->user);


        $invalidGuid = 'invalid-guid-12345';
        $response = $this->get(route('producto.show', ['guid' => $invalidGuid]));

        $response->assertStatus(302);
        $response->assertRedirect(route('pages.home'));
        $response->assertSessionHas('error', 'Producto no encontrado');
    }

    public function test_show_vista_con_guid_inexistente()
    {
        $this->actingAs($this->user);

        $nonExistingGuid = 'non-existing-guid';
        $response = $this->get(route('producto.show', ['guid' => $nonExistingGuid]));

        $response->assertStatus(302);
        $response->assertRedirect(route('pages.home'));
        $response->assertSessionHas('error', 'Producto no encontrado');
    }

    public function test_store()
    {
        $this->actingAs($this->user);

        $data = [
            'nombre' => 'Producto Test',
            'descripcion' => 'Descripción del producto',
            'estadoFisico' => 'Nuevo',
            'precio' => 100.00,
            'stock' => 10,
            'categoria' => 'Cocina',
            'imagen1' => UploadedFile::fake()->image('imagen1.jpg'),
            'imagen2' => UploadedFile::fake()->image('imagen2.jpg'),
        ];

        $response = $this->post(route('producto.store'), $data);

        $response->assertStatus(403);
        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('success', 'Producto añadido correctamente.');
    }

    public function test_it_redirects_to_login_if_not_authenticated()
    {
        $response = $this->post(route('producto.store'), []);

        $response->assertRedirect(route('login'));
    }

    public function test_update()
    {
        $user = User::factory()->create(['role' => 'cliente']);
        $this->actingAs($user);

        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        $producto = Producto::factory()->create(['vendedor_id' => $cliente->id]);

        $this->assertEquals($producto->vendedor_id, $cliente->id);

        $updatedData = [
            'nombre' => 'Producto Actualizado',
            'descripcion' => 'Descripción actualizada',
            'estadoFisico' => 'Usado',
            'precio' => 200,
            'stock' => 5,
            'categoria' => 'Hogar',
        ];

        $response = $this->put(route('producto.update', ['guid' => $producto->guid]), $updatedData);
        $response->assertRedirect(route('profile'));

        $this->assertDatabaseHas('productos', [
            'guid' => $producto->guid,
            'nombre' => 'Producto Actualizado',
            'descripcion' => 'Descripción actualizada',
            'estadoFisico' => 'Usado',
            'precio' => 200,
            'stock' => 5,
            'categoria' => 'Hogar',
        ]);

        $this->assertEquals($producto->vendedor_id, $cliente->id);
    }

    public function test_destroy()
    {

        $this->actingAs($this->user);

        $producto = $this->producto1;

        $response = $this->delete(route('producto.destroy', ['guid' => $producto->guid]));
        $response->assertRedirect(route('profile'));

        $this->assertDatabaseMissing('productos', ['guid' => $producto->guid]);
    }

    public function test_changestatus()
    {
        $this->actingAs($this->user);

        $producto = $this->producto1;

        $response = $this->post(route('producto.changestatus', ['guid' => $producto->guid]));

        $response->assertStatus(403);
        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('success', 'Estado del producto actualizado correctamente.');
    }

    public function test_index_vista_usuario_no_autenticado()
    {
        $response = $this->get(route('pages.home'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.home');
        $response->assertViewHas('productos');

        $productosEnVista = $response->original->getData()['productos'];

        // Verificar que se muestran todos los productos disponibles
        $this->assertTrue($productosEnVista->contains('id', $this->producto1->id));
        $this->assertTrue($productosEnVista->contains('id', $this->producto2->id));
    }

    public function test_search_sin_parametros()
    {
        $response = $this->get(route('productos.search'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.home');
        $response->assertViewHas('productos');

        $productosEnVista = $response->original->getData()['productos'];

        // Verificar que se muestran todos los productos disponibles
        $this->assertTrue($productosEnVista->contains('id', $this->producto1->id));
        $this->assertTrue($productosEnVista->contains('id', $this->producto2->id));
    }

    public function test_search_categoria_todos()
    {
        $response = $this->get(route('productos.search', [
            'categoria' => 'todos',
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('pages.home');
        $response->assertViewHas('productos');

        $productosEnVista = $response->original->getData()['productos'];

        // Verificar que se muestran todos los productos disponibles
        $this->assertTrue($productosEnVista->contains('id', $this->producto1->id));
        $this->assertTrue($productosEnVista->contains('id', $this->producto2->id));
    }

    public function test_search_rango_precios()
    {
        $response = $this->get(route('productos.search', [
            'precio_min' => 50,
            'precio_max' => 150,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('pages.home');
        $response->assertViewHas('productos');

        $productosEnVista = $response->original->getData()['productos'];

        // Verificar que solo se muestran los productos dentro del rango de precios
        foreach ($productosEnVista as $producto) {
            $this->assertTrue($producto->precio >= 50 && $producto->precio <= 150);
        }
    }

    public function test_store_validacion_fallida()
    {
        $this->actingAs($this->user);

        $data = [
            'nombre' => '',
            'descripcion' => 'Descripción del producto',
            'estadoFisico' => 'Nuevo',
            'precio' => 100.00,
            'stock' => 10,
            'categoria' => 'Cocina',
            'imagen1' => UploadedFile::fake()->image('imagen1.jpg'),
        ];

        $response = $this->post(route('producto.store'), $data);

        $response->assertSessionHasErrors(['nombre']);
    }

    public function test_store_subida_imagen_fallida()
    {
        $this->actingAs($this->user);

        $data = [
            'nombre' => 'Producto Test',
            'descripcion' => 'Descripción del producto',
            'estadoFisico' => 'Nuevo',
            'precio' => 100.00,
            'stock' => 10,
            'categoria' => 'Cocina',
            'imagen1' => UploadedFile::fake()->image('imagen1.jpg')->size(6000),
        ];

        $response = $this->post(route('producto.store'), $data);

        $response->assertSessionHasErrors(['imagen1']);
    }

    public function test_edit_usuario_no_autorizado()
    {
        $otroUser = User::factory()->create();
        $this->actingAs($otroUser);

        $response = $this->get(route('producto.edit', ['guid' => $this->producto1->guid]));

        $response->assertStatus(302);
    }

    public function test_update_validacion_fallida()
    {
        $this->actingAs($this->user);

        $data = [
            'nombre' => '',
            'descripcion' => 'Descripción actualizada',
            'estadoFisico' => 'Usado',
            'precio' => 200,
            'stock' => 5,
            'categoria' => 'Hogar',
        ];

        $response = $this->put(route('producto.update', ['guid' => $this->producto1->guid]), $data);

        $response->assertSessionHasErrors(['nombre']);
    }

    public function test_changestatus_usuario_no_autorizado()
    {
        $otroUser = User::factory()->create();
        $this->actingAs($otroUser);

        $vendedor = Cliente::factory()->create(['usuario_id' => User::factory()->create()->id]);
        $producto = Producto::factory()->create([
            'vendedor_id' => $vendedor->id,
        ]);

        $response = $this->post(route('producto.changestatus', ['guid' => $producto->guid]));

        $response->assertStatus(403);
    }

    public function test_edit_usuario_autorizado()
    {
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        $producto = Producto::factory()->create(['vendedor_id' => $cliente->id]);

        $this->actingAs($user);

        $response = $this->get(route('producto.edit', ['guid' => $producto->guid]));

        $response->assertStatus(200);
        $response->assertViewIs('profile.edit-producto');
        $response->assertViewHas('producto', $producto);
    }

    public function test_edit_usuario_no_autorizado_()
    {
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);

        $otroUser = User::factory()->create();
        $otroCliente = Cliente::factory()->create(['usuario_id' => $otroUser->id]);
        $producto = Producto::factory()->create(['vendedor_id' => $otroCliente->id]);

        $this->actingAs($user);

        $response = $this->get(route('producto.edit', ['guid' => $producto->guid]));

        $response->assertStatus(302);
    }

    public function test_edit_usuario_no_autenticado()
    {
        $producto = Producto::factory()->create();

        $response = $this->get(route('producto.edit', ['guid' => $producto->guid]));

        $response->assertRedirect(route('login'));
    }

    public function test_edit_producto_no_encontrado()
    {
        // Crear un usuario y autenticarlo
        $user = User::factory()->create();
        $this->actingAs($user);

        // Hacer la solicitud al método edit con un GUID inexistente
        $response = $this->get(route('producto.edit', ['guid' => 'guid-inexistente']));

        // Verificar que se redirige con un mensaje de error
        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('error', 'Producto no encontrado');
    }

    public function test_show_add_form_usuario_autenticado()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('producto.add'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.add-producto');
    }

    public function test_show_add_form_usuario_no_autenticado()
    {
        $response = $this->get(route('producto.add'));

        $response->assertRedirect(route('login'));
    }

}
