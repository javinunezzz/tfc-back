<!DOCTYPE html>
<html>
<head>
    <title>Nuevo Favorito</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
            margin-bottom: 20px;
        }
        h2 {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px 0;
        }
        .details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
        }
        .details li {
            list-style: none;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .details li:last-child {
            border-bottom: none;
        }
        .highlight {
            color: #3498db;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>¡Tienes un nuevo favorito! 🌟</h2>
        </div>

        <div class="content">
            <p>El usuario <span class="highlight">{{ $username }}</span> ha marcado como favorito tu apunte:</p>

            <div class="details">
                <ul>
                    <li><strong>📚 Título:</strong> {{ $apunte }}</li>
                    <li><strong>📂 Categoría:</strong> {{ $categoria }}</li>
                    <li><strong>📖 Asignatura:</strong> {{ $asignatura }}</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            <p>¡Gracias por compartir tus conocimientos con la comunidad! 🎓</p>
        </div>
    </div>
</body>
</html>