<?php

namespace Tests\Feature\View;

use App\Models\Carrito;
use App\Models\Cliente;
use App\Models\LineaCarrito;
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

    public function test_remove_from_cart_with_invalid_request()
    {
       $response = $this->delete(route('carrito.remove'), ['something' => '']);

        $response->assertJson(['status' => 400]);
        $response->assertJson(['message' => 'No se han proporcionado los campos necesarios']);
    }

    public function test_remove_from_cart_with_nonexistent_product()
    {
        $response = $this->delete(route('carrito.remove'), ['productId' => 'someId']);

        $response->assertJson(['status' => 404]);
        $response->assertJson(['message' => 'No se ha podido eliminar el producto del carrito']);
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

    public function test_delete_one_from_cart_with_invalid_request(){
        $response = $this->put(route('carrito.removeOne'), ['something' => '']);

        $response->assertJson(['status' => 400]);
        $response->assertJson(['message' => 'No se han proporcionado los campos necesarios']);
    }

    public function test_delete_one_from_cart_deletes_it_when_amount_is_one(){
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

        $response = $this->put(route('carrito.removeOne'), ['productId' => $producto->guid]);

        $response->assertJson(['status' => 200]);
        $this->assertEquals(0, session('carrito')->itemAmount);
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

    public function test_add_one_to_cart_with_invalid_request(){
        $response = $this->put(route('carrito.addOne'), ['something' => '']);

        $response->assertJson(['status' => 400]);
        $response->assertJson(['message' => 'No se han proporcionado los campos necesarios']);
    }

    public function test_add_one_with_not_enough_stock(){
        $product = Producto::factory()->create(['stock' => 1]);

        Session::put('carrito', new Carrito([
            'lineasCarrito' => [new LineaCarrito([
                'producto' => $product,
                'cantidad' => 1,
                'precioTotal' => $product->precio,
            ])],
            'precioTotal' => $product->precio,
            'itemAmount' => 1
        ]));

        $response = $this->put(route('carrito.addOne'), ['productId' => $product->guid]);

        $response->assertJson(['status' => 400]);
        $response->assertJson(['message' => 'No hay stock suficiente para agregar más productos']);
    }

    public function test_add_one_with_a_product_thats_not_in_the_cart_already(){
        Session::put('carrito', new Carrito([
            'lineasCarrito' => [],
            'precioTotal' => 0,
            'itemAmount' => 0
        ]));

        $response = $this->put(route('carrito.addOne'), ['productId' => 'nonExistentProduct']);

        $response->assertJson(['status' => 404]);
        $response->assertJson(['message' => 'No se ha encontrado el producto en el carrito, por favor recargue la página']);
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

    public function test_add_to_cart_or_edit_with_a_product_thats_in_the_cart_already(){
        $product = Producto::factory()->create([
           'stock' => 10
        ]);

        Session::put('carrito', new Carrito([
            'lineasCarrito' => [new LineaCarrito([
                'producto' => $product,
                'cantidad' => 2,
                'precioTotal' => $product->precio * 2,
            ])],
            'precioTotal' => $product->precio * 2,
            'itemAmount' => 2
        ]));

        $response = $this->post(route('carrito.add'), ['productId' => $product->guid, 'amount' => 3]);

        $response->assertJson(['status' => 200]);
        $this->assertEquals($product->precio * 5, session('carrito')->precioTotal);
    }

    public function test_add_to_cart_or_edit_with_a_product_thats_in_the_cart_already_but_doesnt_have_enough_stock(){
        $product = Producto::factory()->create([
           'stock' => 3
        ]);

        Session::put('carrito', new Carrito([
            'lineasCarrito' => [new LineaCarrito([
                'producto' => $product,
                'cantidad' => 2,
                'precioTotal' => $product->precio * 2,
            ])],
            'precioTotal' => $product->precio * 2,
            'itemAmount' => 2
        ]));

        $response = $this->post(route('carrito.add'), ['productId' => $product->guid, 'amount' => 3]);

        $response->assertJson(['status' => 400]);
        $response->assertJson(['message' => 'No hay stock suficiente para agregar más productos']);
    }

    public function test_add_to_cart_or_edit_with_invalid_request(){
        $response = $this->post(route('carrito.add'), ['something' => '']);

        $response->assertJson(['status' => 400]);
        $response->assertJson(['message' => 'No se han proporcionado los campos necesarios']);
    }

    public function test_add_to_cart_or_edit_with_nonexistent_product(){
        $response = $this->post(route('carrito.add'), ['productId' => 'nonExistentProduct', 'amount' => 2]);

        $response->assertJson(['status' => 404]);
        $response->assertJson(['message' => 'No se ha encontrado el producto, por favor recargue la página']);
    }

    public function test_add_to_cart_or_edit_with_your_own_product(){
        $user = User::factory()->create();
        $user->role = 'client';
        $this->actingAs($user);

        $client = Cliente::factory()->create(
            [
                'usuario_id' => $user->id
            ]
        );

        $product = Producto::factory()->create([
               'vendedor_id' => $client->id]
        );

        Session::put('carrito', new Carrito([
            'lineasCarrito' => [new LineaCarrito([
                'producto' => $product,
                'cantidad' => 1,
                'precioTotal' => $product->precio,
            ])],
            'precioTotal' => $product->precio,
            'itemAmount' => 1
        ]));

        $response = $this->post(route('carrito.add'), ['productId' => $product->guid, 'amount' => 1]);

        $response->assertJson(['status' => 400]);
        $response->assertJson(['message' => 'No puedes añadir tus propios productos al carrito']);
    }

    public function test_add_to_cart_or_edit_when_logged_in(){
        $user = User::factory()->create();
        $this->actingAs($user);

        $user = User::factory()->create();
        $user->role = 'client';
        $this->actingAs($user);

        $product = Producto::factory()->create();

        Session::put('carrito', new Carrito([
            'lineasCarrito' => [new LineaCarrito([
                'producto' => $product,
                'cantidad' => 1,
                'precioTotal' => $product->precio,
            ])],
            'precioTotal' => $product->precio,
            'itemAmount' => 1
        ]));

        $response = $this->post(route('carrito.add'), ['productId' => 'nonExistentProduct', 'amount' => 2]);

        $response->assertJson(['status' => 404]);
        $response->assertJson(['message' => 'No se ha encontrado el producto, por favor recargue la página']);
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

    public function test_show_order_with_missing_client(){
        $user = User::factory()->create();
        $user->role = 'cliente';
        $this->actingAs($user);

        Session::put('carrito', new Carrito([
            'lineasCarrito' => [],
            'precioTotal' => 0,
            'itemAmount' => 0
        ]));

        $response = $this->get(route('carrito.checkout'));
        $response->assertRedirect(route('carrito'));
    }

    public function test_add_more_than_stock()
    {
        $producto = Producto::factory()->create(['stock' => 5]);

        $response = $this->post(route('carrito.add'), ['productId' => $producto->guid, 'amount' => 10]);

        $response->assertJson(['status' => '400']);
        $response->assertJson(['message' => 'No hay stock suficiente para agregar más productos']);
    }

    public function test_remove_non_existent_product(){
        $response = $this->put(route('carrito.removeOne'), ['productId' => 'non-existent']);

        $response->assertJson(['status' => 404]);
        $response->assertJson(['message' => 'No se ha encontrado el producto en el carrito, por favor recargue la página']);
    }

    public function test_non_authenticated_user_cannot_checkout()
    {
        $response = $this->get(route('carrito.checkout'));

        $response->assertRedirect(route('login'));
    }
}
