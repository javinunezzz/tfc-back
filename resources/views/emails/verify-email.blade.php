<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de correo</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #2193b0, #6dd5ed);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: #ffffff;
            color: #2c3e50;
            padding: 40px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
            position: relative;
            overflow: hidden;
        }
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(to right, #2193b0, #6dd5ed);
        }
        h1 {
            font-size: 2em;
            margin-bottom: 25px;
            color: #2193b0;
            font-weight: 600;
            letter-spacing: -0.5px;
        }
        p {
            font-size: 1.1em;
            margin-bottom: 30px;
            line-height: 1.6;
            color: #5a6c7d;
        }
        button, a {
            display: inline-block;
            padding: 15px 35px;
            background: linear-gradient(45deg, #2193b0, #6dd5ed);
            color: #fff;
            font-size: 1.1em;
            font-weight: 500;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(33, 147, 176, 0.2);
        }
        button:hover, a:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(33, 147, 176, 0.3);
            background: linear-gradient(45deg, #1c7a94, #5bc3db);
        }
        footer {
            margin-top: 35px;
            font-size: 0.9em;
            color: #94a3b8;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Verifica tu correo electrónico</h1>
    <p>Haz clic en el siguiente botón para verificar tu cuenta y comenzar a usar NoteShare:</p>
    <a href="{{ $verificationLink }}">Verificar correo</a>
    <footer>
        &copy; 2025 NoteShare. Todos los derechos reservados.
    </footer>
</div>
</body>
</html>
