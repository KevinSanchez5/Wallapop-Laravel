<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'guid'=> '789a7609-624a-49b2-bcf9-a9ea1d034f5e',
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
            'guid'=> '2491f841-0993-4096-82b9-6884a887f683',
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
            'guid'=> '3ce8a699-56cb-4765-acb2-2b5e36fea78f',
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
            'guid'=> '5852148c-4d79-4556-a20f-9448b6d55279',
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
            'guid'=> 'a2b3c4d5-1234-5678-9abc-def012345678',
            'name' => 'Ana López',
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
            'guid'=> 'f6e7d8c9-9876-5432-b1a2-c3d4e5f6g7h8',
            'name' => 'Carlos Fernández',
            'email' => 'carlos@example.com',
            'password' => 'SuperAdmin456!',
            'role' => 'admin',
            'email_verified_at' => now(),
            'remember_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);

        User::create([
            'guid'=> '9d00acfc-64b4-4406-9de9-5988aa3e4816',
            'name' => 'Isabella Rodríguez',
            'email' => 'isabella@example.com',
            'password' => 'Password123?',
            'role' => 'admin',
            'email_verified_at' => now(),
            'remember_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);

        User::create([
            'guid'=> '4b081c14-d84b-4994-b521-57763b87483a',
            'name' => 'Mario de Domingo',
            'email' => 'wolverine.mda.307@gmail.com',
            'password' => '30072004',
            'role' => 'admin',
            'email_verified_at' => now(),
            'remember_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);

    }
}
