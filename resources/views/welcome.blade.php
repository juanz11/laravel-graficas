@extends('layouts.admin')

@section('title', 'Inicio - Sistema de Análisis')

@section('content')
<div class="page-header">
    <h1 class="page-title">Bienvenido al Sistema de Análisis de Ventas</h1>
    <p class="page-description">Gestiona tus datos, visualiza estadísticas y analiza el rendimiento de ventas.</p>
</div>

<style>
    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .card {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .card-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .card-description {
        color: #6c757d;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .card-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .card-primary .card-title,
    .card-primary .card-description {
        color: white;
    }

    .stats-section {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        margin-top: 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .stats-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 1.5rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .stat-item {
        text-align: center;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 10px;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
    }
</style>

<div class="dashboard-cards">
    <a href="{{ route('import.form') }}" class="card card-primary">
        <div class="card-icon">📤</div>
        <h3 class="card-title">Importar Datos</h3>
        <p class="card-description">Sube tu archivo Excel para procesar y analizar los datos de ventas.</p>
    </a>

    <a href="{{ route('grafica') }}" class="card">
        <div class="card-icon">📊</div>
        <h3 class="card-title">Ver Gráficas</h3>
        <p class="card-description">Visualiza estadísticas y gráficas interactivas de tus datos.</p>
    </a>

    <a href="{{ route('historial') }}" class="card">
        <div class="card-icon">📋</div>
        <h3 class="card-title">Historial</h3>
        <p class="card-description">Consulta todas las importaciones anteriores y sus registros.</p>
    </a>
</div>

<div class="stats-section">
    <h2 class="stats-title">Estadísticas Rápidas</h2>
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-value">{{ \App\Models\Importacion::count() }}</div>
            <div class="stat-label">Importaciones Totales</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ number_format(\App\Models\RegistroExcel::count()) }}</div>
            <div class="stat-label">Registros Procesados</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">
                @php
                    $ultima = \App\Models\Importacion::latest('fecha_importacion')->first();
                @endphp
                {{ $ultima ? $ultima->fecha_importacion->diffForHumans() : 'N/A' }}
            </div>
            <div class="stat-label">Última Importación</div>
        </div>
    </div>
</div>
@endsection
