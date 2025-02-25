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
            'avatar' => 'clientes/avatar.png',
            'telefono' => '612345678',
            'direccion' => [
                'calle' => 'Avenida Siempre Viva',
                'numero' => 742,
                'piso' => 1,
                'letra' => 'A',
                'codigoPostal' => 28001
            ],
            'activo' => true,
            'usuario_guid' => '2491f841-0993-4096-82b9-6884a887f683',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Cliente::create([
            'guid' => '97b37979-552a-4916-989a-b96031348856',
            'nombre' => 'Maria',
            'apellido' => 'Garcia',
            'avatar' =>'clientes/avatar.png',
            'telefono' => '987654321',
            'direccion' => [
                'calle' => 'Calle de las Nubes',
                'numero' => 123,
                'piso' => 2,
                'letra' => 'C',
                'codigoPostal' => 28971
            ],
            'activo' => true,
            'usuario_guid' => '3ce8a699-56cb-4765-acb2-2b5e36fea78f',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Cliente::create([
            'guid' => '81f58779-2a53-4922-b628-16c330d4b28a',
            'nombre' => 'Pedro',
            'apellido' => 'Martinez',
            'avatar' => 'clientes/avatar.png',
            'telefono' => '321456789',
            'direccion' => [
                'calle' => 'Avenida España',
                'numero' => 456,
                'piso' => 3,
                'letra' => 'B',
                'codigoPostal' => 28970
            ],
            'activo' => false,
            'usuario_guid' => '5852148c-4d79-4556-a20f-9448b6d55279',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
