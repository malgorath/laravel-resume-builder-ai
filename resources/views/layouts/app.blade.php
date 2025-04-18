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

        <!-- Scripts and Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])  {{-- <<< MAKE SURE THIS IS PRESENT <<< --}}
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            {{-- Optional: Add navigation if this layout needs it --}}
            @auth
             @include('layouts.navigation')
            @else
                <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full h-full">
                        <div class="flex justify-between h-16">
                            <div class="flex">
                                <div class="shrink-0 flex items-center ">
                                    <a href="{{ route('login') }}">
                                        <x-application-logo class="block h-16 w-auto fill-current text-gray-800 dark:text-gray-200" />
                                    </a>
                                    <span class="text-gray-800 dark:text-gray-200 font-medium text-xl h-9 ms-2">
                                        Resume Builder
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>

            @endauth

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>
    </body>
</html>
