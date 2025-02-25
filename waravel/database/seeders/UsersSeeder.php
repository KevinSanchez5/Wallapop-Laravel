<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
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
        User::create([
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
        ]);
        User::create([
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
        ]);
        User::create([
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
        ]);
        User::create([
            'name' => 'Isabella Rodríguez',
            'email' => 'isabella@example.com',
            'password' => 'Password123?',
            'role' => 'user',
            'email_verified_at' => now(),
            'remember_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);
    }
}
