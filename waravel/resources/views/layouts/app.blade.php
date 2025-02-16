<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mi Sitio')</title>
    @vite(['resources/css/app.css', 'resources/js/carousel.js'])
</head>
<body class="bg-gray-100 min-h-screen">

<!-- Este es el heather que hay que cambiar si se ha iniciado sesion o no -->
<x-header />

<!-- Este es el contenido general que varia en cada pgina -->
<main class="container mx-auto py-6">
    @yield('content')
</main>


<!-- Este es el pie de página que NO varia -->
<x-footer />

</body>
</html>
