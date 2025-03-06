<?php

namespace Tests\Feature\View;

use Tests\TestCase;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class CarritoControllerViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_cart()
    {
        $response = $this->get(route('cart.show'));
        $response->assertStatus(200);
        $response->assertViewIs('pages.shoppingCart');
    }

    public function test_remove_from_cart()
    {
        $producto = Producto::factory()->create();

        Session::put('carrito', [
            'lineasCarrito' => [[
                'producto' => $producto,
                'cantidad' => 1,
                'precioTotal' => $producto->precio,
            ]],
            'precioTotal' => $producto->precio,
            'itemAmount' => 1
        ]);

        $response = $this->delete(route('cart.remove'), ['productId' => $producto->guid]);

        $response->assertStatus(200);
        $this->assertEquals(0, session('carrito')['precioTotal']);
    }

    public function test_delete_one_from_cart()
    {
        $producto = Producto::factory()->create();

        Session::put('carrito', [
            'lineasCarrito' => [[
                'producto' => $producto,
                'cantidad' => 2,
                'precioTotal' => $producto->precio * 2,
            ]],
            'precioTotal' => $producto->precio * 2,
            'itemAmount' => 2
        ]);

        $response = $this->delete(route('cart.deleteOne'), ['productId' => $producto->guid]);

        $response->assertStatus(200);
        $this->assertEquals($producto->precio, session('carrito')['precioTotal']);
    }

    public function test_add_one_to_cart()
    {
        $producto = Producto::factory()->create();

        Session::put('carrito', [
            'lineasCarrito' => [[
                'producto' => $producto,
                'cantidad' => 1,
                'precioTotal' => $producto->precio,
            ]],
            'precioTotal' => $producto->precio,
            'itemAmount' => 1
        ]);

        $response = $this->post(route('cart.addOne'), ['productId' => $producto->guid]);

        $response->assertStatus(200);
        $this->assertEquals($producto->precio * 2, session('carrito')['precioTotal']);
    }

    public function test_add_to_cart_or_edit()
    {
        $producto = Producto::factory()->create();

        Session::put('carrito', [
            'lineasCarrito' => [],
            'precioTotal' => 0,
            'itemAmount' => 0
        ]);

        $response = $this->post(route('cart.addOrEdit'), ['productId' => $producto->guid, 'amount' => 2]);

        $response->assertStatus(200);
        $this->assertEquals($producto->precio * 2, session('carrito')['precioTotal']);
    }

    public function test_show_order()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Session::put('carrito', [
            'lineasCarrito' => [],
            'precioTotal' => 0
        ]);

        $response = $this->get(route('cart.order'));
        $response->assertStatus(200);
        $response->assertViewIs('pages.orderSummary');
    }
}
