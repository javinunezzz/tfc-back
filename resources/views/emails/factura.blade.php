<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura de Suscripción</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            color: #2c3e50;
            background-color: #f8f9fa;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            color: #246fa0;
            padding: 20px;
            background: linear-gradient(145deg, #ffffff, #f5f7fa);
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(44, 62, 80, 0.1);
        }
        .header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }
        .header h2 {
            margin: 10px 0 0;
            color: #2c3e50;
        }
        .invoice-details, .customer-details {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(44, 62, 80, 0.08);
        }
        .customer-details h3 {
            color: #246fa0;
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(44, 62, 80, 0.08);
        }
        th, td {
            border: 1px solid #e9ecef;
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: #246fa0;
            color: white;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .total {
            text-align: right;
            margin-top: 20px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(44, 62, 80, 0.08);
        }
        .total p {
            margin: 10px 0;
            font-size: 1.1em;
        }
        .total p:last-child {
            color: #246fa0;
            font-size: 1.3em;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            color: #6c757d;
            border-top: 2px solid #e9ecef;
        }
        strong {
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FACTURA</h1>
        <h2>NoteShare</h2>
    </div>

    <div class="invoice-details">
        <p><strong>Número de Factura:</strong> {{ date('Ymd') }}-{{ $suscripcion->id }}</p>
        <p><strong>Fecha:</strong> {{ $suscripcion->fecha_inicio }}</p>
    </div>

    <div class="customer-details">
        <h3>Detalles del Cliente</h3>
        <p><strong>Nombre:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Período</th>
                <th>Precio</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Suscripción Premium NoteShare</td>
                <td>{{ $suscripcion->fecha_inicio }} - {{ $suscripcion->fecha_fin }}</td>
                <td>4.13€</td>
            </tr>
        </tbody>
    </table>

    <div class="total">
        <p><strong>Subtotal:</strong> 4.13€</p>
        <p><strong>IVA (21%):</strong> 0.87€</p>
        <p><strong>Total:</strong> 5.00€</p>
    </div>

    <div class="footer">
        <p>Gracias por confiar en NoteShare</p>
    </div>
</body>
</html>
