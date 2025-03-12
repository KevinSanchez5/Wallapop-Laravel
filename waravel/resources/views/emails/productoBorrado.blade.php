<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producto Baneado - Waravel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #e63946;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            color: #e63946;
        }
        .content {
            font-size: 16px;
            color: #333;
        }
        .content strong {
            color: #e63946;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Producto Baneado</h2>
    </div>
    <div class="content">
        <p>Hola, <strong>{{ $usuario->name }}</strong>,</p>
        <p>Te informamos que tu producto <strong>{{ $producto->nombre }}</strong> ha sido baneado de Waravel debido a un incumplimiento de nuestras normas.</p>
        <p>Si crees que esto ha sido un error o deseas más información, por favor, contáctanos.</p>
        <p>Gracias por tu comprensión.</p>
    </div>
    <div class="footer">
        <p>&copy; 2025 Waravel. Todos los derechos reservados.</p>
    </div>
</div>
</body>
</html>
