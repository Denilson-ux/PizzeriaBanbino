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
        
        <!-- Font Awesome for theme toggle icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        <!-- Theme Toggle CSS -->
        <link rel="stylesheet" href="{{ asset('resources/css/theme-toggle.css') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
        @stack('styles')
        
        <!-- Theme initialization script (debe ejecutarse lo antes posible) -->
        <script>
            // Aplicar tema inmediatamente para evitar flash
            (function() {
                const savedTheme = localStorage.getItem('theme');
                const systemTheme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                const theme = savedTheme || systemTheme;
                
                document.documentElement.setAttribute('data-theme', theme);
                document.documentElement.classList.add(`${theme}-mode`);
            })();
        </script>
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                        <!-- Theme toggle button en el header -->
                        <div class="absolute top-4 right-4">
                            <x-theme-toggle position="inline" size="sm" />
                        </div>
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts
        
        <!-- Theme Toggle JavaScript -->
        <script src="{{ asset('resources/js/theme-toggle.js') }}"></script>
        @stack('scripts')
    </body>
</html>