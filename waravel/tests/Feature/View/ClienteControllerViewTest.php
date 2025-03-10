<?php

namespace Tests\Feature\View;

use App\Http\Controllers\Views\ClienteControllerView;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use App\Models\Valoracion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class ClienteControllerViewTest extends TestCase
{
    use RefreshDatabase;

    /*public function test_mostrarCliente()
    {
        $user = User::factory()->create();

        $cliente = new Cliente();
        $cliente->guid = 'guidcliente';
        $cliente->nombre = 'Jettie';
        $cliente->apellido = 'Weissnat';
        $cliente->avatar = 'https://via.placeholder.com/640x480.png/00ddee?text=aut';
        $cliente->telefono = '+17434213369';
        $cliente->direccion = json_encode(['calle' => '843 Koepp Prairie Suite 306', 'ciudad' => 'West Devin', 'pais' => 'Saint Kitts and Nevis']);
        $cliente->activo = true;
        $cliente->usuario_id = $user->id;
        $cliente->save();

        $productos = [];
        for ($i = 0; $i < 3; $i++) {
            $producto = new Producto();
            $producto->guid = 'guidproduc' . $i;
            $producto->vendedor_id = $cliente->id;
            $producto->nombre = 'Producto ' . ($i + 1);
            $producto->descripcion = 'Descripción del producto ' . ($i + 1);
            $producto->estadoFisico = 'Nuevo';
            $producto->precio = 100.00;
            $producto->stock = 50;
            $producto->categoria = 'Cocina';
            $producto->estado = 'Disponible';
            $producto->imagenes = json_encode(['imagen' . ($i + 1) . '.jpg']);
            $producto->save();
            $productos[] = $producto;
        }

        $valoraciones = [];
        for ($i = 0; $i < 3; $i++) {
            $valoracion = new Valoracion();
            $valoracion->clienteValorado_id = $cliente->id;
            $valoracion->autor_id = 1;
            $valoracion->puntuacion = rand(1, 5);
            $valoracion->comentario = 'Comentario ' . ($i + 1);
            $valoracion->save();
            $valoraciones[] = $valoracion;
        }

        $response = $this->get(route('cliente.ver', ['guid' => $cliente->guid]));
        $response->assertStatus(200);
    }*/

    public function test_mostrarCliente_cliente_no_existe()
    {
        $response = $this->get(route('cliente.ver', ['guid' => 'cliente-invalido']));
        $response->assertStatus(404);
    }

    public function test_mostrarCliente_no_productos()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $cliente = Cliente::create([
            'guid' => 'guidcliente',
            'nombre' => 'John',
            'apellido' => 'Doe',
            'avatar' => 'https://via.placeholder.com/640x480.png/00ddee?text=aut',
            'telefono' => '+17434213369',
            'direccion' => json_encode(['calle' => '456 Calle Verde', 'ciudad' => 'Verde', 'pais' => 'Verdeland']),
            'activo' => true,
            'usuario_id' => $user->id,
        ]);

        $cliente2 = Cliente::create([
            'guid' => 'guidclient2',
            'nombre' => 'Jane',
            'apellido' => 'Doe',
            'avatar' => 'https://via.placeholder.com/640x480.png/00ddee?text=aut',
            'telefono' => '+17434213369',
            'direccion' => json_encode(['calle' => '123 Calle Verde', 'ciudad' => 'Verde', 'pais' => 'Verdeland']),
            'activo' => true,
            'usuario_id' => $user2->id,
        ]);

        $valoraciones = [];
        for ($i = 0; $i < 3; $i++) {
            $valoracion = new Valoracion();
            $valoracion->clienteValorado_id = $cliente->id;
            $valoracion->autor_id = $cliente2->id;
            $valoracion->puntuacion = rand(1, 5);
            $valoracion->comentario = 'Comentario ' . ($i + 1);
            $valoracion->save();
            $valoraciones[] = $valoracion;
        }

        $response = $this->get(route('cliente.ver', ['guid' => $cliente->guid]));
        $response->assertStatus(200);
    }

    public function test_mostrarCliente_paginado()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $cliente = Cliente::create([
            'guid' => 'guidcliente',
            'nombre' => 'John',
            'apellido' => 'Doe',
            'avatar' => 'https://via.placeholder.com/640x480.png/00ddee?text=aut',
            'telefono' => '+17434213369',
            'direccion' => json_encode(['calle' => '456 Calle Verde', 'ciudad' => 'Verde', 'pais' => 'Verdeland']),
            'activo' => true,
            'usuario_id' => $user->id,
        ]);

        $cliente2 = Cliente::create([
            'guid' => 'guidclient2',
            'nombre' => 'Jane',
            'apellido' => 'Doe',
            'avatar' => 'https://via.placeholder.com/640x480.png/00ddee?text=aut',
            'telefono' => '+17434213369',
            'direccion' => json_encode(['calle' => '123 Calle Verde', 'ciudad' => 'Verde', 'pais' => 'Verdeland']),
            'activo' => true,
            'usuario_id' => $user2->id,
        ]);

        for ($i = 0; $i < 10; $i++) {
            $producto = new Producto();
            $producto->guid = 'guidproduc' . $i;
            $producto->vendedor_id = $cliente->id;
            $producto->nombre = 'Producto ' . ($i + 1);
            $producto->descripcion = 'Descripción del producto ' . ($i + 1);
            $producto->estadoFisico = 'Nuevo';
            $producto->precio = 100.00;
            $producto->stock = 50;
            $producto->categoria = 'Cocina';
            $producto->estado = 'Disponible';
            $producto->imagenes = json_encode(['imagen' . ($i + 1) . '.jpg']);
            $producto->save();
        }

        for ($i = 0; $i < 10; $i++) {
            $valoracion = new Valoracion();
            $valoracion->clienteValorado_id = $cliente->id;
            $valoracion->autor_id = $cliente2->id;
            $valoracion->puntuacion = rand(1, 5);
            $valoracion->comentario = 'Comentario ' . ($i + 1);
            $valoracion->save();
        }

        $response = $this->get(route('cliente.ver', ['guid' => $cliente->guid]));
        $response->assertStatus(200);
    }

    public function test_añadirFavorito()
    {
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        $producto = Producto::factory()->create();

        $response = $this->actingAs($user)->postJson(route('favorito.añadir'), [
            'productoGuid' => $producto->guid,
            'userId' => $user->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 200, 'message' => 'Producto agregado a favoritos']);
        $this->assertTrue($cliente->favoritos->contains($producto));
    }

    public function test_añadirFavorito_producto_ya_existe()
    {
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        $producto = Producto::factory()->create();
        $cliente->favoritos()->attach($producto->id);

        $response = $this->actingAs($user)->postJson(route('favorito.añadir'), [
            'productoGuid' => $producto->guid,
            'userId' => $user->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 200, 'message' => 'Producto ya añadido en favoritos']);
    }

    public function test_añadirFavorito_producto_no_existe()
    {
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);

        $response = $this->actingAs($user)->postJson(route('favorito.añadir'), [
            'productoGuid' => 'guid-invalido',
            'userId' => $user->id,
        ]);

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Producto no encontrado']);
    }

    public function test_añadirFavorito_cliente_no_existe()
    {
        $user = User::factory()->create();
        $producto = Producto::factory()->create();

        $response = $this->actingAs($user)->postJson(route('favorito.añadir'), [
            'productoGuid' => $producto->guid,
            'userId' => 999, // ID de cliente que no existe
        ]);

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Cliente no encontrado']);
    }

    public function test_añadirFavorito_no_autenticado()
    {
        $producto = Producto::factory()->create();

        $response = $this->postJson(route('favorito.añadir'), [
            'productoGuid' => $producto->guid,
            'userId' => 1,
        ]);

        $response->assertStatus(401);
    }

    public function test_eliminarFavorito()
    {
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);
        $producto = Producto::factory()->create();
        $cliente->favoritos()->attach($producto->id);

        $response = $this->actingAs($user)->deleteJson(route('favorito.eliminar'), [
            'productoGuid' => $producto->guid,
            'userId' => $user->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 200, 'message' => 'Producto eliminado de favoritos']);
        $this->assertFalse($cliente->favoritos->contains($producto));
    }

    public function test_eliminarFavorito_producto_no_existe()
    {
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create(['usuario_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson(route('favorito.eliminar'), [
            'productoGuid' => 'guid-invalido',
            'userId' => $user->id,
        ]);

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Producto no encontrado']);
    }

    public function test_eliminarFavorito_cliente_no_existe()
    {
        $user = User::factory()->create();
        $producto = Producto::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('favorito.eliminar'), [
            'productoGuid' => $producto->guid,
            'userId' => 999, // ID de cliente que no existe
        ]);

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Cliente no encontrado']);
    }

    public function test_eliminarFavorito_no_autenticado()
    {
        $producto = Producto::factory()->create();

        $response = $this->deleteJson(route('favorito.eliminar'), [
            'productoGuid' => $producto->guid,
            'userId' => 1,
        ]);

        $response->assertStatus(401);
    }
}
