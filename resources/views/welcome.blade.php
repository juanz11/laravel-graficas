<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                    font-family: 'Poppins', sans-serif;
                }

                body {
                    background: radial-gradient(1200px 800px at 20% 20%, #2e6be6 0%, #0b2a5a 40%, #06162f 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    position: relative;
                    padding: 24px;
                }

                body::before {
                    content: '';
                    position: absolute;
                    inset: 0;
                    background: rgba(0, 0, 0, 0.45);
                    z-index: 1;
                }

                .container {
                    position: relative;
                    z-index: 2;
                    background: rgba(255, 255, 255, 0.95);
                    padding: 2rem;
                    border-radius: 15px;
                    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18);
                    width: 90%;
                    max-width: 1000px;
                    text-align: center;
                    backdrop-filter: blur(10px);
                }

                .logo {
                    max-width: 200px;
                    width: 100%;
                    height: auto;
                    margin: 0 auto 1.75rem;
                    display: block;
                }

                h1 {
                    color: #0b2a5a;
                    margin-bottom: 1rem;
                    font-size: 2rem;
                    font-weight: 600;
                }

                .description {
                    color: #24405f;
                    margin-bottom: 2rem;
                    line-height: 1.6;
                    font-size: 1.05rem;
                }

                .buttons {
                    display: flex;
                    gap: 1rem;
                    justify-content: center;
                    margin-top: 1.5rem;
                    flex-wrap: wrap;
                }

                .btn {
                    padding: 0.85rem 2rem;
                    border-radius: 10px;
                    font-weight: 500;
                    text-decoration: none;
                    transition: all 0.25s ease;
                    font-size: 1rem;
                    display: inline-block;
                }

                .btn-primary {
                    background: #3498db;
                    color: white;
                    border: 2px solid #3498db;
                }

                .btn-primary:hover {
                    background: #2980b9;
                    border-color: #2980b9;
                    transform: translateY(-2px);
                }

                .btn-outline {
                    border: 2px solid #3498db;
                    color: #3498db;
                    background: transparent;
                }

                .btn-outline:hover {
                    background: #3498db;
                    color: white;
                    transform: translateY(-2px);
                }

                @media (max-width: 768px) {
                    .container {
                        padding: 1.5rem;
                    }

                    .buttons {
                        flex-direction: column;
                    }

                    .btn {
                        width: 100%;
                    }
                }
            </style>
        @endif
    </head>
    <body>
        <div class="container">
            {{-- Laravel Logo --}}
            <img src="{{ asset('logo.png') }}" alt="Logo" class="logo">

            <h1>Sistema de Datos, Análisis y Estadísticas de Ventas</h1>

            <p class="description">
                Importa tu archivo y visualiza las ventas con gráficas.
            </p>

            <div class="buttons">
                <a href="{{ route('import.form') }}" class="btn btn-primary">Exportar Datos / Visualizar Ventas</a>
                <a href="{{ route('grafica') }}" class="btn btn-outline">Ir a Gráficas</a>
            </div>
        </div>
    </body>
</html>
