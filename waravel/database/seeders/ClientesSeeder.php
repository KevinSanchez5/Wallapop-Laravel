<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClientesSeeder extends Seeder
{
    public function run(): void
    {
        Cliente::create([
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
            'usuario_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Cliente::create([
            'nombre' => 'Maria',
            'apellido' => 'Garcia',
            'avatar' =>'clientes/avatar.png',
            'telefono' => '987654321',
            'direccion' => [
                'calle' => 'Calle de las Nubes',
                'numero' => 123,
                'piso' => 3,
                'letra' => 'C',
                'codigoPostal' => 28971
            ],
            'activo' => true,
            'usuario_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Cliente::create([
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
            'usuario_id' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
