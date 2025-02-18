<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductosSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'guid' => '1f0368b0-5ce9-4099-bd13-0c1cede8d349',
                'vendedor_id' => 1,
                'nombre' => 'Portátil Gamer',
                'descripcion' => 'Este potente portátil está diseñado para gaming de alto rendimiento y tareas exigentes como edición de video y modelado 3D. Equipado con un procesador de última generación, tarjeta gráfica dedicada y una pantalla de alta frecuencia de actualización, ofrece una experiencia fluida tanto para jugadores como para profesionales.',
                'estadoFisico' => 'Nuevo',
                'precio' => 800.00,
                'categoria' => 'Tecnologia',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['portatil1.webp', 'portatil2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guid' => 'b4cb952d-1568-4763-901a-dfb9e05a4992',
                'vendedor_id' => 2,
                'nombre' => 'Chaqueta de Cuero',
                'descripcion' => 'Chaqueta de cuero genuino con un diseño clásico y sofisticado. Ideal para quienes buscan un look elegante sin sacrificar comodidad y protección contra el frío. Su forro interior aporta calidez, mientras que su material resistente garantiza una larga durabilidad.',
                'estadoFisico' => 'Usado',
                'precio' => 15.00,
                'categoria' => 'Ropa',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['chaqueta1.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guid' => '8b09d444-554b-4b95-9962-d6d64d46719c',
                'vendedor_id' => 3,
                'nombre' => 'Guitarra Eléctrica',
                'descripcion' => 'Guitarra eléctrica de cuerpo sólido con un diseño clásico y un sonido potente. Perfecta para músicos de cualquier nivel que buscan un instrumento versátil para rock, blues, jazz y más. Aunque presenta signos de uso, su sonido sigue siendo excepcional y está lista para conectar y tocar.',
                'estadoFisico' => 'Deteriorado',
                'precio' => 300.00,
                'categoria' => 'Musica',
                'estado' => 'Vendido',
                'imagenes' => json_encode(['guitarra1.webp', 'guitarra2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guid' => 'f047b42f-5151-4497-a300-7e78794c850f',
                'vendedor_id' => 1,
                'nombre' => 'Pantalones Vaqueros',
                'descripcion' => 'Pantalones vaqueros de alta calidad, confeccionados con tela resistente y un ajuste cómodo. Ideales para el día a día o para combinarlos con distintos estilos. Su diseño clásico nunca pasa de moda y su durabilidad los hace una opción excelente para cualquier guardarropa.',
                'estadoFisico' => 'Nuevo',
                'precio' => 10.00,
                'categoria' => 'Ropa',
                'estado' => 'Vendido',
                'imagenes' => json_encode(['pantalones1.webp', 'pantalones2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guid' => 'b060d2c6-9386-46a7-88c8-607850d75585',
                'vendedor_id' => 2,
                'nombre' => 'Mario Party 8',
                'descripcion' => 'Divertido juego de fiesta para toda la familia. Mario Party 8 ofrece una gran variedad de minijuegos y tableros interactivos que garantizan horas de entretenimiento. Perfecto para jugar solo o en compañía de amigos y familiares, este título es un clásico de la saga de Nintendo.',
                'estadoFisico' => 'Nuevo',
                'precio' => 50.00,
                'categoria' => 'Videojuegos',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['mario1.webp','mario2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guid' => '00f26236-958a-4043-8756-60430d98040d',
                'vendedor_id' => 3,
                'nombre' => 'Consola Xbox Series X',
                'descripcion' => 'La consola de nueva generación de Microsoft con un rendimiento excepcional. Disfruta de gráficos en 4K, tiempos de carga ultrarrápidos y una biblioteca de juegos extensa. Ideal para quienes buscan la mejor experiencia en videojuegos y entretenimiento en casa.',
                'estadoFisico' => 'Nuevo',
                'precio' => 250.00,
                'categoria' => 'Videojuegos',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['xbox1.webp', 'xbox2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            //nuevos
            [
                'guid' => "a7f42c1b-3d5f-4a8d-9e79-2d3a87eb8c12",
                'vendedor_id' => 2,
                'nombre' => 'Set de Púas para Guitarra',
                'descripcion' => 'Paquete de 12 púas de diferentes grosores y materiales, ideales para distintos estilos musicales. Perfectas para guitarras acústicas y eléctricas.',
                'estadoFisico' => 'Nuevo',
                'precio' => 5.00,
                'categoria' => 'Musica',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['puas1.webp', 'puas2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guid' => "b3d62a5c-83f7-47dc-b02a-98e5f8a28e9d",
                'vendedor_id' => 2,
                'nombre' => 'Amplificador Fender 40W',
                'descripcion' => 'Amplificador Fender de 40W con ecualización ajustable y efectos de reverberación. Ideal para ensayos y pequeñas presentaciones en vivo.',
                'estadoFisico' => 'Usado',
                'precio' => 120.00,
                'categoria' => 'Musica',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['ampli1.webp', 'ampli2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guid' => "cf89d4b6-276f-4f15-bd16-77c531dc1b3d",
                'vendedor_id' => 1,
                'nombre' => 'Batería Electrónica Roland',
                'descripcion' => 'Kit de batería electrónica con pads sensibles al tacto, módulo de sonidos y conexión MIDI para grabaciones digitales.',
                'estadoFisico' => 'Nuevo',
                'precio' => 600.00,
                'categoria' => 'Musica',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['bateria1.webp', 'bateria2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guid' => "d2f86a6a-89c3-4e1a-84b2-0d8473a2b4e1",
                'vendedor_id' => 3,
                'nombre' => 'Smartwatch Xiaomi Mi Band 7',
                'descripcion' => 'Reloj inteligente con monitor de actividad física, sensor de frecuencia cardíaca y notificaciones de smartphone.',
                'estadoFisico' => 'Nuevo',
                'precio' => 50.00,
                'categoria' => 'Tecnologia',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['smartwatch1.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guid' => "e91a8d56-5eb6-43b5-b847-b9d35ef0d30e",
                'vendedor_id' => 3,
                'nombre' => 'Zapatillas Adidas Running',
                'descripcion' => 'Zapatillas deportivas con suela de espuma de alto rendimiento. Comodidad y soporte ideal para correr largas distancias.',
                'estadoFisico' => 'Nuevo',
                'precio' => 70.00,
                'categoria' => 'Ropa',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['zapatillas1.webp', 'zapatillas2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guid' => "f4c1d6e3-69bf-4a17-9c8e-1bd48b92b25e",
                'vendedor_id' => 2,
                'nombre' => 'Lámpara LED Inteligente',
                'descripcion' => 'Lámpara de escritorio con luz LED regulable y control táctil. Compatible con asistentes de voz como Alexa y Google Home.',
                'estadoFisico' => 'Nuevo',
                'precio' => 30.00,
                'categoria' => 'Hogar',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['lampara1.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guid' => "09c7a5f9-30b7-470a-98c1-22c3c7b6e5fd",
                'vendedor_id' => 1,
                'nombre' => 'The Legend of Zelda: Breath of the Wild',
                'descripcion' => 'Juego de aventura y exploración para Nintendo Switch con un mundo abierto enorme y mecánicas innovadoras.',
                'estadoFisico' => 'Nuevo',
                'precio' => 55.00,
                'categoria' => 'Videojuegos',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['zelda1.webp', 'zelda2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guid' => "b5a36f42-44f8-4e6d-8f14-1c3a98c5f9d7",
                'vendedor_id' => 1,
                'nombre' => 'Colección de Blu-ray Star Wars',
                'descripcion' => 'Edición especial en Blu-ray de la saga completa de Star Wars, con contenido exclusivo y material detrás de cámaras.',
                'estadoFisico' => 'Usado',
                'precio' => 80.00,
                'categoria' => 'Cine',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['starwars1.webp', 'starwars2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guid' => "d7e41a94-8a8f-48dc-96e6-62b8c839f27b",
                'vendedor_id' => 3,
                'nombre' => 'Cafetera Espresso Automática',
                'descripcion' => 'Cafetera con molinillo integrado y sistema de espumado de leche. Perfecta para preparar café de calidad en casa.',
                'estadoFisico' => 'Nuevo',
                'precio' => 150.00,
                'categoria' => 'Cocina',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['cafetera1.webp', 'cafetera2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guid' => "c8f972b6-3f44-4b0f-8e6f-7a5c961f9b3d",
                'vendedor_id' => 1,
                'nombre' => 'Figura de Acción Spider-Man',
                'descripcion' => 'Figura coleccionable de Spider-Man en edición especial con detalles realistas y articulaciones móviles.',
                'estadoFisico' => 'Nuevo',
                'precio' => 40.00,
                'categoria' => 'Coleccionismo',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['spiderman1.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        Producto::insert($productos);
    }
}
