<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClientesSeeder extends Seeder
{
    public function run(): void
    {
        Cliente::create([
            'guid' => 'de6a7d01-af1d-44fb-a615-2583f52da3c4',
            'nombre' => 'Juan',
            'apellido' => 'Perez',
            'avatar' => 'juan.jpg',
            'telefono' => '612345678',
            'direccion' => [
                'calle' => 'Avenida Siempre Viva',
                'numero' => 742,
                'piso' => 1,
                'letra' => 'A',
                'codigoPostal' => 28001
            ],
            'activo' => true,
            'usuario_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Cliente::create([
            'guid' => '97b37979-552a-4916-989a-b96031348856',
            'nombre' => 'Maria',
            'apellido' => 'Garcia',
            'avatar' =>'maria.jpg',
            'telefono' => '987654321',
            'direccion' => [
                'calle' => 'Calle de las Nubes',
                'numero' => 123,
                'piso' => 2,
                'letra' => 'C',
                'codigo_postal' => 28002
            ],
            'activo' => true,
            'usuario_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Cliente::create([
            'guid' => '81f58779-2a53-4922-b628-16c330d4b28a',
            'nombre' => 'Pedro',
            'apellido' => 'Martinez',
            'avatar' => 'pedro.jpg',
            'telefono' => '321456789',
            'direccion' => [
                'calle' => 'Avenida España',
                'numero' => 456,
                'piso' => 3,
                'letra' => 'B',
                'codigo_postal' => 28003
            ],
            'activo' => false,
            'usuario_id' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
