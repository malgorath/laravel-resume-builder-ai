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
