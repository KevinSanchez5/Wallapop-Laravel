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

        $storagePath = storage_path('app/public/productos');

        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        $image1 = UploadedFile::fake()->image('imagen1.jpg');
        $image2 = UploadedFile::fake()->image('imagen2.jpg');

        $data = [
            'nombre' => 'Producto Test',
            'descripcion' => 'Descripción del producto',
            'estadoFisico' => 'Nuevo',
            'precio' => 100.00,
            'stock' => 10,
            'categoria' => 'Cocina',
            'imagen1' => $image1,
            'imagen2' => $image2,
        ];

        $response = $this->post(route('producto.store'), $data);
        $producto = Producto::first();
        $imagenes = json_decode($producto->imagenes, true);

        $this->assertEquals([
            "https://via.placeholder.com/640x480.png",
            "https://via.placeholder.com/640x480.png"
        ], $imagenes);

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
        $response->assertRedirect(route('profile'));

        $this->assertDatabaseHas('productos', [
            'guid' => $producto->guid,
            'estado' => 'Desactivado'
        ]);
    }

}





