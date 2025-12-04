<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .auth-card {
                background: white;
                border-radius: 12px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
                padding: 2.5rem;
                max-width: 450px;
                width: 100%;
            }
            .auth-logo {
                text-align: center;
                margin-bottom: 2rem;
            }
            .auth-logo a {
                text-decoration: none;
                color: #667eea;
                font-size: 1.75rem;
                font-weight: 600;
            }
            .form-label {
                font-weight: 500;
                color: #374151;
                margin-bottom: 0.5rem;
            }
            .form-control {
                border: 1px solid #d1d5db;
                border-radius: 8px;
                padding: 0.75rem;
                transition: all 0.2s;
            }
            .form-control:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            }
            .btn-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                border-radius: 8px;
                padding: 0.75rem 2rem;
                font-weight: 500;
                transition: transform 0.2s, box-shadow 0.2s;
            }
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            }
            .auth-links {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 1.5rem;
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            .auth-links a {
                color: #667eea;
                text-decoration: none;
                font-size: 0.875rem;
                transition: color 0.2s;
            }
            .auth-links a:hover {
                color: #764ba2;
                text-decoration: underline;
            }
            .invalid-feedback {
                display: block;
                margin-top: 0.25rem;
            }
        </style>
    </head>
    <body>
        <div class="auth-card">
            <div class="auth-logo">
                <a href="{{ route('home') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
            </div>

            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (isset($slot))
                {{ $slot }}
            @endif
        </div>

        <!-- Bootstrap JS Bundle -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>
</html>
