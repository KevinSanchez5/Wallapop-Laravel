<?php

namespace Database\Seeders;

use App\Models\Venta;
use Illuminate\Database\Seeder;

class VentaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Venta::create([
            'guid'=>'kY8XqT5L9v3',
            'estado' =>'Pendiente',
            'comprador' => [
                'guid'=>'DU6jCZtareb',
                'id' => 2,
                'nombre' => 'Maria',
                'apellido' => 'Garcia'
            ],
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid'=>'2G6HueqixE5',
                        'id' => 1,
                        'nombre' => 'Juan',
                        'apellido' => 'Perez'
                    ],
                    'cantidad' => 2,
                    'producto' => [
                        'guid'=>'G4YXT9K5QLV',
                        'id' => 1,
                        'nombre' => 'Portatil Gamer',
                        'imagenes' => ['productos/portatil1.webp', 'productos/portatil2.webp'],
                        'descripcion' => 'Portatil gaming de gama alta para trabajos pesados.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 800.00,
                        'categoria' => 'Tecnologia'
                    ],
                    'precioTotal' => 2 * 800.00,
                ]
            ],
            'precioTotal' => 1600.00,
            'created_at' => now()
        ]);
        Venta::create([
            'guid'=>'Z4mT7pQX2Vy',
            'estado' =>'Procesando',
            'comprador' => [
                'guid'=>'yEC3KBt6CFY',
                'id' => 3,
                'nombre' => 'Pedro',
                'apellido' => 'Martinez'
            ],
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid'=>'2G6HueqixE5',
                        'id' => 1,
                        'nombre' => 'Juan',
                        'apellido' => 'Perez'
                    ],
                    'cantidad' => 1,
                    'producto' => [
                        'guid'=>'VYQ8XK4T9L5',
                        'id' => 4,
                        'nombre' => 'Pantalones Vaqueros',
                        'imagenes' => ['productos/pantalones1.webp', 'productos/pantalones2.webp'],
                        'descripcion' => 'Pantalones Vaqueros cómodos.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 10.00,
                        'categoria' => 'Ropa',
                    ],
                    'precioTotal' => 1 * 10.00
                ],
                [
                    'vendedor' => [
                        'guid'=>'DU6jCZtareb',
                        'id' => 2,
                        'nombre' => 'Maria',
                        'apellido' => 'Garcia'
                    ],
                    'cantidad' => 2,
                    'producto' => [
                        'guid'=>'T3K9QLYV7X5',
                        'id' => 5,
                        'nombre' => 'Mario Party 8',
                        'imagenes' => ['productos/mario1.webp','productos/mario2.webp'],
                        'descripcion' => 'Juego de plataformas y acción, muy popular en Nintendo.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 50.00,
                        'categoria' => 'Videojuegos',
                    ],
                    'precioTotal' => 2 * 50.00
                ]
            ],
            'precioTotal' => 110.00,
            'created_at' => now()
        ]);
        Venta::create([
            'guid' => 'V3XKYQ9T57L',
            'estado' => 'Entregado',
            'comprador' => [
                'guid' => 'G5Yt9XqK8VL',
                'id' => 5,
                'nombre' => 'Diego',
                'apellido' => 'Ruiz'
            ],
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid' => '2G6HueqixE5',
                        'id' => 1,
                        'nombre' => 'Juan',
                        'apellido' => 'Perez'
                    ],
                    'cantidad' => 1,
                    'producto' => [
                        'guid' => 'G4YXT9K5QLV',
                        'id' => 1,
                        'nombre' => 'Portátil Gamer',
                        'imagenes' => ['productos/portatil1.webp', 'productos/portatil2.webp'],
                        'descripcion' => 'Este potente portátil está diseñado para gaming de alto rendimiento.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 800.00,
                        'categoria' => 'Tecnologia'
                    ],
                    'precioTotal' => 800.00,
                ],
                [
                    'vendedor' => [
                        'guid' => 'DU6jCZtareb',
                        'id' => 2,
                        'nombre' => 'Maria',
                        'apellido' => 'Garcia'
                    ],
                    'cantidad' => 2,
                    'producto' => [
                        'guid' => '7K5TQL9XYV3',
                        'id' => 12,
                        'nombre' => 'Lámpara LED Inteligente',
                        'imagenes' => ['productos/lampara1.webp'],
                        'descripcion' => 'Lámpara de escritorio con luz LED regulable y control táctil.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 30.00,
                        'categoria' => 'Hogar'
                    ],
                    'precioTotal' => 60.00,
                ]
            ],
            'precioTotal' => 860.00,
            'created_at' => now()
        ]);

        Venta::create([
            'guid' => 'DU6jCZtareb',
            'estado' => 'Pendiente',
            'comprador' => [
                'guid' => 'X9vB7LpQ2ZM',
                'id' => 4,
                'nombre' => 'Laura',
                'apellido' => 'Gómez'
            ],
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid' => 'yEC3KBt6CFY',
                        'id' => 3,
                        'nombre' => 'Pedro',
                        'apellido' => 'Martinez'
                    ],
                    'cantidad' => 1,
                    'producto' => [
                        'guid' => 'L5X7YQT9VK3',
                        'id' => 6,
                        'nombre' => 'Consola Xbox Series X',
                        'imagenes' => ['productos/xbox1.webp', 'productos/xbox2.webp'],
                        'descripcion' => 'La consola de nueva generación de Microsoft con un rendimiento excepcional.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 250.00,
                        'categoria' => 'Videojuegos'
                    ],
                    'precioTotal' => 250.00,
                ],
                [
                    'vendedor' => [
                        'guid' => 'yEC3KBt6CFY',
                        'id' => 3,
                        'nombre' => 'Pedro',
                        'apellido' => 'Martinez'
                    ],
                    'cantidad' => 3,
                    'producto' => [
                        'guid' => 'X9YT7KQLV53',
                        'id' => 11,
                        'nombre' => 'Zapatillas Adidas Running',
                        'imagenes' => ['productos/zapatillas1.webp', 'productos/zapatillas2.webp'],
                        'descripcion' => 'Zapatillas deportivas con suela de espuma de alto rendimiento.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 70.00,
                        'categoria' => 'Ropa'
                    ],
                    'precioTotal' => 210.00,
                ]
            ],
            'precioTotal' => 460.00,
            'created_at' => now()
        ]);
        Venta::create([
            'guid' => '2G6HueqixE5',
            'estado' => 'Enviado',
            'comprador' => [
                'guid' => '2G6HueqixE5',
                'id' => 1,
                'nombre' => 'Juan',
                'apellido' => 'Perez'
            ],
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid' => 'yEC3KBt6CFY',
                        'id' => 3,
                        'nombre' => 'Pedro',
                        'apellido' => 'Martinez'
                    ],
                    'cantidad' => 1,
                    'producto' => [
                        'guid' => 'QX9T7LK5VY3',
                        'id' => 3,
                        'nombre' => 'Guitarra Eléctrica',
                        'imagenes' => ['productos/guitarra1.webp', 'productos/guitarra2.webp'],
                        'descripcion' => 'Guitarra eléctrica de cuerpo sólido con un diseño clásico y un sonido potente.',
                        'estadoFisico' => 'Deteriorado',
                        'precio' => 300.00,
                        'categoria' => 'Musica'
                    ],
                    'precioTotal' => 1 * 300.00,
                ]
            ],
            'precioTotal' => 300.00,
            'created_at' => now()
        ]);
        Venta::create([
            'guid' => 'G4YXT9K5QLV',
            'estado' => 'Pendiente',
            'comprador' => [
                'guid' => 'X9vB7LpQ2ZM',
                'id' => 4,
                'nombre' => 'Laura',
                'apellido' => 'Gómez'
            ],
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid' => '2G6HueqixE5',
                        'id' => 1,
                        'nombre' => 'Juan',
                        'apellido' => 'Perez'
                    ],
                    'cantidad' => 2,
                    'producto' => [
                        'guid' => 'VYQ8XK4T9L5',
                        'id' => 4,
                        'nombre' => 'Pantalones Vaqueros',
                        'imagenes' => ['productos/pantalones1.webp', 'productos/pantalones2.webp'],
                        'descripcion' => 'Pantalones vaqueros de alta calidad, confeccionados con tela resistente y un ajuste cómodo.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 10.00,
                        'categoria' => 'Ropa'
                    ],
                    'precioTotal' => 2 * 10.00,
                ],
                [
                    'vendedor' => [
                        'guid' => 'DU6jCZtareb',
                        'id' => 2,
                        'nombre' => 'Maria',
                        'apellido' => 'Garcia'
                    ],
                    'cantidad' => 1,
                    'producto' => [
                        'guid' => 'T3K9QLYV7X5',
                        'id' => 5,
                        'nombre' => 'Mario Party 8',
                        'imagenes' => ['productos/mario1.webp', 'productos/mario2.webp'],
                        'descripcion' => 'Divertido juego de fiesta para toda la familia.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 50.00,
                        'categoria' => 'Videojuegos'
                    ],
                    'precioTotal' => 1 * 50.00,
                ]
            ],
            'precioTotal' => 70.00,
            'created_at' => now()
        ]);
        Venta::create([
            'guid' => 'yEC3KBt6CFY',
            'estado' => 'Entregado',
            'comprador' => [
                'guid' => 'G5Yt9XqK8VL',
                'id' => 5,
                'nombre' => 'Diego',
                'apellido' => 'Ruiz'
            ],
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid' => 'yEC3KBt6CFY',
                        'id' => 3,
                        'nombre' => 'Pedro',
                        'apellido' => 'Martinez'
                    ],
                    'cantidad' => 1,
                    'producto' => [
                        'guid' => 'L5X7YQT9VK3',
                        'id' => 6,
                        'nombre' => 'Consola Xbox Series X',
                        'imagenes' => ['productos/xbox1.webp', 'productos/xbox2.webp'],
                        'descripcion' => 'La consola de nueva generación de Microsoft con un rendimiento excepcional.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 250.00,
                        'categoria' => 'Videojuegos'
                    ],
                    'precioTotal' => 1 * 250.00,
                ]
            ],
            'precioTotal' => 250.00,
            'created_at' => now()
        ]);
        Venta::create([
            'guid' => 'X9vB7LpQ2ZM',
            'estado' => 'Cancelado',
            'comprador' => [
                'guid' => '2G6HueqixE5',
                'id' => 1,
                'nombre' => 'Juan',
                'apellido' => 'Perez'
            ],
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid' => 'DU6jCZtareb',
                        'id' => 2,
                        'nombre' => 'Maria',
                        'apellido' => 'Garcia'
                    ],
                    'cantidad' => 3,
                    'producto' => [
                        'guid' => 'X8KQ5T9YLV7',
                        'id' => 7,
                        'nombre' => 'Set de Púas para Guitarra',
                        'imagenes' => ['productos/puas1.webp', 'productos/puas2.webp'],
                        'descripcion' => 'Paquete de 12 púas de diferentes grosores y materiales.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 5.00,
                        'categoria' => 'Musica'
                    ],
                    'precioTotal' => 3 * 5.00,
                ]
            ],
            'precioTotal' => 15.00,
            'created_at' => now()
        ]);
        Venta::create([
            'guid' => 'G5Yt9XqK8VL',
            'estado' => 'Devuelto',
            'comprador' => [
                'guid' => 'X9vB7LpQ2ZM',
                'id' => 4,
                'nombre' => 'Laura',
                'apellido' => 'Gómez'
            ],
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid' => 'DU6jCZtareb',
                        'id' => 2,
                        'nombre' => 'Maria',
                        'apellido' => 'Garcia'
                    ],
                    'cantidad' => 1,
                    'producto' => [
                        'guid' => 'Y9VQXK37TL5',
                        'id' => 8,
                        'nombre' => 'Amplificador Fender 40W',
                        'imagenes' => ['productos/ampli1.webp', 'productos/ampli2.webp'],
                        'descripcion' => 'Amplificador Fender de 40W con ecualización ajustable y efectos de reverberación.',
                        'estadoFisico' => 'Usado',
                        'precio' => 120.00,
                        'categoria' => 'Musica'
                    ],
                    'precioTotal' => 1 * 120.00,
                ]
            ],
            'precioTotal' => 120.00,
            'created_at' => now()
        ]);
        Venta::create([
            'guid' => 'Z8K3VLYTQ72',
            'estado' => 'Procesando',
            'comprador' => [
                'guid' => 'DU6jCZtareb',
                'id' => 2,
                'nombre' => 'Maria',
                'apellido' => 'Garcia'
            ],
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid' => '2G6HueqixE5',
                        'id' => 1,
                        'nombre' => 'Juan',
                        'apellido' => 'Perez'
                    ],
                    'cantidad' => 1,
                    'producto' => [
                        'guid' => 'K7YLTQ9X5V3',
                        'id' => 9,
                        'nombre' => 'Batería Electrónica Roland',
                        'imagenes' => ['productos/bateria1.webp', 'productos/bateria2.webp'],
                        'descripcion' => 'Kit de batería electrónica con pads sensibles al tacto, módulo de sonidos y conexión MIDI para grabaciones digitales.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 600.00,
                        'categoria' => 'Musica'
                    ],
                    'precioTotal' => 1 * 600.00,
                ]
            ],
            'precioTotal' => 600.00,
            'created_at' => now()
        ]);
        Venta::create([
            'guid' => 'QX9T7LK5VY3',
            'estado' => 'Entregado',
            'comprador' => [
                'guid' => 'DU6jCZtareb',
                'id' => 2,
                'nombre' => 'Maria',
                'apellido' => 'Garcia'
            ],
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid' => 'yEC3KBt6CFY',
                        'id' => 3,
                        'nombre' => 'Pedro',
                        'apellido' => 'Martinez'
                    ],
                    'cantidad' => 2,
                    'producto' => [
                        'guid' => 'QX5V9KY3TL7',
                        'id' => 10,
                        'nombre' => 'Smartwatch Xiaomi Mi Band 7',
                        'imagenes' => ['productos/smartwatch1.webp'],
                        'descripcion' => 'Reloj inteligente con monitor de actividad física, sensor de frecuencia cardíaca y notificaciones de smartphone.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 50.00,
                        'categoria' => 'Tecnologia'
                    ],
                    'precioTotal' => 2 * 50.00,
                ]
            ],
            'precioTotal' => 100.00,
            'created_at' => now()
        ]);
        Venta::create([
            'guid' => 'VYQ8XK4T9L5',
            'estado' => 'Entregado',
            'comprador' => [
                'guid' => '2G6HueqixE5',
                'id' => 1,
                'nombre' => 'Juan',
                'apellido' => 'Perez'
            ],
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid' => 'yEC3KBt6CFY',
                        'id' => 3,
                        'nombre' => 'Pedro',
                        'apellido' => 'Martinez'
                    ],
                    'cantidad' => 1,
                    'producto' => [
                        'guid' => 'X9YT7KQLV53',
                        'id' => 11,
                        'nombre' => 'Zapatillas Adidas Running',
                        'imagenes' => ['productos/zapatillas1.webp', 'productos/zapatillas2.webp'],
                        'descripcion' => 'Zapatillas deportivas con suela de espuma de alto rendimiento.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 70.00,
                        'categoria' => 'Ropa'
                    ],
                    'precioTotal' => 1 * 70.00,
                ],
                [
                    'vendedor' => [
                        'guid' => 'DU6jCZtareb',
                        'id' => 2,
                        'nombre' => 'Maria',
                        'apellido' => 'Garcia'
                    ],
                    'cantidad' => 1,
                    'producto' => [
                        'guid' => '7K5TQL9XYV3',
                        'id' => 12,
                        'nombre' => 'Lámpara LED Inteligente',
                        'imagenes' => ['productos/lampara1.webp'],
                        'descripcion' => 'Lámpara de escritorio con luz LED regulable y control táctil.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 30.00,
                        'categoria' => 'Hogar'
                    ],
                    'precioTotal' => 1 * 30.00,
                ]
            ],
            'precioTotal' => 100.00,
            'created_at' => now()
        ]);
        Venta::create([
            'guid' => 'VYQ8gfpe3ti',
            'estado' => 'Cancelado',
            'comprador' => [
                'guid' => 'yEC3KBt6CFY',
                'id' => 3,
                'nombre' => 'Pedro',
                'apellido' => 'Martinez'
            ],
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid' => 'DU6jCZtareb',
                        'id' => 2,
                        'nombre' => 'Maria',
                        'apellido' => 'Garcia'
                    ],
                    'cantidad' => 1,
                    'producto' => [
                        'guid' => 'Z8K3VLYTQ72',
                        'id' => 2,
                        'nombre' => 'Chaqueta de Cuero',
                        'imagenes' => ['productos/chaqueta1.webp'],
                        'descripcion' => 'Chaqueta de cuero genuino con un diseño clásico y sofisticado.',
                        'estadoFisico' => 'Usado',
                        'precio' => 15.00,
                        'categoria' => 'Ropa'
                    ],
                    'precioTotal' => 1 * 15.00,
                ]
            ],
            'precioTotal' => 15.00,
            'created_at' => now()
        ]);
        Venta::create([
            'guid' => 'feg6tK4T9L5',
            'estado' => 'Enviado',
            'comprador' => [
                'guid' => 'X9vB7LpQ2ZM',
                'id' => 4,
                'nombre' => 'Laura',
                'apellido' => 'Gómez'
            ],
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid' => 'DU6jCZtareb',
                        'id' => 2,
                        'nombre' => 'Maria',
                        'apellido' => 'Garcia'
                    ],
                    'cantidad' => 2,
                    'producto' => [
                        'guid' => 'T3K9QLYV7X5',
                        'id' => 5,
                        'nombre' => 'Mario Party 8',
                        'imagenes' => ['productos/mario1.webp', 'productos/mario2.webp'],
                        'descripcion' => 'Divertido juego de fiesta para toda la familia.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 50.00,
                        'categoria' => 'Videojuegos'
                    ],
                    'precioTotal' => 2 * 50.00,
                ]
            ],
            'precioTotal' => 100.00,
            'created_at' => now()
        ]);
    }
}
