<?php

namespace Database\Seeders;

use App\Models\LineaVenta;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UsersSeeder::class);
        $this->call(ClientesSeeder::class);
        $this->call(ProductosSeeder::class);
        $this->call(ValoracionesSeeder::class);
        $this->call(ClientesFavoritosSeeder::class);
        //$this->call(LineaVentaSeeder::class);
        //$this->call(VentaSeeder::class);
        //$this->call(LineaCarritoSeeder::class);
        //$this->call(CarritoSeeder::class);
    }
}
