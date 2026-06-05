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

    .fullscreen {
        box-shadow: 0 0 50px rgba(0,0,0,0.3) !important;
        border-radius: 0 !important;
    }

    .fullscreen .chart-container {
        height: calc(100vh - 200px) !important;
    }

    .fullscreen .table-wrapper {
        max-height: calc(100vh - 150px) !important;
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

    /* Selector de productos */
    .producto-selector {
        position: relative;
    }

    .producto-search-wrap {
        display: flex;
        align-items: center;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        overflow: hidden;
        transition: border-color 0.3s;
    }

    .producto-search-wrap:focus-within {
        border-color: #667eea;
    }

    .producto-search-input {
        flex: 1;
        padding: 0.75rem 1rem;
        border: none;
        outline: none;
        font-size: 0.95rem;
    }

    .producto-count {
        padding: 0 1rem;
        color: #667eea;
        font-weight: 600;
        font-size: 0.85rem;
        white-space: nowrap;
        background: #f0f2ff;
        height: 100%;
        display: flex;
        align-items: center;
    }

    .producto-dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: white;
        border: 2px solid #667eea;
        border-radius: 8px;
        z-index: 999;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }

    .producto-dropdown.open {
        display: block;
    }

    .producto-dropdown-actions {
        display: flex;
        gap: 0.5rem;
        padding: 0.75rem;
        border-bottom: 1px solid #e9ecef;
    }

    .producto-dropdown-actions button {
        padding: 0.35rem 0.85rem;
        border-radius: 6px;
        border: 1px solid #667eea;
        background: white;
        color: #667eea;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .producto-dropdown-actions button:hover {
        background: #667eea;
        color: white;
    }

    .producto-list {
        max-height: 220px;
        overflow-y: auto;
        padding: 0.5rem 0;
    }

    .producto-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.6rem 1rem;
        cursor: pointer;
        transition: background 0.15s;
        font-size: 0.9rem;
        color: #333;
    }

    .producto-item:hover {
        background: #f0f2ff;
    }

    .producto-item.selected {
        background: #eef0ff;
        color: #667eea;
        font-weight: 500;
    }

    .producto-item input[type=checkbox] {
        accent-color: #667eea;
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    .producto-item.hidden {
        display: none;
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
            <div class="stat-value">{{ number_format($totalUnits, 2) }} {{ $metrica === 'valor_usd' ? 'USD' : '' }}</div>
            <div class="stat-label">Total {{ $metrica === 'valor_usd' ? 'Valor USD' : 'Unidades' }}</div>
        </div>
        @if(isset($avgTasa) && $avgTasa !== null)
        <div class="stat-box">
            <div class="stat-value">Bs. {{ number_format($avgTasa, 2) }}</div>
            <div class="stat-label">Tasa Promedio</div>
        </div>
        @endif
        <div class="stat-box">
            <div class="stat-value">{{ count($labels) }}</div>
            <div class="stat-label">{{ $vista === 'producto' ? 'Productos' : 'Clientes' }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $selectedYear ?? 'Todos' }}</div>
            <div class="stat-label">Año Seleccionado</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ count($selectedMonths) > 0 ? count($selectedMonths) : 'Todos' }}</div>
            <div class="stat-label">Meses Filtrados</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ count($selectedClientes ?? []) > 0 ? count($selectedClientes) : 'Todos' }}</div>
            <div class="stat-label">Clientes Filtrados</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ count($selectedClases ?? []) > 0 ? count($selectedClases) : 'Todas' }}</div>
            <div class="stat-label">Clases Filtradas</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ count($selectedProductos) > 0 ? count($selectedProductos) : 'Todos' }}</div>
            <div class="stat-label">Productos Filtrados</div>
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
                                {{ $m }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Clientes</label>

                    <!-- Inputs hidden para enviar los seleccionados -->
                    <div id="clienteHiddenInputs">
                        @foreach($selectedClientes ?? [] as $sc)
                            <input type="hidden" name="cliente[]" value="{{ $sc }}">
                        @endforeach
                    </div>

                    <!-- Selector con búsqueda -->
                    <div class="producto-selector">
                        <div class="producto-search-wrap">
                            <input type="text" id="clienteSearch" placeholder="🔍 Buscar cliente..." class="producto-search-input" autocomplete="off">
                            <span id="clienteCount" class="producto-count">
                                {{ count($selectedClientes ?? []) > 0 ? count($selectedClientes) . ' seleccionado(s)' : 'Todos' }}
                            </span>
                        </div>
                        <div class="producto-dropdown" id="clienteDropdown">
                            <div class="producto-dropdown-actions">
                                <button type="button" onclick="selectAllClientes()">Seleccionar todos</button>
                                <button type="button" onclick="clearClientes()">Limpiar</button>
                            </div>
                            <div class="producto-list" id="clienteList">
                                @foreach ($availableClientes ?? [] as $c)
                                    <label class="producto-item {{ in_array($c, $selectedClientes ?? []) ? 'selected' : '' }}" data-value="{{ $c }}">
                                        <input type="checkbox" value="{{ $c }}" {{ in_array($c, $selectedClientes ?? []) ? 'checked' : '' }} onchange="toggleCliente(this)">
                                        <span>{{ $c }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Clase Terapéutica</label>

                    <!-- Inputs hidden para enviar los seleccionados -->
                    <div id="claseHiddenInputs">
                        @foreach($selectedClases ?? [] as $sc)
                            <input type="hidden" name="clase[]" value="{{ $sc }}">
                        @endforeach
                    </div>

                    <!-- Selector con búsqueda -->
                    <div class="producto-selector">
                        <div class="producto-search-wrap">
                            <input type="text" id="claseSearch" placeholder="🔍 Buscar clase terapéutica..." class="producto-search-input" autocomplete="off">
                            <span id="claseCount" class="producto-count">
                                {{ count($selectedClases ?? []) > 0 ? count($selectedClases) . ' seleccionado(s)' : 'Todas' }}
                            </span>
                        </div>
                        <div class="producto-dropdown" id="claseDropdown">
                            <div class="producto-dropdown-actions">
                                <button type="button" onclick="selectAllClases()">Seleccionar todos</button>
                                <button type="button" onclick="clearClases()">Limpiar</button>
                            </div>
                            <div class="producto-list" id="claseList">
                                @foreach ($availableClases ?? [] as $c)
                                    <label class="producto-item {{ in_array($c, $selectedClases ?? []) ? 'selected' : '' }}" data-value="{{ $c }}">
                                        <input type="checkbox" value="{{ $c }}" {{ in_array($c, $selectedClases ?? []) ? 'checked' : '' }} onchange="toggleClase(this)">
                                        <span>{{ $c }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label class="form-label">Productos</label>

                    <!-- Inputs hidden para enviar los seleccionados -->
                    <div id="productoHiddenInputs">
                        @foreach($selectedProductos as $sp)
                            <input type="hidden" name="producto[]" value="{{ $sp }}">
                        @endforeach
                    </div>

                    <!-- Selector con búsqueda -->
                    <div class="producto-selector">
                        <div class="producto-search-wrap">
                            <input type="text" id="productoSearch" placeholder="🔍 Buscar producto..." class="producto-search-input" autocomplete="off">
                            <span id="productoCount" class="producto-count">
                                {{ count($selectedProductos) > 0 ? count($selectedProductos) . ' seleccionado(s)' : 'Todos' }}
                            </span>
                        </div>
                        <div class="producto-dropdown" id="productoDropdown">
                            <div class="producto-dropdown-actions">
                                <button type="button" onclick="selectAllProductos()">Seleccionar todos</button>
                                <button type="button" onclick="clearProductos()">Limpiar</button>
                            </div>
                            <div class="producto-list" id="productoList">
                                @foreach ($availableProductos as $p)
                                    <label class="producto-item {{ in_array($p, $selectedProductos) ? 'selected' : '' }}" data-value="{{ $p }}">
                                        <input type="checkbox" value="{{ $p }}" {{ in_array($p, $selectedProductos) ? 'checked' : '' }} onchange="toggleProducto(this)">
                                        <span>{{ $p }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
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
        <div style="display: flex; flex-wrap: wrap; gap: 2rem; margin-bottom: 1.5rem;">
            <div>
                <div style="margin-bottom: 0.5rem;">
                    <strong>Métrica:</strong>
                </div>
                <div class="chart-type-buttons">
                    <button class="btn-chart-type {{ $metrica === 'unidades' ? 'active' : '' }}" onclick="changeMetrica('unidades')">
                        📦 Unidades
                    </button>
                    <button class="btn-chart-type {{ $metrica === 'valor_usd' ? 'active' : '' }}" onclick="changeMetrica('valor_usd')">
                        💵 Valor USD
                    </button>
                </div>
            </div>
            
            <div>
                <div style="margin-bottom: 0.5rem;">
                    <strong>Vista de Datos:</strong>
                </div>
        <div class="chart-type-buttons" style="margin-bottom: 1.5rem;">
            <button class="btn-chart-type {{ $vista === 'cliente' ? 'active' : '' }}" onclick="changeView('cliente')" data-vista="cliente">
                👥 Por Cliente
            </button>
            <button class="btn-chart-type {{ $vista === 'producto' ? 'active' : '' }}" onclick="changeView('producto')" data-vista="producto">
                📦 Por Producto
            </button>
        </div>

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
            <span id="chartTitleText">{{ $vista === 'producto' ? 'Distribución por Producto' : 'Distribución por Cliente' }}</span>
        </h2>
        <div class="chart-container">
            <canvas id="mainChart"></canvas>
        </div>
        <div class="export-buttons">
            <button class="btn-export" onclick="downloadChart()">💾 Descargar Gráfica</button>
            <button class="btn-export" onclick="exportToExcel()">📊 Exportar Datos</button>
            <button class="btn-export" onclick="toggleFullscreen()">🖥️ Pantalla Completa</button>
        </div>
    </div>

    <!-- Tabla de datos -->
    <div class="table-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; color: #2d3748;">📋 Datos Detallados</h3>
            <button class="btn-export" onclick="toggleTableFullscreen()">🖥️ Pantalla Completa</button>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ $vista === 'producto' ? 'Producto' : 'Cliente' }}</th>
                        <th>{{ $metrica === 'valor_usd' ? 'Valor USD' : 'Unidades' }}</th>
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
        // Obtener canvas
        const canvas = document.getElementById('mainChart');
        
        if (canvas) {
            canvas.style.display = 'block';
        }
        
        // Esperar un frame para que el canvas sea visible antes de obtener contexto
        setTimeout(() => {
            const ctx = canvas ? canvas.getContext('2d') : null;
            
            if (currentChart && currentChart !== null) {
                currentChart.destroy();
            }

            // Generar colores dinámicos
            const chartColors = labels.map((_, i) => {
                const hue = (i * 137.5) % 360;
                return `hsl(${hue}, 70%, 60%)`;
            });

            const chartBorderColors = chartColors.map(c => c.replace('60%', '50%'));

            const isHorizontal = type === 'horizontalBar';
            const actualType = isHorizontal ? 'bar' : type;

            const config = {
                type: actualType,
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Porcentaje (%)',
                        data: data,
                        backgroundColor: chartColors,
                        borderColor: chartBorderColors,
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
                                    const isUsd = '{{ $metrica }}' === 'valor_usd';
                                    const formattedUnit = isUsd ? '$' + unitValue.toLocaleString(undefined, {minimumFractionDigits: 2}) : unitValue.toLocaleString();
                                    const unitLabel = isUsd ? 'USD' : 'unidades';
                                    return `${label}: ${value.toFixed(2)}% (${formattedUnit} ${unitLabel})`;
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
        }, 100);
    }

    function changeView(vista) {
        // Obtener parámetros actuales de la URL
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('vista', vista);
        
        // Recargar la página con la nueva vista
        window.location.href = window.location.pathname + '?' + urlParams.toString();
    }

    function changeMetrica(metrica) {
        // Obtener parámetros actuales de la URL
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('metrica', metrica);
        
        // Recargar la página con la nueva métrica
        window.location.href = window.location.pathname + '?' + urlParams.toString();
    }

    function changeChartType(type) {
        currentType = type;
        
        // Actualizar botones activos
        document.querySelectorAll('.btn-chart-type').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-type="${type}"]`).classList.add('active');

        // Determinar si es vista por cliente o producto
        const currentVista = '{{ $vista }}';
        const vistaText = currentVista === 'producto' ? 'Producto' : 'Cliente';
        
        // Actualizar título
        const titles = {
            'doughnut': { icon: '🍩', text: `Distribución por ${vistaText} (Dona)` },
            'pie': { icon: '🥧', text: `Distribución por ${vistaText} (Pastel)` },
            'bar': { icon: '📊', text: `Comparación por ${vistaText} (Barras)` },
            'horizontalBar': { icon: '📈', text: `Comparación por ${vistaText} (Barras Horizontales)` },
            'line': { icon: '📉', text: `Tendencia por ${vistaText} (Líneas)` },
            'polarArea': { icon: '🎯', text: `Distribución por ${vistaText} (Área Polar)` }
        };

        document.getElementById('chartTitleIcon').textContent = titles[type].icon;
        document.getElementById('chartTitleText').textContent = titles[type].text;

        // Crear nueva gráfica
        createChart(type);
    }

    
    function downloadChart() {
        if (currentChart) {
            // Descargar con Chart.js
            const link = document.createElement('a');
            link.download = `grafica-${currentType}-${Date.now()}.png`;
            link.href = currentChart.toBase64Image();
            link.click();
        }
    }

    function toggleFullscreen() {
        const chartCard = document.querySelector('.chart-card');
        const isFullscreen = chartCard.classList.contains('fullscreen');
        
        if (!isFullscreen) {
            chartCard.classList.add('fullscreen');
            chartCard.style.position = 'fixed';
            chartCard.style.top = '0';
            chartCard.style.left = '0';
            chartCard.style.width = '100vw';
            chartCard.style.height = '100vh';
            chartCard.style.zIndex = '9999';
            chartCard.style.backgroundColor = 'white';
            chartCard.style.padding = '2rem';
            chartCard.style.overflow = 'auto';
            
            // Ajustar tamaño del contenedor de gráfica
            const chartContainer = chartCard.querySelector('.chart-container');
            chartContainer.style.height = 'calc(100vh - 200px)';
            
            // Recrear gráfica actual para ajustar al nuevo tamaño
            setTimeout(() => {
                createChart(currentType);
            }, 100);
            
            // Agregar botones de navegación en pantalla completa
            const navButtons = document.createElement('div');
            navButtons.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 10000; display: flex; gap: 10px;';
            navButtons.innerHTML = `
                <button onclick="previousChart()" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
                    ⬅️ Anterior
                </button>
                <button onclick="nextChart()" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
                    Siguiente ➡️
                </button>
                <button onclick="exitFullscreen()" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
                    🔽 Salir
                </button>
            `;
            chartCard.appendChild(navButtons);
            
            // Cambiar texto del botón original
            event.target.textContent = '🔽 Salir Pantalla Completa';
        } else {
            // Restaurar estado normal
            chartCard.classList.remove('fullscreen');
            chartCard.style.cssText = '';
            const chartContainer = chartCard.querySelector('.chart-container');
            chartContainer.style.cssText = '';
            
            // Eliminar botones de navegación
            const navButtons = chartCard.querySelector('div[style*="position: fixed"]');
            if (navButtons) navButtons.remove();
            
            // Recrear gráfica para ajustar al tamaño original
            setTimeout(() => {
                createChart(currentType);
            }, 100);
            
            // Cambiar texto del botón
            event.target.textContent = '🖥️ Pantalla Completa';
        }
    }

    function toggleTableFullscreen() {
        const tableCard = document.querySelector('.table-card');
        const isFullscreen = tableCard.classList.contains('fullscreen');
        
        if (!isFullscreen) {
            tableCard.classList.add('fullscreen');
            tableCard.style.position = 'fixed';
            tableCard.style.top = '0';
            tableCard.style.left = '0';
            tableCard.style.width = '100vw';
            tableCard.style.height = '100vh';
            tableCard.style.zIndex = '9999';
            tableCard.style.backgroundColor = 'white';
            tableCard.style.padding = '2rem';
            tableCard.style.overflow = 'auto';
            
            // Cambiar texto del botón
            event.target.textContent = '🔽 Salir Pantalla Completa';
        } else {
            // Restaurar estado normal
            tableCard.classList.remove('fullscreen');
            tableCard.style.cssText = '';
            
            // Cambiar texto del botón
            event.target.textContent = '🖥️ Pantalla Completa';
        }
    }

    function nextChart() {
        const chartTypes = ['doughnut', 'pie', 'bar', 'horizontalBar', 'line', 'polarArea'];
        const currentIndex = chartTypes.indexOf(currentType);
        const nextIndex = (currentIndex + 1) % chartTypes.length;
        const nextType = chartTypes[nextIndex];
        
        // Actualizar botón activo
        document.querySelectorAll('.btn-chart-type').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-type="${nextType}"]`).classList.add('active');
        
        // Cambiar gráfica
        currentType = nextType;
        if (nextType === '3d') {
            create3DChart();
        } else {
            createChart(nextType);
        }
        
        // Actualizar título
        const currentVista = '{{ $vista }}';
        const vistaText = currentVista === 'producto' ? 'Producto' : 'Cliente';
        const titles = {
            'doughnut': { icon: '🍩', text: `Distribución por ${vistaText} (Dona)` },
            'pie': { icon: '🥧', text: `Distribución por ${vistaText} (Pastel)` },
            'bar': { icon: '📊', text: `Comparación por ${vistaText} (Barras)` },
            'horizontalBar': { icon: '📈', text: `Comparación por ${vistaText} (Barras Horizontales)` },
            'line': { icon: '📉', text: `Tendencia por ${vistaText} (Líneas)` },
            'polarArea': { icon: '🎯', text: `Distribución por ${vistaText} (Área Polar)` }
        };
        
        document.getElementById('chartTitleIcon').textContent = titles[nextType].icon;
        document.getElementById('chartTitleText').textContent = titles[nextType].text;
    }

    function previousChart() {
        const chartTypes = ['doughnut', 'pie', 'bar', 'horizontalBar', 'line', 'polarArea'];
        const currentIndex = chartTypes.indexOf(currentType);
        const prevIndex = currentIndex === 0 ? chartTypes.length - 1 : currentIndex - 1;
        const prevType = chartTypes[prevIndex];
        
        // Actualizar botón activo
        document.querySelectorAll('.btn-chart-type').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-type="${prevType}"]`).classList.add('active');
        
        // Cambiar gráfica
        currentType = prevType;
        if (prevType === '3d') {
            create3DChart();
        } else {
            createChart(prevType);
        }
        
        // Actualizar título
        const currentVista = '{{ $vista }}';
        const vistaText = currentVista === 'producto' ? 'Producto' : 'Cliente';
        const titles = {
            'doughnut': { icon: '🍩', text: `Distribución por ${vistaText} (Dona)` },
            'pie': { icon: '🥧', text: `Distribución por ${vistaText} (Pastel)` },
            'bar': { icon: '📊', text: `Comparación por ${vistaText} (Barras)` },
            'horizontalBar': { icon: '📈', text: `Comparación por ${vistaText} (Barras Horizontales)` },
            'line': { icon: '📉', text: `Tendencia por ${vistaText} (Líneas)` },
            'polarArea': { icon: '🎯', text: `Distribución por ${vistaText} (Área Polar)` }
        };
        
        document.getElementById('chartTitleIcon').textContent = titles[prevType].icon;
        document.getElementById('chartTitleText').textContent = titles[prevType].text;
    }

    function exitFullscreen() {
        const chartCard = document.querySelector('.chart-card');
        if (chartCard && chartCard.classList.contains('fullscreen')) {
            // Restaurar estado normal
            chartCard.classList.remove('fullscreen');
            chartCard.style.cssText = '';
            const chartContainer = chartCard.querySelector('.chart-container');
            chartContainer.style.cssText = '';
            
            // Eliminar botones de navegación
            const navButtons = chartCard.querySelector('div[style*="position: fixed"]');
            if (navButtons) navButtons.remove();
            
            // Cambiar texto del botón original - buscar más específicamente
            const allButtons = document.querySelectorAll('button');
            allButtons.forEach(btn => {
                if (btn.textContent.includes('Salir Pantalla Completa')) {
                    btn.textContent = '🖥️ Pantalla Completa';
                }
            });
            
            // Recrear gráfica para ajustar al tamaño original
            setTimeout(() => {
                createChart(currentType);
            }, 100);
        }
    }

    function exportToExcel() {
        // Obtener los datos de la tabla
        const table = document.querySelector('table');
        const rows = table.querySelectorAll('tr');
        
        let csv = [];
        
        // Encabezados
        const headers = [];
        rows[0].querySelectorAll('th').forEach(th => {
            headers.push(th.textContent.trim());
        });
        csv.push(headers.join(','));
        
        // Datos
        for (let i = 1; i < rows.length; i++) {
            const rowData = [];
            rows[i].querySelectorAll('td').forEach(td => {
                rowData.push(td.textContent.trim());
            });
            csv.push(rowData.join(','));
        }
        
        // Crear blob y descargar
        const csvContent = csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        
        const vistaText = '{{ $vista === "producto" ? "productos" : "clientes" }}';
        const timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
        
        link.href = URL.createObjectURL(blob);
        link.download = `export-${vistaText}-${timestamp}.csv`;
        link.click();
        
        // Liberar URL
        setTimeout(() => URL.revokeObjectURL(link.href), 100);
    }

    // Agregar soporte para teclas de navegación en pantalla completa
    document.addEventListener('keydown', function(e) {
        const chartCard = document.querySelector('.chart-card');
        const isFullscreen = chartCard && chartCard.classList.contains('fullscreen');
        
        if (isFullscreen) {
            switch(e.key) {
                case 'ArrowLeft':
                    e.preventDefault();
                    previousChart();
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    nextChart();
                    break;
                case 'Escape':
                    e.preventDefault();
                    exitFullscreen();
                    break;
            }
        }
    });

    // Inicializar gráfica
    createChart('doughnut');

    // Selector de productos
    const searchInput = document.getElementById('productoSearch');
    const dropdown = document.getElementById('productoDropdown');

    searchInput.addEventListener('focus', () => dropdown.classList.add('open'));

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.producto-selector')) {
            dropdown.classList.remove('open');
        }
    });

    searchInput.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.producto-item').forEach(item => {
            const text = item.querySelector('span').textContent.toLowerCase();
            item.classList.toggle('hidden', !text.includes(q));
        });
    });

    function toggleProducto(checkbox) {
        const item = checkbox.closest('.producto-item');
        item.classList.toggle('selected', checkbox.checked);
        syncHiddenInputs();
    }

    function syncHiddenInputs() {
        const container = document.getElementById('productoHiddenInputs');
        container.innerHTML = '';
        document.querySelectorAll('.producto-item input:checked').forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'producto[]';
            input.value = cb.value;
            container.appendChild(input);
        });
        const count = container.querySelectorAll('input').length;
        document.getElementById('productoCount').textContent = count > 0 ? count + ' seleccionado(s)' : 'Todos';
    }

    function selectAllProductos() {
        document.querySelectorAll('.producto-item:not(.hidden) input').forEach(cb => {
            cb.checked = true;
            cb.closest('.producto-item').classList.add('selected');
        });
        syncHiddenInputs();
    }

    function clearProductos() {
        document.querySelectorAll('.producto-item input').forEach(cb => {
            cb.checked = false;
            cb.closest('.producto-item').classList.remove('selected');
        });
        syncHiddenInputs();
    }

    // Funciones para clientes
    const clienteSearchInput = document.getElementById('clienteSearch');
    const clienteDropdown = document.getElementById('clienteDropdown');

    clienteSearchInput.addEventListener('focus', () => clienteDropdown.classList.add('open'));

    clienteSearchInput.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#clienteList .producto-item').forEach(item => {
            const text = item.querySelector('span').textContent.toLowerCase();
            item.classList.toggle('hidden', !text.includes(q));
        });
    });

    function toggleCliente(checkbox) {
        const item = checkbox.closest('.producto-item');
        item.classList.toggle('selected', checkbox.checked);
        syncHiddenClientes();
    }

    function syncHiddenClientes() {
        const container = document.getElementById('clienteHiddenInputs');
        container.innerHTML = '';
        document.querySelectorAll('#clienteList .producto-item input:checked').forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'cliente[]';
            input.value = cb.value;
            container.appendChild(input);
        });
        const count = container.querySelectorAll('input').length;
        document.getElementById('clienteCount').textContent = count > 0 ? count + ' seleccionado(s)' : 'Todos';
    }

    function selectAllClientes() {
        document.querySelectorAll('#clienteList .producto-item:not(.hidden) input').forEach(cb => {
            cb.checked = true;
            cb.closest('.producto-item').classList.add('selected');
        });
        syncHiddenClientes();
    }

    function clearClientes() {
        document.querySelectorAll('#clienteList .producto-item input').forEach(cb => {
            cb.checked = false;
            cb.closest('.producto-item').classList.remove('selected');
        });
        syncHiddenClientes();
    }

    // Funciones para clases terapéuticas
    const claseSearchInput = document.getElementById('claseSearch');
    const claseDropdown = document.getElementById('claseDropdown');

    claseSearchInput.addEventListener('focus', () => claseDropdown.classList.add('open'));

    claseSearchInput.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#claseList .producto-item').forEach(item => {
            const text = item.querySelector('span').textContent.toLowerCase();
            item.classList.toggle('hidden', !text.includes(q));
        });
    });

    function toggleClase(checkbox) {
        const item = checkbox.closest('.producto-item');
        item.classList.toggle('selected', checkbox.checked);
        syncHiddenClases();
    }

    function syncHiddenClases() {
        const container = document.getElementById('claseHiddenInputs');
        container.innerHTML = '';
        document.querySelectorAll('#claseList .producto-item input:checked').forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'clase[]';
            input.value = cb.value;
            container.appendChild(input);
        });
        const count = container.querySelectorAll('input').length;
        document.getElementById('claseCount').textContent = count > 0 ? count + ' seleccionado(s)' : 'Todas';
    }

    function selectAllClases() {
        document.querySelectorAll('#claseList .producto-item:not(.hidden) input').forEach(cb => {
            cb.checked = true;
            cb.closest('.producto-item').classList.add('selected');
        });
        syncHiddenClases();
    }

    function clearClases() {
        document.querySelectorAll('#claseList .producto-item input').forEach(cb => {
            cb.checked = false;
            cb.closest('.producto-item').classList.remove('selected');
        });
        syncHiddenClases();
    }
</script>
@endpush
