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
        ]);//id user 2

        Cliente::create([
            'nombre' => 'Maria',
            'apellido' => 'Garcia',
            'avatar' => 'clientes/avatar.png',
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
        ]);//id user 3

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
        ]);//id user 4

        // Más clientes
        Cliente::create([
            'nombre' => 'Laura',
            'apellido' => 'Gómez',
            'avatar' => 'clientes/avatar.png',
            'telefono' => '611223344',
            'direccion' => [
                'calle' => 'Calle del Río',
                'numero' => 10,
                'piso' => 2,
                'letra' => 'B',
                'codigoPostal' => 28003
            ],
            'activo' => true,
            'usuario_id' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);//id user 5

        Cliente::create([
            'nombre' => 'Diego',
            'apellido' => 'Ruiz',
            'avatar' => 'clientes/avatar.png',
            'telefono' => '622334455',
            'direccion' => [
                'calle' => 'Calle del Bosque',
                'numero' => 15,
                'piso' => 1,
                'letra' => 'A',
                'codigoPostal' => 28004
            ],
            'activo' => true,
            'usuario_id' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);//id user 6

        Cliente::create([
            'nombre' => 'Sofía',
            'apellido' => 'López',
            'avatar' => 'clientes/avatar.png',
            'telefono' => '633445566',
            'direccion' => [
                'calle' => 'Calle del Mar',
                'numero' => 20,
                'piso' => 3,
                'letra' => 'C',
                'codigoPostal' => 28005
            ],
            'activo' => true,
            'usuario_id' => 7,
            'created_at' => now(),
            'updated_at' => now(),
        ]);//id user 7

        Cliente::create([
            'nombre' => 'Javier',
            'apellido' => 'Torres',
            'avatar' => 'clientes/avatar.png',
            'telefono' => '644556677',
            'direccion' => [
                'calle' => 'Calle del Sol',
                'numero' => 25,
                'piso' => 4,
                'letra' => 'D',
                'codigoPostal' => 28006
            ],
            'activo' => true,
            'usuario_id' => 8,
            'created_at' => now(),
            'updated_at' => now(),
        ]);//id user 8

        Cliente::create([
            'nombre' => 'Elena',
            'apellido' => 'Castro',
            'avatar' => 'clientes/avatar.png',
            'telefono' => '655667788',
            'direccion' => [
                'calle' => 'Calle de la Montaña',
                'numero' => 30,
                'piso' => 5,
                'letra' => 'E',
                'codigoPostal' => 28007
            ],
            'activo' => true,
            'usuario_id' => 9, // ID del nuevo usuario Elena Castro
            'created_at' => now(),
            'updated_at' => now(),
        ]);//id user 9

        Cliente::create([
            'nombre' => 'Ana',
            'apellido' => 'Lopez',
            'avatar' => 'clientes/avatar.png',
            'telefono' => '654321987',
            'direccion' => [
                'calle' => 'Calle del Sol',
                'numero' => 789,
                'piso' => 2,
                'letra' => 'D',
                'codigoPostal' => 28002
            ],
            'activo' => true,
            'usuario_id' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);//id user 10

        Cliente::create([
            'nombre' => 'Carlos',
            'apellido' => 'Fernandez',
            'avatar' => 'clientes/avatar.png',
            'telefono' => '987123654',
            'direccion' => [
                'calle' => 'Calle de la Luna',
                'numero' => 321,
                'piso' => 4,
                'letra' => 'E',
                'codigoPostal' => 28972
            ],
            'activo' => true,
            'usuario_id' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);//id user 11

        Cliente::create([
            'nombre' => 'Isabella',
            'apellido' => 'Rodriguez',
            'avatar' => 'clientes/avatar.png',
            'telefono' => '321987654',
            'direccion' => [
                'calle' => 'Avenida de las Estrellas',
                'numero' => 654,
                'piso' => 5,
                'letra' => 'F',
                'codigoPostal' => 28973
            ],
            'activo' => true,
            'usuario_id' => 7,
            'created_at' => now(),
            'updated_at' => now(),
        ]);//id user 12

        Cliente::create([
            'nombre' => 'Jose',
            'apellido' => 'Luis',
            'avatar' => 'clientes/avatar.png',
            'telefono' => '654789321',
            'direccion' => [
                'calle' => 'Calle del Cielo',
                'numero' => 987,
                'piso' => 6,
                'letra' => 'G',
                'codigoPostal' => 28974
            ],
            'activo' => true,
            'usuario_id' => 8,
            'created_at' => now(),
            'updated_at' => now(),
        ]);//id user 13

    }
}
