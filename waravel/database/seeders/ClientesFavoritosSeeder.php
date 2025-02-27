<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientesFavoritosSeeder extends Seeder
{
    public function run(): void
    {
        $clienteFavoritos = [
            [
                'cliente_id' => 1,
                'producto_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cliente_id' => 1,
                'producto_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cliente_id' => 2,
                'producto_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cliente_id' => 2,
                'producto_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cliente_id' => 3,
                'producto_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cliente_id' => 3,
                'producto_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

    }
}
