<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Importar Excel</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin: 24px; }
        .card { max-width: 720px; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; }
        label { display:block; font-weight: 600; margin-top: 12px; }
        input[type=file], select { width: 100%; padding: 8px; margin-top: 6px; }
        button { margin-top: 16px; padding: 10px 14px; border: 0; border-radius: 8px; background: #111827; color: white; cursor:pointer; }
        .error { color: #b91c1c; margin-top: 10px; }
        .links { margin-top: 12px; }
        .links a { color:#2563eb; text-decoration:none; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Importar Excel</h1>

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="post" action="{{ route('import') }}" enctype="multipart/form-data">
            @csrf

            <label for="excel">Archivo Excel (.xls/.xlsx)</label>
            <input id="excel" name="excel" type="file" accept=".xls,.xlsx,.xlsm" required>

            <button type="submit">Importar</button>
        </form>

        <div class="links">
            <a href="{{ route('grafica') }}">Ver gráfica</a>
        </div>
    </div>
</body>
</html>
