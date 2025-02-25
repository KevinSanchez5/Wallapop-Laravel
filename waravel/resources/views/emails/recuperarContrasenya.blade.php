
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
            text-align: center;
            padding: 20px;
        }
        .container {
    max-width: 500px;
            background: white;
            padding: 20px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
    color: #333;
}
.code {
    font-size: 24px;
            font-weight: bold;
            color: #d9534f;
            background: #f8d7da;
            padding: 10px;
            display: inline-block;
            border-radius: 5px;
            letter-spacing: 2px;
        }
        .footer {
    margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Recuperación de Contraseña</h2>
        <p>Hola <strong>{{ $usuario->name }}</strong>,</p>
<p>Has solicitado restablecer tu contraseña. Usa el siguiente código para completar el proceso:</p>
<p class="code">{{ $codigo }}</p>
<p>Si no solicitaste este cambio, ignora este mensaje.</p>
<p class="footer">Este código expira en 5 minutos.</p>
</div>
</body>
</html>
