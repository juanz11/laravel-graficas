@extends('layouts.admin')

@section('title', 'Gráficas y Estadísticas')

@push('styles')
<style>
    .filters-card {
        background: white;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .form-select {
        padding: 0.75rem;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.3s;
    }

    .form-select:focus {
        outline: none;
        border-color: #667eea;
    }

    .btn-filter {
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .chart-controls {
        background: white;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
    }

    .chart-type-buttons {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .btn-chart-type {
        padding: 0.75rem 1.5rem;
        border: 2px solid #e9ecef;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-chart-type:hover {
        border-color: #667eea;
        background: #f8f9ff;
    }

    .btn-chart-type.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: #667eea;
    }

    .charts-grid {
        display: grid;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .chart-card {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .chart-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .chart-container {
        position: relative;
        height: 400px;
    }

    .stats-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-box {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        text-align: center;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        color: #6c757d;
        font-size: 0.85rem;
    }

    .table-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    th {
        padding: 1rem 1.5rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.9rem;
    }

    td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f1f3f5;
        color: #495057;
    }

    tbody tr:hover {
        background: #f8f9fa;
    }

    .error-card {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
        padding: 1.5rem;
        border-radius: 15px;
        margin-bottom: 1.5rem;
    }

    .export-buttons {
        display: flex;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .btn-export {
        padding: 0.5rem 1rem;
        border: 2px solid #667eea;
        background: white;
        color: #667eea;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-export:hover {
        background: #667eea;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">Gráficas y Estadísticas</h1>
    <p class="page-description">Visualiza y analiza los datos de ventas con diferentes tipos de gráficas.</p>
</div>

@if ($error)
    <div class="error-card">
        <strong>⚠️ Error:</strong> {{ $error }}
    </div>
@else
    <!-- Resumen de estadísticas -->
    <div class="stats-summary">
        <div class="stat-box">
            <div class="stat-value">{{ number_format($totalUnits, 0) }}</div>
            <div class="stat-label">Total Unidades</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ count($labels) }}</div>
            <div class="stat-label">Clientes</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $selectedYear ?? 'Todos' }}</div>
            <div class="stat-label">Año Seleccionado</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ count($selectedMonths) > 0 ? count($selectedMonths) : 'Todos' }}</div>
            <div class="stat-label">Meses Filtrados</div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-card">
        <form method="GET" action="{{ route('grafica') }}">
            <div class="filters-grid">
                <div class="form-group">
                    <label for="anio" class="form-label">Año</label>
                    <select id="anio" name="anio" class="form-select">
                        <option value="">Todos los años</option>
                        @foreach ($availableYears as $y)
                            <option value="{{ $y }}" @selected($selectedYear === $y)>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="mes" class="form-label">Meses (Ctrl + clic para múltiples)</label>
                    <select id="mes" name="mes[]" multiple class="form-select" style="height: 120px;">
                        @foreach ($availableMonths as $m)
                            <option value="{{ $m }}" @selected(in_array($m, $selectedMonths, true))>
                                Mes {{ $m }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem; margin-top: 1rem;">
                <button type="submit" class="btn-filter">🔍 Aplicar Filtros</button>
                <a href="{{ route('grafica') }}" class="btn-export">🔄 Limpiar Filtros</a>
            </div>
        </form>
    </div>

    <!-- Controles de tipo de gráfica -->
    <div class="chart-controls">
        <div style="margin-bottom: 1rem;">
            <strong>Tipo de Gráfica:</strong>
        </div>
        <div class="chart-type-buttons">
            <button class="btn-chart-type active" onclick="changeChartType('doughnut')" data-type="doughnut">
                🍩 Dona
            </button>
            <button class="btn-chart-type" onclick="changeChartType('pie')" data-type="pie">
                🥧 Pastel
            </button>
            <button class="btn-chart-type" onclick="changeChartType('bar')" data-type="bar">
                📊 Barras
            </button>
            <button class="btn-chart-type" onclick="changeChartType('horizontalBar')" data-type="horizontalBar">
                📈 Barras Horizontales
            </button>
            <button class="btn-chart-type" onclick="changeChartType('line')" data-type="line">
                📉 Líneas
            </button>
            <button class="btn-chart-type" onclick="changeChartType('polarArea')" data-type="polarArea">
                🎯 Área Polar
            </button>
        </div>
    </div>

    <!-- Gráfica principal -->
    <div class="chart-card">
        <h2 class="chart-title">
            <span id="chartTitleIcon">🍩</span>
            <span id="chartTitleText">Distribución por Cliente</span>
        </h2>
        <div class="chart-container">
            <canvas id="mainChart"></canvas>
        </div>
        <div class="export-buttons">
            <button class="btn-export" onclick="downloadChart()">💾 Descargar Gráfica</button>
        </div>
    </div>

    <!-- Tabla de datos -->
    <div class="table-card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Unidades</th>
                        <th>Porcentaje</th>
                    </tr>
                </thead>
                <tbody>
                    @php $counter = 1; @endphp
                    @foreach ($unitsByLabel as $label => $units)
                        @php
                            $idx = array_search($label, $labels, true);
                            $pct = $idx !== false ? $percentages[$idx] : 0;
                        @endphp
                        <tr>
                            <td><strong>{{ $counter++ }}</strong></td>
                            <td>{{ $label }}</td>
                            <td>{{ number_format($units, 2) }}</td>
                            <td>{{ number_format($pct, 2) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const labels = @json($labels);
    const data = @json($percentages);
    const units = @json(array_values($unitsByLabel ?? []));

    // Generar colores dinámicos
    const colors = labels.map((_, i) => {
        const hue = (i * 137.5) % 360;
        return `hsl(${hue}, 70%, 60%)`;
    });

    const borderColors = colors.map(c => c.replace('60%', '50%'));

    let currentChart = null;
    let currentType = 'doughnut';

    function createChart(type) {
        const ctx = document.getElementById('mainChart').getContext('2d');
        
        if (currentChart) {
            currentChart.destroy();
        }

        const isHorizontal = type === 'horizontalBar';
        const actualType = isHorizontal ? 'bar' : type;

        const config = {
            type: actualType,
            data: {
                labels: labels,
                datasets: [{
                    label: 'Porcentaje (%)',
                    data: data,
                    backgroundColor: colors,
                    borderColor: borderColors,
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: isHorizontal ? 'y' : 'x',
                plugins: {
                    legend: {
                        position: type === 'doughnut' || type === 'pie' ? 'right' : 'top',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed.y || context.parsed || 0;
                                const unitValue = units[context.dataIndex] || 0;
                                return `${label}: ${value.toFixed(2)}% (${unitValue.toLocaleString()} unidades)`;
                            }
                        }
                    }
                },
                scales: type === 'bar' || type === 'line' || isHorizontal ? {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                } : {}
            }
        };

        currentChart = new Chart(ctx, config);
    }

    function changeChartType(type) {
        currentType = type;
        
        // Actualizar botones activos
        document.querySelectorAll('.btn-chart-type').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-type="${type}"]`).classList.add('active');

        // Actualizar título
        const titles = {
            'doughnut': { icon: '🍩', text: 'Distribución por Cliente (Dona)' },
            'pie': { icon: '🥧', text: 'Distribución por Cliente (Pastel)' },
            'bar': { icon: '📊', text: 'Comparación por Cliente (Barras)' },
            'horizontalBar': { icon: '📈', text: 'Comparación por Cliente (Barras Horizontales)' },
            'line': { icon: '📉', text: 'Tendencia por Cliente (Líneas)' },
            'polarArea': { icon: '🎯', text: 'Distribución por Cliente (Área Polar)' }
        };

        document.getElementById('chartTitleIcon').textContent = titles[type].icon;
        document.getElementById('chartTitleText').textContent = titles[type].text;

        // Crear nueva gráfica
        createChart(type);
    }

    function downloadChart() {
        const link = document.createElement('a');
        link.download = `grafica-${currentType}-${Date.now()}.png`;
        link.href = currentChart.toBase64Image();
        link.click();
    }

    // Inicializar gráfica
    createChart('doughnut');
</script>
@endpush
