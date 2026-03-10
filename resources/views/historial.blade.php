@extends('layouts.admin')

@section('title', 'Historial de Importaciones')

@section('content')
<div class="page-header">
    <h1 class="page-title">Historial de Importaciones</h1>
    <p class="page-description">Consulta y gestiona todas las importaciones de archivos Excel realizadas.</p>
</div>

<style>
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
        padding: 1.25rem 1.5rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    td {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f3f5;
        color: #495057;
    }

    tbody tr {
        transition: all 0.2s;
    }

    tbody tr:hover {
        background: #f8f9fa;
    }

    .badge {
        display: inline-block;
        padding: 0.35rem 0.85rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge-active {
        background: #d4edda;
        color: #155724;
    }

    .btn-select {
        padding: 0.5rem 1.25rem;
        background: #28a745;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 0.9rem;
    }

    .btn-select:hover {
        background: #218838;
        transform: translateY(-2px);
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
    }

    .empty-title {
        font-size: 1.5rem;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .empty-text {
        color: #6c757d;
        margin-bottom: 2rem;
    }

    .btn-primary {
        display: inline-block;
        padding: 0.875rem 2rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-decoration: none;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        padding: 2rem;
        list-style: none;
    }

    .pagination a,
    .pagination span {
        padding: 0.5rem 1rem;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        text-decoration: none;
        color: #667eea;
        transition: all 0.3s;
    }

    .pagination a:hover {
        background: #f8f9fa;
    }

    .pagination .active span {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: #667eea;
    }

    .pagination .disabled span {
        color: #adb5bd;
        cursor: not-allowed;
    }
</style>

@if($importaciones->count() > 0)
    <div class="table-card">
        <div class="table-wrapper">
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
                            <td><strong>#{{ $importacion->id }}</strong></td>
                            <td>{{ $importacion->archivo_nombre }}</td>
                            <td>{{ $importacion->fecha_importacion->format('d/m/Y H:i') }}</td>
                            <td>{{ number_format($importacion->registros_count) }}</td>
                            <td>
                                @if($importacion->id == $importacionActualId)
                                    <span class="badge badge-active">✓ Activa</span>
                                @else
                                    <span>—</span>
                                @endif
                            </td>
                            <td>
                                @if($importacion->id != $importacionActualId)
                                    <form method="POST" action="{{ route('historial.seleccionar', $importacion->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-select">Seleccionar</button>
                                    </form>
                                @else
                                    <span style="color: #28a745; font-weight: 600;">En uso</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($importaciones->hasPages())
            <div class="pagination">
                {{ $importaciones->links() }}
            </div>
        @endif
    </div>
@else
    <div class="table-card">
        <div class="empty-state">
            <div class="empty-icon">📭</div>
            <h2 class="empty-title">No hay importaciones</h2>
            <p class="empty-text">Aún no has importado ningún archivo Excel. Comienza importando tu primer archivo.</p>
            <a href="{{ route('import.form') }}" class="btn-primary">📤 Importar Primer Archivo</a>
        </div>
    </div>
@endif
@endsection
