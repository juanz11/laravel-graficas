<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Importaciones</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 1.5rem;
            font-size: 2rem;
        }

        .actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-success {
            background: #28a745;
            color: white;
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }

        .btn-success:hover {
            background: #218838;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        thead {
            background: #f8f9fa;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        th {
            font-weight: 600;
            color: #495057;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .badge-active {
            background: #d4edda;
            color: #155724;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            list-style: none;
        }

        .pagination a, .pagination span {
            padding: 0.5rem 1rem;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            text-decoration: none;
            color: #667eea;
        }

        .pagination .active span {
            background: #667eea;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Historial de Importaciones</h1>

        <div class="actions">
            <a href="{{ route('grafica') }}" class="btn btn-primary">Ver Gráfica</a>
            <a href="{{ route('import.form') }}" class="btn btn-secondary">Nueva Importación</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($importaciones->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Archivo</th>
                        <th>Fecha Importación</th>
                        <th>Registros</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($importaciones as $importacion)
                        <tr>
                            <td>#{{ $importacion->id }}</td>
                            <td>{{ $importacion->archivo_nombre }}</td>
                            <td>{{ $importacion->fecha_importacion->format('d/m/Y H:i') }}</td>
                            <td>{{ number_format($importacion->registros_count ?? $importacion->registros->count()) }}</td>
                            <td>
                                @if($importacion->id == $importacionActualId)
                                    <span class="badge badge-active">Activa</span>
                                @endif
                            </td>
                            <td>
                                @if($importacion->id != $importacionActualId)
                                    <form method="POST" action="{{ route('historial.seleccionar', $importacion->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success">Seleccionar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $importaciones->links() }}
        @else
            <div class="empty-state">
                <p>No hay importaciones registradas.</p>
                <a href="{{ route('import.form') }}" class="btn btn-primary" style="margin-top: 1rem; display: inline-block;">Importar Primer Archivo</a>
            </div>
        @endif
    </div>
</body>
</html>
