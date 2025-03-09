<?php

namespace Tests\Feature;

use App\Models\User;
use App\Utils\GuidGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        Redis::flushall();


        $this->user = User::create([
            'name' => 'usuario',
            'email' => 'usuario@example.com',
            'password' => bcrypt('secret'),
            'role' => 'cliente',
        ]);
    }



    public function test_index()
    {
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'guid' => GuidGenerator::generarId(),
                'name' => "Usuario $i",
                'email' => "usuario$i@example.com",
                'password' => bcrypt('password$i'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        $response = $this->getJson('api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'users' => [
                    '*' => ['id', 'guid', 'name', 'email', 'password', 'role', 'created_at', 'updated_at']
                ],
                'paginacion' => [
                    'pagina_actual', 'elementos_por_pagina', 'ultima_pagina', 'elementos_totales'
                ]
            ]);

        $json = $response->json();
        $this->assertEquals(5, count($json['users']));
        $this->assertEquals(1, $json['paginacion']['pagina_actual']);
        $this->assertEquals(5, $json['paginacion']['elementos_por_pagina']);
        $this->assertEquals(11, $json['paginacion']['elementos_totales']);
    }



    public function test_show(): void
    {
        Redis::del('user_' . $this->user->id);

        $response = $this->getJson("/api/users/{$this->user->guid}");

        $response->assertStatus(200);

        $response->assertJson([
            'id' => $this->user->id,
            'guid' => $this->user->guid,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'role' => $this->user->role,
            'created_at' => $this->user->created_at->toJSON(),
            'updated_at' => $this->user->updated_at->toJSON(),
        ]);

        $userRedis = json_decode(Redis::get('user_' . $this->user->guid), true);

        $this->assertEquals($this->user->guid, $userRedis['guid']);
        $this->assertEquals($this->user->name, $userRedis['name']);
        $this->assertEquals($this->user->email, $userRedis['email']);
        $this->assertEquals($this->user->role, $userRedis['role']);
    }



    public function test_show_not_found()
    {
        $response = $this->get('/api/users/999');

        $response->assertNotFound();
        $response->assertJson(['message' => 'User not found']);
    }

    public function test_store()
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

    public function test_store_invalid()
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

    public function test_update()
    {
        $user = User::factory()->create([
            'name' => 'Juan',
            'email' => 'juan@gmail.com',
            'password' => 'Password123!',
            'role' => 'user',
        ]);

        Redis::set('user_' . $user->id, json_encode($user));

        $updateData = [
            'name' => 'Otro',
            'email' => 'otro@gmail.com',
            'password' => 'NewPass123!',
            'role' => 'admin'
        ];

        $response = $this->putJson('/api/users/' . $user->id, $updateData);


        $user->refresh();


        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => 'Otro',
                'email' => 'otro@gmail.com',
                'role' => 'admin'
            ]);

        $this->assertEquals('Otro', $user->name);
        $this->assertEquals('otro@gmail.com', $user->email);
        $this->assertEquals('admin', $user->role);
        $this->assertTrue(Hash::check('NewPass123!', $user->password));


        $cachedUser = json_decode(Redis::get('user_' . $user->id), true);
        $this->assertEquals('Otro', $cachedUser['name']);
        $this->assertEquals('otro@gmail.com', $cachedUser['email']);
        $this->assertEquals('admin', $cachedUser['role']);
    }


    public function test_update_invalid()
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

        $response = $this->putJson('/api/users/' . $user->guid, $updateData);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['email', 'password', 'role']);
    }


    public function test_update_not_found()
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



    public function test_destroy()
    {
        $user = User::factory(['name' => 'Juan'])->create();

        $response = $this->delete("/api/users/{$user->guid}");

        $response->assertOk();
        $response->assertJson(['message' => 'User eliminado correctamente']);
        $this->assertDatabaseMissing('users', $user->toArray());
    }

    public function test_destroy_Redis()
    {
        $user = User::factory(['name' => 'Juan'])->create();

        Redis::set('user_' . $user->id, json_encode($user->toArray()));

        $response = $this->delete("/api/users/{$user->id}");

        $response->assertOk();
        $response->assertJson(['message' => 'User eliminado correctamente']);
        $this->assertDatabaseMissing('users', $user->toArray());

        $this->assertNull(Redis::get('user_' . $user->id));
    }

    public function test_destroy_not_found()
    {

        $response = $this->delete("/api/users/999");

        $response->assertNotFound();
        $response->assertJson(['message' => 'User no encontrado']);
    }

    /*public function testEnviarCorreoRecuperarContrasenya_Success(){
        Log::spy();
        $user = User::factory()->create(['id' => '51', 'email' => 'test@test.com']);

        Mail::fake();

        $response = $this->postJson('api/users/correo-codigo', [
            'email'=>'test@test.com'
        ]);
        $response->assertOk();
        $response->assertJson(['message' => 'Correo enviado correctamente']);

        Log::shouldHaveReceived('info')->with('Iniciando proceso de recuperación de contraseña', ['email' => 'test@test.com']);
        Log::shouldHaveReceived('info')->with('Validación de email completada', ['email' => 'test@test.com']);
        Log::shouldHaveReceived('info')->with('Buscando usuario por email', ['email' => 'test@test.com']);
        Log::shouldHaveReceived('info')->with('Usuario encontrado', ['user_id' => $user->id]);
        Log::shouldHaveReceived('info')->with('Correo de recuperación enviado', ['email' => 'test@test.com']);
        Log::shouldHaveReceived('info')->with('Proceso de recuperación de contraseña completado', ['email' => 'test@test.com']);

    }


    public function test_find_user_not_found()
    {
        $response = $this->postJson('/users/verificar-correo/{email}', [
            'email' => 'noexiste@example.com',
        ]);


        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Usuario no encontrado',
            ]);
    }*/



    public function testEnviarCorreoRecuperarContrasenya_ErrorEnviarCorreo()
    {
        Log::spy();

        $user = User::factory()->create([
            'email' => 'test@example.com'
        ]);

        Mail::shouldReceive('to->send')
            ->andThrow(new \Exception('Error al enviar correo'));

        $response = $this->postJson('api/users/correo-codigo', [
            'email' => $user->email
        ]);

        $response->assertStatus(503);
        $response->assertJson(['error' => 'Error al enviar correo']);
    }

}
