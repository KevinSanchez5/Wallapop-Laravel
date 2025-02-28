<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductosSeeder extends Seeder
{
    public function run(): void
    {

        Producto::create([
                'vendedor_id' => 1,
                'nombre' => 'Portátil Gamer',
                'descripcion' => 'Este potente portátil está diseñado para gaming de alto rendimiento y tareas exigentes como edición de video y modelado 3D. Equipado con un procesador de última generación, tarjeta gráfica dedicada y una pantalla de alta frecuencia de actualización, ofrece una experiencia fluida tanto para jugadores como para profesionales.',
                'estadoFisico' => 'Nuevo',
                'precio' => 800.00,
                'stock' => 100,
                'categoria' => 'Tecnologia',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['productos/portatil1.webp', 'productos/portatil2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        Producto::create([
                'vendedor_id' => 2,
                'nombre' => 'Chaqueta de Cuero',
                'descripcion' => 'Chaqueta de cuero genuino con un diseño clásico y sofisticado. Ideal para quienes buscan un look elegante sin sacrificar comodidad y protección contra el frío. Su forro interior aporta calidez, mientras que su material resistente garantiza una larga durabilidad.',
                'estadoFisico' => 'Usado',
                'precio' => 15.00,
                'stock' => 100,
                'categoria' => 'Ropa',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['productos/chaqueta1.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        Producto::create([
                'vendedor_id' =>3,
                'nombre' => 'Guitarra Eléctrica',
                'descripcion' => 'Guitarra eléctrica de cuerpo sólido con un diseño clásico y un sonido potente. Perfecta para músicos de cualquier nivel que buscan un instrumento versátil para rock, blues, jazz y más. Aunque presenta signos de uso, su sonido sigue siendo excepcional y está lista para conectar y tocar.',
                'estadoFisico' => 'Deteriorado',
                'precio' => 300.00,
                'stock' => 100,
                'categoria' => 'Musica',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['productos/guitarra1.webp', 'productos/guitarra2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        Producto::create([
                'vendedor_id' => 1,
                'nombre' => 'Pantalones Vaqueros',
                'descripcion' => 'Pantalones vaqueros de alta calidad, confeccionados con tela resistente y un ajuste cómodo. Ideales para el día a día o para combinarlos con distintos estilos. Su diseño clásico nunca pasa de moda y su durabilidad los hace una opción excelente para cualquier guardarropa.',
                'estadoFisico' => 'Nuevo',
                'precio' => 10.00,
                'stock' => 100,
                'categoria' => 'Ropa',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['productos/pantalones1.webp', 'productos/pantalones2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        Producto::create([
                'vendedor_id' => 2,
                'nombre' => 'Mario Party 8',
                'descripcion' => 'Divertido juego de fiesta para toda la familia. Mario Party 8 ofrece una gran variedad de minijuegos y tableros interactivos que garantizan horas de entretenimiento. Perfecto para jugar solo o en compañía de amigos y familiares, este título es un clásico de la saga de Nintendo.',
                'estadoFisico' => 'Nuevo',
                'precio' => 50.00,
                'stock' => 100,
                'categoria' => 'Videojuegos',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['productos/mario1.webp','productos/mario2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        Producto::create([
                'vendedor_id' => 3,
                'nombre' => 'Consola Xbox Series X',
                'descripcion' => 'La consola de nueva generación de Microsoft con un rendimiento excepcional. Disfruta de gráficos en 4K, tiempos de carga ultrarrápidos y una biblioteca de juegos extensa. Ideal para quienes buscan la mejor experiencia en videojuegos y entretenimiento en casa.',
                'estadoFisico' => 'Nuevo',
                'precio' => 250.00,
                'stock' => 100,
                'categoria' => 'Videojuegos',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['productos/xbox1.webp', 'productos/xbox2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        Producto::create([
                'vendedor_id' => 2,
                'nombre' => 'Set de Púas para Guitarra',
                'descripcion' => 'Paquete de 12 púas de diferentes grosores y materiales, ideales para distintos estilos musicales. Perfectas para guitarras acústicas y eléctricas.',
                'estadoFisico' => 'Nuevo',
                'precio' => 5.00,
                'stock' => 100,
                'categoria' => 'Musica',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['productos/puas1.webp', 'productos/puas2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        Producto::create([
                'vendedor_id' => 2,
                'nombre' => 'Amplificador Fender 40W',
                'descripcion' => 'Amplificador Fender de 40W con ecualización ajustable y efectos de reverberación. Ideal para ensayos y pequeñas presentaciones en vivo.',
                'estadoFisico' => 'Usado',
                'precio' => 120.00,
                'stock' => 100,
                'categoria' => 'Musica',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['productos/ampli1.webp', 'productos/ampli2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        Producto::create([
                'vendedor_id' => 1,
                'nombre' => 'Batería Electrónica Roland',
                'descripcion' => 'Kit de batería electrónica con pads sensibles al tacto, módulo de sonidos y conexión MIDI para grabaciones digitales.',
                'estadoFisico' => 'Nuevo',
                'precio' => 600.00,
                'stock' => 100,
                'categoria' => 'Musica',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['productos/bateria1.webp', 'productos/bateria2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        Producto::create([
                'vendedor_id' => 3,
                'nombre' => 'Smartwatch Xiaomi Mi Band 7',
                'descripcion' => 'Reloj inteligente con monitor de actividad física, sensor de frecuencia cardíaca y notificaciones de smartphone.',
                'estadoFisico' => 'Nuevo',
                'precio' => 50.00,
                'stock' => 100,
                'categoria' => 'Tecnologia',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['productos/smartwatch1.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        Producto::create([
                'vendedor_id' => 3,
                'nombre' => 'Zapatillas Adidas Running',
                'descripcion' => 'Zapatillas deportivas con suela de espuma de alto rendimiento. Comodidad y soporte ideal para correr largas distancias.',
                'estadoFisico' => 'Nuevo',
                'precio' => 70.00,
                'stock' => 100,
                'categoria' => 'Ropa',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['productos/zapatillas1.webp', 'productos/zapatillas2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        Producto::create([
                'vendedor_id' => 2,
                'nombre' => 'Lámpara LED Inteligente',
                'descripcion' => 'Lámpara de escritorio con luz LED regulable y control táctil. Compatible con asistentes de voz como Alexa y Google Home.',
                'estadoFisico' => 'Nuevo',
                'precio' => 30.00,
                'stock' => 100,
                'categoria' => 'Hogar',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['productos/lampara1.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        Producto::create([
                'vendedor_id' => 1,
                'nombre' => 'The Legend of Zelda: Breath of the Wild',
                'descripcion' => 'Juego de aventura y exploración para Nintendo Switch con un mundo abierto enorme y mecánicas innovadoras.',
                'estadoFisico' => 'Nuevo',
                'precio' => 55.00,
                'stock' => 100,
                'categoria' => 'Videojuegos',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['productos/zelda1.webp', 'productos/zelda2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        Producto::create([
                'vendedor_id' => 1,
                'nombre' => 'Colección de Blu-ray Star Wars',
                'descripcion' => 'Edición especial en Blu-ray de la saga completa de Star Wars, con contenido exclusivo y material detrás de cámaras.',
                'estadoFisico' => 'Usado',
                'precio' => 80.00,
                'stock' => 100,
                'categoria' => 'Cine',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['productos/starwars1.webp', 'productos/starwars2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        Producto::create([
                'vendedor_id' => 3,
                'nombre' => 'Cafetera Espresso Automática',
                'descripcion' => 'Cafetera con molinillo integrado y sistema de espumado de leche. Perfecta para preparar café de calidad en casa.',
                'estadoFisico' => 'Nuevo',
                'precio' => 150.00,
                'stock' => 100,
                'categoria' => 'Cocina',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['productos/cafetera1.webp', 'productos/cafetera2.webp']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        Producto::create([
                'vendedor_id' => 1,
                'nombre' => 'Figura de Acción Spider-Man',
                'descripcion' => 'Figura coleccionable de Spider-Man en edición especial con detalles realistas y articulaciones móviles.',
                'estadoFisico' => 'Nuevo',
                'precio' => 40.00,
                'stock' => 100,
                'categoria' => 'Coleccionismo',
                'estado' => 'Disponible',
                'imagenes' => json_encode(['productos/spiderman1.webp']),
                'created_at' => now(),
                'updated_at' => now(),

        ]);


        Producto::create([
            'guid' => 'P008',
            'vendedor_id' => 9,
            'nombre' => 'Monitor Gaming 27"',
            'descripcion' => 'Monitor de 27 pulgadas con tasa de refresco de 144Hz y resolución QHD. Ideal para gaming y diseño gráfico.',
            'estadoFisico' => 'Nuevo',
            'precio' => 300.00,
            'stock' => 10,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
            'imagenes' => json_encode(['productos/monitor1.webp', 'productos/monitor2.webp']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Producto::create([
            'guid' => 'P009',
            'vendedor_id' => 8,
            'nombre' => 'Teclado Mecánico RGB',
            'descripcion' => 'Teclado mecánico con retroiluminación RGB y switches Cherry MX. Perfecto para gamers y programadores.',
            'estadoFisico' => 'Nuevo',
            'precio' => 120.00,
            'stock' => 15,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
            'imagenes' => json_encode(['productos/teclado1.webp', 'productos/teclado2.webp']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Producto::create([
            'guid' => 'P010',
            'vendedor_id' => 7,
            'nombre' => 'Silla Gaming Ergonómica',
            'descripcion' => 'Silla ergonómica con soporte lumbar y reposacabezas ajustable. Ideal para largas sesiones de gaming o trabajo.',
            'estadoFisico' => 'Nuevo',
            'precio' => 200.00,
            'stock' => 5,
            'categoria' => 'Hogar',
            'estado' => 'Disponible',
            'imagenes' => json_encode(['productos/silla1.webp', 'productos/silla2.webp']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Producto::create([
            'guid' => 'P011',
            'vendedor_id' => 6,
            'nombre' => 'Auriculares Inalámbricos',
            'descripcion' => 'Auriculares con cancelación de ruido y sonido envolvente. Perfectos para música y llamadas.',
            'estadoFisico' => 'Nuevo',
            'precio' => 150.00,
            'stock' => 20,
            'categoria' => 'Tecnologia',
            'estado' => 'Disponible',
            'imagenes' => json_encode(['productos/auriculares1.webp', 'productos/auriculares2.webp']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Producto::create([
            'guid' => 'P012',
            'vendedor_id' => 5,
            'nombre' => 'Mesa de Oficina',
            'descripcion' => 'Mesa de oficina con diseño moderno y espacio amplio. Ideal para trabajar desde casa.',
            'estadoFisico' => 'Nuevo',
            'precio' => 180.00,
            'stock' => 8,
            'categoria' => 'Hogar',
            'estado' => 'Disponible',
            'imagenes' => json_encode(['productos/mesa1.webp', 'productos/mesa2.webp']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
