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

    public function test_mostrarCliente()
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
    }

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
}
