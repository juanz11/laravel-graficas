<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Acceso</title>

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
                max-width: 520px;
                text-align: center;
                backdrop-filter: blur(10px);
            }

            .logo {
                max-width: 180px;
                width: 100%;
                height: auto;
                margin: 0 auto 1.5rem;
                display: block;
            }

            h1 {
                color: #0b2a5a;
                margin-bottom: 0.5rem;
                font-size: 1.75rem;
                font-weight: 600;
            }

            .description {
                color: #24405f;
                margin-bottom: 1.75rem;
                line-height: 1.6;
                font-size: 1rem;
            }

            .field {
                text-align: left;
                margin-bottom: 1rem;
            }

            label {
                display: block;
                margin-bottom: 0.35rem;
                color: #0b2a5a;
                font-size: 0.95rem;
                font-weight: 500;
            }

            input {
                width: 100%;
                padding: 0.85rem 1rem;
                border-radius: 10px;
                border: 1px solid rgba(11, 42, 90, 0.22);
                outline: none;
                font-size: 1rem;
            }

            input:focus {
                border-color: #3498db;
                box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.18);
            }

            .error {
                margin-top: 0.5rem;
                color: #b91c1c;
                font-size: 0.9rem;
            }

            .btn {
                width: 100%;
                padding: 0.9rem 1.5rem;
                border-radius: 10px;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.25s ease;
                font-size: 1rem;
                display: inline-block;
                border: 2px solid #3498db;
                background: #3498db;
                color: #fff;
                cursor: pointer;
                margin-top: 0.25rem;
            }

            .btn:hover {
                background: #2980b9;
                border-color: #2980b9;
                transform: translateY(-2px);
            }
        </style>
    </head>
    <body>
        <div class="container">
            <img src="{{ asset('logo.png') }}" alt="Logo" class="logo">

            <h1>Iniciar sesión</h1>
            <p class="description">Ingresa tus credenciales para continuar.</p>

            <form method="POST" action="{{ route('simple.login.post') }}">
                @csrf

                <div class="field">
                    <label for="username">Usuario</label>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" autocomplete="username" required>
                    @error('username')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Clave</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required>
                    @error('password')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn">Entrar</button>
            </form>
        </div>
    </body>
</html>
