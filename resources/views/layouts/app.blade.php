{{-- resources/views/layouts/app.blade.php -- MODIFIED FOR BOOTSTRAP --}}
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
        
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        
        <style>
            body {
                background-color: #f8f9fa;
            }
            .navbar-brand {
                font-size: 1.5rem;
                font-weight: 600;
            }
            .nav-link.active {
                font-weight: 600;
            }
            .card {
                border: none;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                border-radius: 8px;
            }
            .btn {
                border-radius: 6px;
                font-weight: 500;
            }
            .table {
                background: white;
            }
            /* Ensure navbar collapse works properly */
            @media (min-width: 992px) {
                .navbar-collapse {
                    display: flex !important;
                }
            }
            /* Make sure nav links are visible */
            .navbar-nav .nav-link {
                color: rgba(255, 255, 255, 0.85) !important;
            }
            .navbar-nav .nav-link:hover {
                color: rgba(255, 255, 255, 1) !important;
            }
            /* Fix pagination styling for Bootstrap */
            .pagination {
                margin-bottom: 0;
            }
            .pagination .page-link {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
                line-height: 1.5;
                color: #0d6efd;
                background-color: #fff;
                border: 1px solid #dee2e6;
            }
            .pagination .page-link:hover {
                z-index: 2;
                color: #0a58ca;
                background-color: #e9ecef;
                border-color: #dee2e6;
            }
            .pagination .page-item.active .page-link {
                z-index: 3;
                color: #fff;
                background-color: #0d6efd;
                border-color: #0d6efd;
            }
            .pagination .page-item.disabled .page-link {
                color: #6c757d;
                pointer-events: none;
                background-color: #fff;
                border-color: #dee2e6;
            }
            /* Ensure pagination arrows are properly sized */
            .pagination .page-link {
                min-width: 38px;
                text-align: center;
            }
        </style>

    </head>
    <body>
        <div id="app"> {{-- Common practice to wrap in an ID --}}
            {{-- IMPORTANT: Replace Breeze navigation with a Bootstrap Navbar --}}
            @include('layouts.partials.navbar-bootstrap') {{-- CREATE THIS FILE with Bootstrap navbar code --}}


            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-light border-bottom mb-4"> {{-- Basic Bootstrap header styling --}}
                    <div class="container py-3"> {{-- Use Bootstrap container --}}
                        {{ $header }} {{-- This will render the H2 from your pages --}}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="py-4"> {{-- Add some padding --}}
                 <div class="container"> {{-- Wrap main content in a container --}}
                    @yield('content')
                 </div>
            </main>
        </div>

        <!-- Bootstrap JS Bundle (includes Popper) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    </body>
</html>
