<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductosSeeder extends Seeder
{
    public function run(): void
    {
        Producto::create([
            'guid' => "1f0368b0-5ce9-4099-bd13-0c1cede8d349",
            'vendedor_id' => 1,
            'nombre' => 'Portatil Gamer',
            'descripcion' => 'Portatil potente para gaming y trabajo pesado.',
            'estadoFisico' => 'Nuevo',
            'precio' => 800.00,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
            'imagenes' => json_encode(['portatil1.jpg', 'portatil2.jpg']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Producto::create([
            'guid' => "b4cb952d-1568-4763-901a-dfb9e05a4992",
            'vendedor_id' => 2,
            'nombre' => 'Chaqueta de cuero',
            'descripcion' => 'Chaqueta elegante y resistente, ideal para el frío.',
            'estadoFisico' => 'Usado',
            'precio' => 15.00,
            'categoria' => 'Ropa',
            'estado' => 'Disponible',
            'imagenes' => json_encode(['chaqueta1.jpg']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Producto::create([
            'guid' => "8b09d444-554b-4b95-9962-d6d64d46719c",
            'vendedor_id' => 3,
            'nombre' => 'Guitarra eléctrica',
            'descripcion' => 'Modelo clásico con excelente sonido.',
            'estadoFisico' => 'Deteriorado',
            'precio' => 300.00,
            'categoria' => 'Musica',
            'estado' => 'Vendido',
            'imagenes' => json_encode(['guitarra1.jpg', 'guitarra2.jpg']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Producto::create([
            'guid' => "f047b42f-5151-4497-a300-7e78794c850f",
            'vendedor_id' => 1,
            'nombre' => 'Pantalones de lana',
            'descripcion' => 'Pantalones de lana de manga corta y cómodos.',
            'estadoFisico' => 'Nuevo',
            'precio' => 10.00,
            'categoria' => 'Ropa',
            'estado' => 'Vendido',
            'imagenes' => json_encode(['pantalones1.jpg', 'pantalones2.jpg']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Producto::create([
            'guid' => "b060d2c6-9386-46a7-88c8-607850d75585",
            'vendedor_id' => 2,
            'nombre' => 'Mario Party 8',
            'descripcion' => 'Juego de plataformas y acción, muy popular en Nintendo.',
            'estadoFisico' => 'Nuevo',
            'precio' => 50.00,
            'categoria' => 'Videojuegos',
            'estado' => 'Disponible',
            'imagenes' => json_encode(['mario1.jpg','mario2.jpg']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Producto::create([
            'guid' => "00f26236-958a-4043-8756-60430d98040d",
            'vendedor_id' => 3,
            'nombre' => 'Consola Xbox Series X',
            'descripcion' => 'Consola de juegos de acción y adrenalina',
            'estadoFisico' => 'Nuevo',
            'precio' => 250.00,
            'categoria' => 'Videojuegos',
            'estado' => 'Disponible',
            'imagenes' => json_encode(['xbox1.jpg', 'xbox2.jpg']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
