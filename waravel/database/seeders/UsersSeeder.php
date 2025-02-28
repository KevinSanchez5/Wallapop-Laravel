<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        //admins
        User::create([
            'guid' =>'P4K7VQ9XTYL',
            'name' => 'Ana López',
            'email' => 'ana@example.com',
            'password' => 'AdminPass123!',
            'role' => 'admin',
            'email_verified_at' => now(),
            'remember_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);
        User::create([
            'guid' =>'0xaL61OsDpb',
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'adminPassword123?',
            'role' => 'admin',
            'email_verified_at' => now(),
            'remember_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);
        //clientes
        User::create([
            'guid' =>'2G6HueqixE5',
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'Password123?',
            'role' => 'cliente',
            'email_verified_at' => now(),
            'remember_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);//id 2
        User::create([
            'guid' =>'DU6jCZtareb',
            'name' => 'María García',
            'email' =>'maria@example.com',
            'password' => 'Password123?',
            'role' => 'cliente',
            'email_verified_at' => now(),
            'remember_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);//id 3
        User::create([
            'guid' =>'yEC3KBt6CFY',
            'name' => 'Pedro Martínez',
            'email' => 'pedro@example.com',
            'password' => 'Password123?',
            'role' => 'cliente',
            'email_verified_at' => now(),
            'remember_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);//id 4
        User::create([
            'guid' =>'X9vB7LpQ2ZM',
            'name' => 'Laura Gómez',
            'email' => 'laura@example.com',
            'password' => 'Password123?',
            'role' => 'cliente',
            'email_verified_at' => now(),
            'remember_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);//id 5
        User::create([
            'guid' =>'G5Yt9XqK8VL',
            'name' => 'Diego Ruiz',
            'email' => 'diego@example.com',
            'password' => 'Password123?',
            'role' => 'cliente',
            'email_verified_at' => now(),
            'remember_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);//id 6
        User::create([
            'guid' =>'J2pM6ZcQ4BR',
            'name' => 'Sofía López',
            'email' => 'sofia@example.com',
            'password' => 'Password123?',
            'role' => 'cliente',
            'email_verified_at' => now(),
            'remember_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);//id 7
        User::create([
            'guid' =>'W7Xn3TfY9KD',
            'name' => 'Javier Torres',
            'email' => 'javier@example.com',
            'password' => 'Password123?',
            'role' => 'cliente',
            'email_verified_at' => now(),
            'remember_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);//id 8
        User::create([
            'guid' =>'P8Lq5VZK2YM',
            'name' => 'Laura Castro',
            'email' => 'laurac@example.com',
            'password' => 'Password123?',
            'role' => 'cliente',
            'email_verified_at' => now(),
            'remember_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);//id 9
        User::create([
            'guid' =>'M4B9XQK7YtN',
            'name' => 'Ana Lopez',
            'email' => 'analopez@example.com',
            'password' => 'Password123?',
            'role' => 'cliente',
            'email_verified_at' => now(),
            'remember_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);//id 10
        User::create([
            'guid' =>'Z6TQ8LpX5YV',
            'name' => 'Carlos Fernandez',
            'email' => 'carlosf@example.com',
            'password' => 'Password123?',
            'role' => 'cliente',
            'email_verified_at' => now(),
            'remember_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);//id 11
        User::create([
            'guid' =>'X9KpL3YV7QT',
            'name' => 'Isabella Rodriguez',
            'email' => 'isabellar@example.com',
            'password' => 'Password123?',
            'role' => 'cliente',
            'email_verified_at' => now(),
            'remember_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);//id 12
        User::create([
            'guid' =>'B6YtQ8XZ5LM',
            'name' => 'Jose Luis',
            'email' => 'joseluis@example.com',
            'password' => 'Password123?',
            'role' => 'cliente',
            'email_verified_at' => now(),
            'remember_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);//id 13



    }
}
