<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 500px;
            background: white;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .banner {
            background-color: #BFF205;
            color: #333;
            text-align: center;
            padding: 15px;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .content {
            padding: 20px;
            text-align: center;
        }
        h2 {
            color: #2d2d2d;
        }
        p {
            color: #555;
            line-height: 1.6;
            margin: 10px 0;
        }

        .code {
            font-size: 28px;
            font-weight: bold;
            color: #d9534f;
            background: #f8d7da;
            padding: 10px 20px;
            display: inline-block;
            border-radius: 5px;
            letter-spacing: 4px;
            margin: 20px 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #A8D004;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn:hover {
            background-color: #91b903;
        }

        .footer {
            background: #f4f4f4;
            padding: 15px;
            font-size: 14px;
            color: #777;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="banner">
        Waravel
    </div>

    <div class="content">
        <h2>Recuperación de Contraseña</h2>
        <p>Hola <strong>{{ $usuario->name }}</strong>,</p>
        <p>Has solicitado restablecer tu contraseña. Usa el siguiente código para completar el proceso:</p>
        <p class="code">{{ $codigo }}</p>
        <p>Si no solicitaste este cambio, puedes ignorar este mensaje.</p>
        <a href="{{ route('pages.home') }}" class="btn">Ir a Waravel</a>
    </div>

    <div class="footer">
        © {{ date('Y') }} Waravel. Todos los derechos reservados.
    </div>
</div>
</body>
</html>
