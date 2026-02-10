<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gráfica de Porcentajes</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin: 24px; }
        .wrap { display: grid; grid-template-columns: 420px 1fr; gap: 18px; align-items: start; }
        .card { border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; }
        .error { color: #b91c1c; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #eee; padding: 8px; text-align: left; }
        th { background: #f9fafb; }
        .topbar { display:flex; gap: 12px; align-items:center; margin-bottom: 14px; }
        select { padding: 6px; }
        button { padding: 8px 12px; border: 0; border-radius: 8px; background: #111827; color: white; cursor:pointer; }
        a { color:#2563eb; text-decoration:none; }
    </style>
</head>
<body>
    <div class="topbar">
        <a href="{{ route('import.form') }}">Importar otro Excel</a>
    </div>

    @if ($error)
        <div class="card">
            <div class="error">{{ $error }}</div>
        </div>
    @else
        <div class="wrap">
            <div class="card">
                <h2>% por Cliente</h2>
                <form method="get" action="{{ route('grafica') }}">
                    <div style="display:grid; gap: 10px;">
                        <div>
                            <label for="anio" style="font-weight:600;">Año</label>
                            <select id="anio" name="anio">
                                <option value="">(Todos)</option>
                                @foreach ($availableYears as $y)
                                    <option value="{{ $y }}" @selected($selectedYear === $y)>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="mes" style="font-weight:600;">Mes</label>
                            <select id="mes" name="mes[]" multiple size="8" style="width:100%;">
                                @foreach ($availableMonths as $m)
                                    <option value="{{ $m }}" @selected(in_array($m, $selectedMonths, true))>{{ $m }}</option>
                                @endforeach
                            </select>
                            <div style="font-size:12px; color:#6b7280; margin-top:6px;">Ctrl + click para seleccionar varios</div>
                        </div>
                        <button type="submit">Aplicar filtros</button>
                    </div>
                </form>

                <div style="margin-top:12px; font-size: 12px; color:#6b7280;">
                    Total Unidades: {{ number_format($totalUnits, 2, '.', ',') }}
                </div>
            </div>

            <div class="card">
                <canvas id="pie" height="140"></canvas>

                <div style="margin-top: 18px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Unidades</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($unitsByLabel as $label => $units)
                                @php
                                    $idx = array_search($label, $labels, true);
                                    $pct = $idx === false ? 0 : $percentages[$idx];
                                @endphp
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td>{{ number_format($units, 2, '.', ',') }}</td>
                                    <td>{{ number_format($pct, 2, '.', ',') }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
            const labels = @json($labels);
            const data = @json($percentages);

            const colors = labels.map((_, i) => `hsl(${(i * 47) % 360} 70% 55% / 0.85)`);

            new Chart(document.getElementById('pie'), {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data,
                        backgroundColor: colors,
                        borderColor: colors.map(c => c.replace('/ 0.85', '/ 1')),
                        borderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'right' },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.label}: ${ctx.parsed}%`
                            }
                        }
                    }
                }
            });
        </script>
    @endif
</body>
</html>
