<?php

namespace Tests\Feature\View;

use App\Models\Carrito;
use App\Models\Cliente;
use App\Models\LineaCarrito;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;

class CarritoControllerViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_cart()
    {
        $response = $this->get(route('carrito'));
        $response->assertStatus(200);
        $response->assertViewIs('pages.shoppingCart');
    }

    public function test_remove_from_cart()
    {
        $producto = Producto::factory()->create();

        Session::put('carrito',  new Carrito([
            'lineasCarrito' => [ new LineaCarrito([
                'producto' => $producto,
                'cantidad' => 2,
                'precioTotal' => $producto->precio * 2,
            ])],
            'precioTotal' => $producto->precio * 2,
            'itemAmount' => 2
        ]));

        $response = $this->delete(route('carrito.remove'), ['productId' => $producto->guid]);

        $response->assertJson(['status' => 200]);
        $this->assertEquals(0, session('carrito')->precioTotal);
    }

    public function test_delete_one_from_cart()
    {
        $producto = Producto::factory()->create();

        Session::put('carrito', new Carrito([
            'lineasCarrito' => [new LineaCarrito([
                'producto' => $producto,
                'cantidad' => 2,
                'precioTotal' => $producto->precio * 2,
            ])],
            'precioTotal' => $producto->precio * 2,
            'itemAmount' => 2
        ]));

        $response = $this->put(route('carrito.removeOne'), ['productId' => $producto->guid]);

        $response->assertJson(['status' => 200]);
        $this->assertEquals($producto->precio, session('carrito')->precioTotal);
    }

    public function test_add_one_to_cart()
    {
        $producto = Producto::factory()->create();

        Session::put('carrito', new Carrito([
            'lineasCarrito' => [new LineaCarrito([
                'producto' => $producto,
                'cantidad' => 1,
                'precioTotal' => $producto->precio,
            ])],
            'precioTotal' => $producto->precio,
            'itemAmount' => 1
        ]));

        $response = $this->put(route('carrito.addOne'), ['productId' => $producto->guid]);

        $response->assertJson(['status' => 200]);
        $this->assertEquals($producto->precio * 2, session('carrito')->precioTotal);
    }

    public function test_add_to_cart_or_edit()
    {
        $producto = Producto::factory()->create();

        Session::put('carrito', new Carrito([
            'lineasCarrito' => [],
            'precioTotal' => 0,
            'itemAmount' => 0
        ]));

        $response = $this->post(route('carrito.add'), ['productId' => $producto->guid, 'amount' => 2]);

        $response->assertJson(['status' => 200]);
        $this->assertEquals($producto->precio * 2, session('carrito')->precioTotal);
    }

    public function test_show_order()
    {
        $user = User::factory()->create(

        );
        $user->role = 'cliente';
        $this->actingAs($user);

        $client = Cliente::factory()->create(
            [
                'usuario_id' => $user->id
            ]
        );

        $producto = Producto::factory()->create(
            [
                'vendedor_id' => $client->id
            ]
        );

        Session::put('carrito', new Carrito([
            'lineasCarrito' => [new LineaCarrito([
                'producto' => $producto,
                'cantidad' => 1,
                'precioTotal' => $producto->precio,
            ])],
            'precioTotal' => $producto->precio,
            'itemAmount' => 1
        ]));

        $response = $this->get(route('carrito.checkout'));
        $response->assertViewIs('pages.orderSummary');
    }
}
