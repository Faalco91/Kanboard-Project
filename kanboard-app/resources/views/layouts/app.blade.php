<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- SEO basique --}}
    <title>@yield('title', config('app.name', 'Kanboard'))</title>
    <meta name="description" content="@yield('description', 'Kanboard - Votre application de gestion de projet Kanban préférée')">
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    {{-- Scripts critiques pour éviter le flash du thème --}}
    <script>
        // Application immédiate du thème
        const savedTheme = localStorage.getItem('kanboard-theme');
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        const theme = savedTheme || systemTheme;
        
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        }
        
        // Config globale simple
        window.Kanboard = {
            csrfToken: '{{ csrf_token() }}',
            userId: {{ auth()->id() ?? 'null' }}
        };
    </script>
    
    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/css/responsive.css'])
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        {{-- Navigation --}}
        @include('layouts.navigation')
        
        {{-- Header optionnel --}}
        @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset
        
        {{-- Messages flash --}}
        @include('layouts.flash-messages')
        
        {{-- Contenu principal --}}
        <main>
            {{ $slot }}
        </main>
    </div>
    
    {{-- Scripts --}}
    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>
</html>
