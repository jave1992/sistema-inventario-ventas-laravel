<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error del sistema</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #111827;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .error-box {
            background: #1f2937;
            padding: 40px;
            border-radius: 16px;
            text-align: center;
            max-width: 450px;
            width: 90%;
            box-sizing: border-box;
        }

        .icon {
            font-size: 55px;
        }

        h1 {
            color: #f87171;
            font-size: 28px;
        }

        p {
            color: #d1d5db;
            line-height: 1.6;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <div class="error-box">
        <div class="icon">⚠️</div>

        <h1>¡Ha ocurrido un error!</h1>

        <p>{{ $message ?? 'Ocurrió un problema inesperado.' }}</p>

        <a href="{{ url('/') }}">Volver al inicio</a>
    </div>
</body>

</html>