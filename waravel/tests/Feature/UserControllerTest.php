<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Redis::flushall();
    }

    public function testIndexReturnsUsers()
    {
        $user = User::factory(['name' => 'Juan'])->create();

        $response = $this->get('/api/users');

        $response->assertOk();
        $response->assertJsonFragment([$user->toArray()]);
    }

    public function testShowWithValidId()
    {
        $user = User::factory()->create(['id' => '4']);

        $response = $this->get('/api/users/' . $user->id);

        $response->assertOk();
        $response->assertJson($user->toArray());
    }

    public function testShowWithValidIdCache()
    {
        $user = User::factory()->create(['id' => '4']);

        Redis::set('user_' . $user->id, json_encode($user));

        $response = $this->get('/api/users/' . $user->id);

        $response->assertOk();
        $response->assertJson($user->toArray());
    }

    public function testShowWithInvalidId()
    {
        $response = $this->get('/api/users/999');

        $response->assertNotFound();
        $response->assertJson(['message' => 'User not found']);
    }

    public function testStore()
    {
        $response = $this->post('/api/users', [
            'name' => 'Juan',
            'email' => 'juan@gmail.com',
            'password' => 'Password@123',
            'role' => 'user'
        ]);

        $response->assertCreated();
        $this->assertJson($response->getContent());
        $this->assertDatabaseHas('users', [
            'name' => 'Juan',
            'email' => 'juan@gmail.com',
            'role' => 'user'
        ]);

        $this->assertCredentials([
            'email' => 'juan@gmail.com',
            'password' => 'Password@123',
        ]);

    }

    public function testStoreUnprocessable()
    {
        $response = $this->post('/api/users', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'role' => 'invalid-role'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password', 'role']);
    }

    public function testUpdate()
    {
        // Crear un usuario en la base de datos
        $user = User::factory()->create([
            'name' => 'Juan',
            'email' => 'juan@gmail.com',
            'password' => 'Password123!',
            'role' => 'user',
        ]);

        // Simular que el usuario está en Redis
        Redis::set('user_' . $user->id, json_encode($user));

        // Datos de actualización
        $updateData = [
            'name' => 'Otro',
            'email' => 'otro@gmail.com',
            'password' => 'NewPass123!',
            'role' => 'admin'
        ];

        // Petición PUT
        $response = $this->putJson('/api/users/' . $user->id, $updateData);

        // Refrescar el usuario desde la base de datos
        $user->refresh();

        // Verificar que la respuesta es correcta
        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => 'Otro',
                'email' => 'otro@gmail.com',
                'role' => 'admin'
            ]);

        // Verificar que los datos se actualizaron en la base de datos
        $this->assertEquals('Otro', $user->name);
        $this->assertEquals('otro@gmail.com', $user->email);
        $this->assertEquals('admin', $user->role);

        // Verificar que la contraseña se haya encriptado
        $this->assertTrue(Hash::check('NewPass123!', $user->password));

        // Verificar que Redis fue actualizado
        $cachedUser = json_decode(Redis::get('user_' . $user->id), true);
        $this->assertEquals('Otro', $cachedUser['name']);
        $this->assertEquals('otro@gmail.com', $cachedUser['email']);
        $this->assertEquals('admin', $cachedUser['role']);
    }

    public function testUpdateWithUserArray()
    {
        // Create a user and store it in the database
        $user = User::factory()->create([
            'name' => 'Juan',
            'email' => 'juan@gmail.com',
            'password' => 'Password123!',
            'role' => 'user',
        ]);

        // Simulate retrieving the user as an array
        $userArray = $user->toArray();

        // Store the user array in Redis
        Redis::set('user_' . $user->id, json_encode($userArray));

        // Data to update the user
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@gmail.com',
            'password' => 'NewPassword123!',
            'role' => 'admin'
        ];

        // Send a PUT request to update the user
        $response = $this->putJson('/api/users/' . $user->id, $updateData);

        // Refresh the user from the database
        $user->refresh();

        // Assert the response status and content
        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => 'Updated Name',
                'email' => 'updated@gmail.com',
                'role' => 'admin'
            ]);

        // Assert the user was updated in the database
        $this->assertEquals('Updated Name', $user->name);
        $this->assertEquals('updated@gmail.com', $user->email);
        $this->assertEquals('admin', $user->role);

        // Assert the password was updated and hashed
        $this->assertTrue(Hash::check('NewPassword123!', $user->password));

        // Assert the user was updated in Redis
        $cachedUser = json_decode(Redis::get('user_' . $user->id), true);
        $this->assertEquals('Updated Name', $cachedUser['name']);
        $this->assertEquals('updated@gmail.com', $cachedUser['email']);
        $this->assertEquals('admin', $cachedUser['role']);
    }

    public function testUpdateUnprocessable()
    {
        $user = User::factory()->create([
            'name' => 'Juan',
            'email' => 'juan@gmail.com',
            'password' => 'Password123!',
            'role' => 'user',
        ]);

        $updateData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'role' => 'invalid-role'
        ];

        $response = $this->putJson('/api/users/' . $user->id, $updateData);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['email', 'password', 'role']);
    }


    public function testUpdateNotFound()
    {
        $response = $this->putJson("/api/users/999", [
            'name' => 'Otro',
            'email' => 'otro@gmail.com',
            'password' => 'OtraPassword@1234',
            'role' => 'admin'
        ]);

        $response->assertNotFound();
        $response->assertJson(['message' => 'User no encontrado']);
    }



    public function testDestroy()
    {
        $user = User::factory(['name' => 'Juan'])->create();

        $response = $this->delete("/api/users/{$user->id}");

        $response->assertOk();
        $response->assertJson(['message' => 'User eliminado correctamente']);
        $this->assertDatabaseMissing('users', $user->toArray());
    }

    public function testDestroyInCache()
    {
        $user = User::factory(['name' => 'Juan'])->create();

        Redis::set('user_' . $user->id, json_encode($user->toArray()));

        $response = $this->delete("/api/users/{$user->id}");

        $response->assertOk();
        $response->assertJson(['message' => 'User eliminado correctamente']);
        $this->assertDatabaseMissing('users', $user->toArray());

        $this->assertNull(Redis::get('user_' . $user->id));
    }



    public function testDestroyNotFound()
    {

        $response = $this->delete("/api/users/999");

        $response->assertNotFound();
        $response->assertJson(['message' => 'User no encontrado']);
    }
}
