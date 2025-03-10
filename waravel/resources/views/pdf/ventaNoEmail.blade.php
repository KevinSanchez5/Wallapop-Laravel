<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Compra PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 20px;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 20px;
        }
        .header svg {
            fill: rgb(191, 242, 5);
            width: 100px;
            height: auto;
        }
        .content {
            margin-bottom: 20px;
        }
        .content table {
            width: 100%;
            border-collapse: collapse;
        }
        .content table, .content th, .content td {
            border: 1px solid black;
        }
        .content th, .content td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 34.45 30.81">
            <circle cx="8.18" cy="6.32" r="6.32"/>
            <circle cx="26.27" cy="6.32" r="6.32"/>
            <path d="M14.91 30.81a1.44 1.44 0 0 0 1.44-1.44V15.9a1.44 1.44 0 0 0-1.44-1.44H1.44A1.44 1.44 0 0 0 0 15.9c0 7.1 7.6 14.91 14.91 14.91Z"/>
            <path d="M19.54 30.81a1.44 1.44 0 0 1-1.44-1.44V15.9a1.44 1.44 0 0 1 1.44-1.44h13.47a1.44 1.44 0 0 1 1.44 1.44c0 7.1-7.6 14.91-14.91 14.91Z"/>
        </svg>
        <h1>Factura de Compra</h1>
    </div>
    <div class="content">
        <h2>Detalles de la Compra</h2>
        <p><strong>Referencia:</strong> {{ $venta->guid }}</p>
        <p><strong>Estado:</strong> {{ $venta->estado }}</p>
        <p><strong>Fecha de pedido:</strong> {{ $venta->created_at }}</p>

        <h2>Detalles del Comprador</h2>
        <p><strong>Nombre:</strong> {{ $venta->comprador->nombre }}</p>
        <p><strong>Apellido:</strong> {{ $venta->comprador->apellido }}</p>

        <h2>Detalles de la Compra</h2>
        <table>
            <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>IVA</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($venta->lineaVentas as $linea)
                @php
                    $precioSinIva = $linea->producto->precio / 1.21;
                    $iva = $linea->producto->precio - $precioSinIva;
                @endphp
                <tr>
                    <td>{{ $linea->producto->nombre }}</td>
                    <td>{{ $linea->cantidad }}</td>
                    <td>{{ number_format($precioSinIva, 2) }} €</td>
                    <td>{{ number_format($iva, 2) }} €</td>
                    <td>{{ $linea->precioTotal }} €</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2"><strong>Total con IVA</strong></td>
                <td>{{number_format($venta->precioTotal / 1.21, 2) }} €</td>
                <td>{{$venta->precioTotal - number_format($venta->precioTotal / 1.21, 2) }} €</td>
                <td>{{ number_format($venta->precioTotal)}} €</td>
            </tr>
            </tfoot>
        </table>

        <h2>Total de la Compra</h2>
        <p><strong>Subtotal:</strong> {{number_format($venta->precioTotal / 1.21, 2) }} €</p>
        <p><strong>IVA (21%):</strong> {{$venta->precioTotal - number_format($venta->precioTotal / 1.21, 2) }} €</p>
        <p><strong>Total con IVA:</strong> {{ number_format($venta->precioTotal)}} €</p>
    </div>
    <div class="footer">
        <p>Gracias por su compra</p>
    </div>
</div>
</body>
</html>
