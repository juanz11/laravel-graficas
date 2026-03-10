@extends('layouts.admin')

@section('title', 'Importar Excel')

@section('content')
<div class="page-header">
    <h1 class="page-title">Importar Archivo Excel</h1>
    <p class="page-description">Sube tu archivo Excel con los datos de ventas para procesarlos y visualizarlos.</p>
</div>

<style>
    .import-card {
        background: white;
        padding: 2.5rem;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        max-width: 700px;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .form-input {
        width: 100%;
        padding: 0.875rem;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s;
    }

    .form-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .file-input-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
        width: 100%;
    }

    .file-input-wrapper input[type=file] {
        position: absolute;
        left: -9999px;
    }

    .file-input-label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 2rem;
        border: 2px dashed #667eea;
        border-radius: 10px;
        background: #f8f9ff;
        cursor: pointer;
        transition: all 0.3s;
        text-align: center;
    }

    .file-input-label:hover {
        background: #eef1ff;
        border-color: #5568d3;
    }

    .file-icon {
        font-size: 2.5rem;
    }

    .file-text {
        color: #667eea;
        font-weight: 600;
    }

    .file-hint {
        color: #6c757d;
        font-size: 0.85rem;
        margin-top: 0.5rem;
    }

    .selected-file {
        margin-top: 1rem;
        padding: 0.75rem;
        background: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 8px;
        color: #155724;
        display: none;
    }

    .btn-submit {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .error-list {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
    }

    .error-list ul {
        margin: 0;
        padding-left: 1.5rem;
    }

    .info-box {
        background: #e7f3ff;
        border: 1px solid #b3d9ff;
        padding: 1.5rem;
        border-radius: 10px;
        margin-top: 2rem;
    }

    .info-title {
        font-weight: 600;
        color: #004085;
        margin-bottom: 0.75rem;
    }

    .info-list {
        color: #004085;
        margin: 0;
        padding-left: 1.5rem;
        line-height: 1.8;
    }
</style>

@if ($errors->any())
    <div class="error-list">
        <strong>Error al importar:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="import-card">
    <form method="POST" action="{{ route('import') }}" enctype="multipart/form-data" id="importForm">
        @csrf

        <div class="form-group">
            <label class="form-label" for="excel">Selecciona tu archivo Excel</label>
            <div class="file-input-wrapper">
                <input id="excel" name="excel" type="file" accept=".xls,.xlsx,.xlsm" required onchange="updateFileName(this)">
                <label for="excel" class="file-input-label">
                    <div>
                        <div class="file-icon">📁</div>
                        <div class="file-text">Haz clic para seleccionar archivo</div>
                        <div class="file-hint">Formatos aceptados: .xls, .xlsx, .xlsm</div>
                    </div>
                </label>
            </div>
            <div id="selectedFile" class="selected-file"></div>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">
            <span>📤 Importar Archivo</span>
        </button>
    </form>

    <div class="info-box">
        <div class="info-title">📌 Información importante:</div>
        <ul class="info-list">
            <li>El archivo debe contener una hoja llamada "Base de datos"</li>
            <li>Los datos se guardarán en el historial automáticamente</li>
            <li>Puedes consultar importaciones anteriores en el Historial</li>
            <li>Las gráficas mostrarán siempre la última importación</li>
        </ul>
    </div>
</div>

<script>
    function updateFileName(input) {
        const selectedFile = document.getElementById('selectedFile');
        const submitBtn = document.getElementById('submitBtn');
        
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
            selectedFile.innerHTML = `✓ Archivo seleccionado: <strong>${fileName}</strong> (${fileSize} MB)`;
            selectedFile.style.display = 'block';
            submitBtn.disabled = false;
        } else {
            selectedFile.style.display = 'none';
            submitBtn.disabled = true;
        }
    }

    document.getElementById('importForm').addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span>⏳ Procesando...</span>';
    });
</script>
@endsection
