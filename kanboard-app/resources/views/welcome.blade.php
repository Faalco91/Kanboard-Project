{{-- Template welcome.blade.php complet pour resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- SEO Meta Tags optimisées --}}
    <title>Kanboard - Organisez, Visualisez, Avancez | Tableau Kanban Collaboratif</title>
    <meta name="description" content="Kanboard, votre tableau de bord inspiré de la méthode Kanban. Glissez, déposez, priorisez… chaque tâche trouve sa place, en solo ou en équipe.">
    <meta name="keywords" content="kanboard, kanban, gestion projet, tâches, organisation, productivité, collaboration, tableau, équipe">
    <meta name="author" content="Kanboard Team">
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Open Graph --}}
    <meta property="og:title" content="Kanboard - Organisez, Visualisez, Avancez">
    <meta property="og:description" content="Kanboard, votre tableau de bord inspiré de la méthode Kanban. Glissez, déposez, priorisez… chaque tâche trouve sa place, en solo ou en équipe.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ asset('images/kanboard-og.png') }}">
    <meta property="og:site_name" content="Kanboard">

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Kanboard - Organisez, Visualisez, Avancez">
    <meta name="twitter:description" content="Kanboard, votre tableau de bord inspiré de la méthode Kanban.">
    <meta name="twitter:image" content="{{ asset('images/kanboard-og.png') }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/Kanboard_icon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="canonical" href="{{ url('/') }}">
    <link rel="sitemap" type="application/xml" href="{{ url('/sitemap.xml') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Script pour le thème --}}
    <script>
        // Application immédiate du thème pour éviter le flash
        (function() {
            const savedTheme = localStorage.getItem('kanboard-theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            document.documentElement.classList.toggle('dark', theme === 'dark');
            
            // Mise à jour du toggle au chargement
            document.addEventListener('DOMContentLoaded', function() {
                const themeToggle = document.getElementById('theme-toggle');
                if (themeToggle) {
                    themeToggle.checked = theme === 'dark';
                }
            });
        })();
        
        // Fonction globale pour le toggle du thème
        window.toggleTheme = function() {
            const isDark = document.documentElement.classList.contains('dark');
            const newTheme = isDark ? 'light' : 'dark';
            
            document.documentElement.classList.toggle('dark', newTheme === 'dark');
            localStorage.setItem('kanboard-theme', newTheme);
            
            // Mise à jour du toggle
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                themeToggle.checked = newTheme === 'dark';
            }
        };
    </script>

    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>

<body class="antialiased bg-white dark:bg-gray-900 transition-colors duration-300">
    {{-- Header --}}
    @if(request()->path() === '/')
        <header class="relative z-50">
            <nav class="bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center py-4">
                        {{-- Logo --}}
                        <div class="flex items-center">
                            <a href="{{ url('/') }}" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-columns text-white text-lg"></i>
                                </div>
                                <span class="text-xl font-bold text-gray-900 dark:text-white">Kanboard</span>
                            </a>
                        </div>

                        {{-- Navigation desktop --}}
                        <div class="hidden md:flex items-center space-x-8">
                            <a href="#features" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                Fonctionnalités
                            </a>
                            <a href="#demo" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                Démo
                            </a>
                            <a href="#contact" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                Contact
                            </a>
                            
                            {{-- Toggle de thème --}}
                            <button onclick="toggleTheme()" 
                                    class="relative inline-flex items-center cursor-pointer p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                    title="Basculer le thème">
                                <div class="relative w-10 h-6 bg-gray-200 dark:bg-gray-700 rounded-full transition-colors duration-300">
                                    <input type="checkbox" id="theme-toggle" class="sr-only">
                                    <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white dark:bg-gray-900 rounded-full shadow transform transition-transform duration-300 dark:translate-x-4">
                                        <i class="fas fa-sun absolute inset-0 flex items-center justify-center text-xs text-yellow-500 opacity-100 dark:opacity-0 transition-opacity duration-300"></i>
                                        <i class="fas fa-moon absolute inset-0 flex items-center justify-center text-xs text-blue-400 opacity-0 dark:opacity-100 transition-opacity duration-300"></i>
                                    </div>
                                </div>
                            </button>
                        </div>

                        {{-- Actions utilisateur --}}
                        <div class="flex items-center space-x-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" 
                                   class="hidden sm:inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                    <i class="fas fa-tachometer-alt mr-2"></i>
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" 
                                   class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    Connexion
                                </a>
                                <a href="{{ route('register') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                    Inscription
                                </a>
                            @endauth

                            {{-- Menu mobile --}}
                            <div class="md:hidden" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    <i class="fas fa-bars text-lg"></i>
                                </button>
                                
                                {{-- Menu mobile dropdown --}}
                                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute right-4 top-16 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2">
                                    <a href="#features" class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Fonctionnalités</a>
                                    <a href="#demo" class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Démo</a>
                                    <a href="#contact" class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Contact</a>
                                    @guest
                                        <div class="border-t border-gray-200 dark:border-gray-700 mt-2 pt-2">
                                            <a href="{{ route('login') }}" class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Connexion</a>
                                            <a href="{{ route('register') }}" class="block px-4 py-2 text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700">Inscription</a>
                                        </div>
                                    @endguest
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </header>
    @endif

    {{-- Contenu principal --}}
    <main role="main">
        {{-- Section Hero --}}
        <section class="hero-gradient py-16 sm:py-24 fade-in">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <div class="mb-8">
                        <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-columns text-white text-3xl"></i>
                        </div>
                    </div>
                    
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-6 slide-up">
                        Organisez. Visualisez. Avancez.
                    </h1>
                    
                    <p class="text-xl sm:text-2xl text-white/90 mb-8 max-w-3xl mx-auto slide-up" style="animation-delay: 0.2s;">
                        Kanboard, votre tableau de bord inspiré de la méthode Kanban.<br>
                        Glissez, déposez, priorisez… chaque tâche trouve sa place, en solo ou en équipe.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6 slide-up" style="animation-delay: 0.4s;">
                        @guest
                            <a href="{{ route('register') }}" 
                               class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-50 transition-colors shadow-lg">
                                <i class="fas fa-rocket mr-2"></i>
                                Commencer gratuitement
                            </a>
                            <a href="#demo" 
                               class="inline-flex items-center px-8 py-4 bg-white/20 text-white font-semibold rounded-lg hover:bg-white/30 transition-colors backdrop-blur-sm">
                                <i class="fas fa-play mr-2"></i>
                                Voir la démo
                            </a>
                        @else
                            <a href="{{ url('/dashboard') }}" 
                               class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-50 transition-colors shadow-lg">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                Accéder au Dashboard
                            </a>
                        @endguest
                    </div>
                    
                    {{-- Badges de fonctionnalités --}}
                    <div class="flex flex-wrap justify-center items-center space-x-6 mt-12 text-white/80">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-400"></i>
                            <span class="text-sm">Collaboratif</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-400"></i>
                            <span class="text-sm">Temps réel</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-400"></i>
                            <span class="text-sm">Multi-plateformes</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-400"></i>
                            <span class="text-sm">Hors ligne</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Section Fonctionnalités --}}
        <section id="features" class="py-16 sm:py-24 bg-gray-50 dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                        Pourquoi choisir Kanboard ?
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                        Découvrez les fonctionnalités qui font de Kanboard votre outil de productivité préféré
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    {{-- Fonctionnalité 1 : Vue Kanban --}}
                    <div class="bg-white dark:bg-gray-700 rounded-xl p-6 shadow-sm hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-columns text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Vue Kanban</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Organisez vos tâches en colonnes personnalisables avec un système de glisser-déposer intuitif.
                        </p>
                    </div>
                    
                    {{-- Fonctionnalité 2 : Collaboration --}}
                    <div class="bg-white dark:bg-gray-700 rounded-xl p-6 shadow-sm hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-users text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Collaboration</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Travaillez en équipe en temps réel avec un système d'invitation et de gestion des membres.
                        </p>
                    </div>
                    
                    {{-- Fonctionnalité 3 : Vue Calendrier --}}
                    <div class="bg-white dark:bg-gray-700 rounded-xl p-6 shadow-sm hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-calendar text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Vue Calendrier</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Visualisez vos échéances sur un calendrier avec des vues jour, semaine et mois.
                        </p>
                    </div>
                    
                    {{-- Fonctionnalité 4 : Vue Liste --}}
                    <div class="bg-white dark:bg-gray-700 rounded-xl p-6 shadow-sm hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-list text-yellow-600 dark:text-yellow-400 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Vue Liste</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Triez et filtrez vos tâches dans une vue liste détaillée avec recherche avancée.
                        </p>
                    </div>
                    
                    {{-- Fonctionnalité 5 : Responsive --}}
                    <div class="bg-white dark:bg-gray-700 rounded-xl p-6 shadow-sm hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-mobile-alt text-red-600 dark:text-red-400 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Responsive</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Accédez à vos projets depuis n'importe quel appareil avec une interface adaptative.
                        </p>
                    </div>
                    
                    {{-- Fonctionnalité 6 : Statistiques --}}
                    <div class="bg-white dark:bg-gray-700 rounded-xl p-6 shadow-sm hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-chart-bar text-indigo-600 dark:text-indigo-400 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Statistiques</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Analysez la productivité de votre équipe avec des rapports détaillés.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Section Démo --}}
        <section id="demo" class="py-16 sm:py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                        Kanboard en action
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400">
                        Découvrez comment Kanboard transforme la gestion de vos projets
                    </p>
                </div>
                
                {{-- Démo interactive Kanban --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 lg:p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6" id="kanban-demo">
                        {{-- Colonne À faire --}}
                        <div class="kanban-column bg-gray-50 dark:bg-gray-700 rounded-lg p-4" data-column="todo">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                <i class="fas fa-inbox mr-2 text-gray-500"></i>
                                À faire
                                <span class="ml-auto bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs px-2 py-1 rounded-full task-count">3</span>
                            </h4>
                            <div class="kanban-cards space-y-3 min-h-[200px]" ondrop="drop(event)" ondragover="allowDrop(event)">
                                <div class="kanban-card bg-white dark:bg-gray-600 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-500 cursor-move hover:shadow-md transition-all duration-200" 
                                     draggable="true" ondragstart="drag(event)" data-task-id="1">
                                    <h5 class="font-medium text-gray-900 dark:text-gray-100 text-sm mb-1">Conception UI/UX</h5>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Interface utilisateur moderne</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded">Design</span>
                                        <span class="text-xs text-gray-400">2j</span>
                                    </div>
                                </div>
                                <div class="kanban-card bg-white dark:bg-gray-600 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-500 cursor-move hover:shadow-md transition-all duration-200" 
                                     draggable="true" ondragstart="drag(event)" data-task-id="2">
                                    <h5 class="font-medium text-gray-900 dark:text-gray-100 text-sm mb-1">API Documentation</h5>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Documentation complète</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-2 py-1 rounded">Docs</span>
                                        <span class="text-xs text-gray-400">1j</span>
                                    </div>
                                </div>
                                <div class="kanban-card bg-white dark:bg-gray-600 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-500 cursor-move hover:shadow-md transition-all duration-200" 
                                     draggable="true" ondragstart="drag(event)" data-task-id="3">
                                    <h5 class="font-medium text-gray-900 dark:text-gray-100 text-sm mb-1">Tests unitaires</h5>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Coverage 90%+</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded">Tests</span>
                                        <span class="text-xs text-gray-400">3j</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Colonne En cours --}}
                        <div class="kanban-column bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4" data-column="progress">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                <i class="fas fa-play mr-2 text-blue-500"></i>
                                En cours
                                <span class="ml-auto bg-blue-200 dark:bg-blue-800 text-blue-700 dark:text-blue-300 text-xs px-2 py-1 rounded-full task-count">2</span>
                            </h4>
                            <div class="kanban-cards space-y-3 min-h-[200px]" ondrop="drop(event)" ondragover="allowDrop(event)">
                                <div class="kanban-card bg-white dark:bg-gray-600 rounded-lg p-3 shadow-sm border border-blue-200 dark:border-blue-700 cursor-move hover:shadow-md transition-all duration-200" 
                                     draggable="true" ondragstart="drag(event)" data-task-id="4">
                                    <h5 class="font-medium text-gray-900 dark:text-gray-100 text-sm mb-1">Vue Kanban</h5>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Glisser-déposer fonctionnel</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded">Dev</span>
                                        <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-1">
                                            <div class="bg-blue-500 h-1 rounded-full" style="width: 75%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="kanban-card bg-white dark:bg-gray-600 rounded-lg p-3 shadow-sm border border-blue-200 dark:border-blue-700 cursor-move hover:shadow-md transition-all duration-200" 
                                     draggable="true" ondragstart="drag(event)" data-task-id="5">
                                    <h5 class="font-medium text-gray-900 dark:text-gray-100 text-sm mb-1">Base de données</h5>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Migrations et modèles</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-2 py-1 rounded">Backend</span>
                                        <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-1">
                                            <div class="bg-blue-500 h-1 rounded-full" style="width: 45%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Colonne Test --}}
                        <div class="kanban-column bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4" data-column="test">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                <i class="fas fa-bug mr-2 text-yellow-500"></i>
                                Test
                                <span class="ml-auto bg-yellow-200 dark:bg-yellow-800 text-yellow-700 dark:text-yellow-300 text-xs px-2 py-1 rounded-full task-count">1</span>
                            </h4>
                            <div class="kanban-cards space-y-3 min-h-[200px]" ondrop="drop(event)" ondragover="allowDrop(event)">
                                <div class="kanban-card bg-white dark:bg-gray-600 rounded-lg p-3 shadow-sm border border-yellow-200 dark:border-yellow-700 cursor-move hover:shadow-md transition-all duration-200" 
                                     draggable="true" ondragstart="drag(event)" data-task-id="6">
                                    <h5 class="font-medium text-gray-900 dark:text-gray-100 text-sm mb-1">Authentification</h5>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Tests de sécurité</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-2 py-1 rounded">Security</span>
                                        <span class="text-xs text-gray-400">Aujourd'hui</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Colonne Terminé --}}
                        <div class="kanban-column bg-green-50 dark:bg-green-900/20 rounded-lg p-4" data-column="done">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                <i class="fas fa-check mr-2 text-green-500"></i>
                                Terminé
                                <span class="ml-auto bg-green-200 dark:bg-green-800 text-green-700 dark:text-green-300 text-xs px-2 py-1 rounded-full task-count">4</span>
                            </h4>
                            <div class="kanban-cards space-y-3 min-h-[200px]" ondrop="drop(event)" ondragover="allowDrop(event)">
                                <div class="kanban-card bg-white dark:bg-gray-600 rounded-lg p-3 shadow-sm border border-green-200 dark:border-green-700 opacity-80 cursor-move" 
                                     draggable="true" ondragstart="drag(event)" data-task-id="7">
                                    <h5 class="font-medium text-gray-900 dark:text-gray-100 text-sm mb-1">Setup Laravel</h5>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Configuration initiale</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-2 py-1 rounded">Backend</span>
                                        <i class="fas fa-check text-green-500 text-xs"></i>
                                    </div>
                                </div>
                                <div class="kanban-card bg-white dark:bg-gray-600 rounded-lg p-3 shadow-sm border border-green-200 dark:border-green-700 opacity-80 cursor-move" 
                                     draggable="true" ondragstart="drag(event)" data-task-id="8">
                                    <h5 class="font-medium text-gray-900 dark:text-gray-100 text-sm mb-1">Authentification</h5>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Login/Register complet</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-2 py-1 rounded">Auth</span>
                                        <i class="fas fa-check text-green-500 text-xs"></i>
                                    </div>
                                </div>
                                <div class="kanban-card bg-white dark:bg-gray-600 rounded-lg p-3 shadow-sm border border-green-200 dark:border-green-700 opacity-80 cursor-move" 
                                     draggable="true" ondragstart="drag(event)" data-task-id="9">
                                    <h5 class="font-medium text-gray-900 dark:text-gray-100 text-sm mb-1">Design System</h5>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Composants UI</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded">Design</span>
                                        <i class="fas fa-check text-green-500 text-xs"></i>
                                    </div>
                                </div>
                                <div class="kanban-card bg-white dark:bg-gray-600 rounded-lg p-3 shadow-sm border border-green-200 dark:border-green-700 opacity-80 cursor-move" 
                                     draggable="true" ondragstart="drag(event)" data-task-id="10">
                                    <h5 class="font-medium text-gray-900 dark:text-gray-100 text-sm mb-1">Déploiement</h5>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Production ready</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-2 py-1 rounded">DevOps</span>
                                        <i class="fas fa-check text-green-500 text-xs"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-8">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        <i class="fas fa-hand-paper text-blue-500 mr-2"></i>
                        Glissez-déposez les cartes entre les colonnes pour organiser votre travail
                    </p>
                    @guest
                        <a href="{{ route('register') }}" 
                           class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                            <i class="fas fa-play mr-2"></i>
                            Essayer maintenant
                        </a>
                    @endguest
                </div>
            </div>
        </section>

        {{-- Section statistiques de l'équipe --}}
        <section class="py-16 sm:py-24 bg-gray-50 dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                        Suivi et performance
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400">
                        Analysez la productivité de votre équipe avec des insights précieux
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    {{-- Statistique 1 --}}
                    <div class="bg-white dark:bg-gray-700 rounded-xl p-6 text-center">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-tasks text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">2,847</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Tâches complétées</div>
                    </div>
                    
                    {{-- Statistique 2 --}}
                    <div class="bg-white dark:bg-gray-700 rounded-xl p-6 text-center">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">156</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Équipes actives</div>
                    </div>
                    
                    {{-- Statistique 3 --}}
                    <div class="bg-white dark:bg-gray-700 rounded-xl p-6 text-center">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-project-diagram text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">89</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Projets en cours</div>
                    </div>
                    
                    {{-- Statistique 4 --}}
                    <div class="bg-white dark:bg-gray-700 rounded-xl p-6 text-center">
                        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">4.2j</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Temps moyen</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Section contact/CTA finale --}}
        <section id="contact" class="py-16 sm:py-24 bg-gradient-to-r from-blue-600 to-purple-600">
            <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">
                    Prêt à transformer votre productivité ?
                </h2>
                <p class="text-xl text-white/90 mb-8">
                    Rejoignez des milliers d'équipes qui utilisent déjà Kanboard pour gérer leurs projets
                </p>
                @guest
                    <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="{{ route('register') }}" 
                           class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-50 transition-colors shadow-lg">
                            <i class="fas fa-rocket mr-2"></i>
                            Commencer gratuitement
                        </a>
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center px-8 py-4 bg-white/20 text-white font-semibold rounded-lg hover:bg-white/30 transition-colors backdrop-blur-sm">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Se connecter
                        </a>
                    </div>
                @else
                    <a href="{{ url('/dashboard') }}" 
                       class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-50 transition-colors shadow-lg">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Accéder au Dashboard
                    </a>
                @endguest
                
                {{-- Informations de contact --}}
                <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8 text-white/80">
                    <div class="text-center">
                        <i class="fas fa-envelope text-2xl mb-2"></i>
                        <div class="text-sm">Email</div>
                        <div class="font-semibold">contact@kanboard.fr</div>
                    </div>
                    <div class="text-center">
                        <i class="fas fa-phone text-2xl mb-2"></i>
                        <div class="text-sm">Téléphone</div>
                        <div class="font-semibold">+33 1 23 45 67 89</div>
                    </div>
                    <div class="text-center">
                        <i class="fas fa-map-marker-alt text-2xl mb-2"></i>
                        <div class="text-sm">Adresse</div>
                        <div class="font-semibold">Paris, France</div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                {{-- Logo et description --}}
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-columns text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-bold">Kanboard</span>
                    </div>
                    <p class="text-gray-400 mb-4 max-w-md">
                        Simple, efficace et collaboratif.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-blue-500 transition-colors">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-blue-500 transition-colors">
                            <i class="fab fa-linkedin text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-blue-500 transition-colors">
                            <i class="fab fa-github text-xl"></i>
                        </a>
                    </div>
                </div>
                
                {{-- Liens produit --}}
                <div>
                    <h4 class="font-semibold text-gray-100 mb-4">Produit</h4>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-gray-400 hover:text-blue-400 transition-colors">Fonctionnalités</a></li>
                        <li><a href="#demo" class="text-gray-400 hover:text-blue-400 transition-colors">Démo</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">Tarifs</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">Aide</a></li>
                    </ul>
                </div>
                
                {{-- Liens légaux --}}
                <div>
                    <h4 class="font-semibold text-gray-100 mb-4">Légal</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">Confidentialité</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">Conditions</a></li>
                        <li><a href="#contact" class="text-gray-400 hover:text-blue-400 transition-colors">Contact</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 pt-8 flex flex-col sm:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm">
                    &copy; {{ date('Y') }} Kanboard. Tous droits réservés.
                </p>
                <p class="text-gray-500 text-sm mt-2 sm:mt-0">
                    Fait avec <i class="fas fa-heart text-red-500"></i> par l'équipe Kanboard
                </p>
            </div>
        </div>
    </footer>

    {{-- Scripts JavaScript --}}
    <script>
        // Variables globales pour la démo Kanban
        let draggedElement = null;
        let moveCount = 0;

        // Fonctions pour le glisser-déposer de la démo Kanban
        function allowDrop(ev) {
            ev.preventDefault();
            ev.currentTarget.classList.add('drag-over');
        }

        function drag(ev) {
            draggedElement = ev.target;
            ev.dataTransfer.setData("text", ev.target.getAttribute('data-task-id'));
            ev.target.style.opacity = '0.5';
            
            // Ajouter effet visuel pendant le drag
            setTimeout(() => {
                ev.target.classList.add('dragging');
            }, 0);
        }

        function drop(ev) {
            ev.preventDefault();
            ev.currentTarget.classList.remove('drag-over');
            
            const data = ev.dataTransfer.getData("text");
            const targetColumn = ev.currentTarget.closest('.kanban-column');
            const sourceColumn = draggedElement.closest('.kanban-column');
            
            if (targetColumn && draggedElement && targetColumn !== sourceColumn) {
                // Ajouter l'élément à la nouvelle colonne
                ev.currentTarget.appendChild(draggedElement);
                
                // Mettre à jour les compteurs
                updateTaskCounts();
                
                // Incrémenter le compteur de mouvements
                moveCount++;
                document.getElementById('demo-moves').textContent = moveCount;
                
                // Mettre à jour l'action
                const sourceColumnName = getColumnName(sourceColumn.getAttribute('data-column'));
                const targetColumnName = getColumnName(targetColumn.getAttribute('data-column'));
                const taskName = draggedElement.querySelector('h5').textContent;
                document.getElementById('demo-action').textContent = `"${taskName}" → ${targetColumnName}`;
                
                // Animation de succès
                draggedElement.classList.add('drop-success');
                setTimeout(() => {
                    draggedElement.classList.remove('drop-success');
                }, 500);
                
                // Notification de feedback
                showDemoNotification(`Tâche déplacée vers "${targetColumnName}"!`);
            }
            
            // Réinitialiser l'apparence
            if (draggedElement) {
                draggedElement.style.opacity = '1';
                draggedElement.classList.remove('dragging');
            }
            
            // Nettoyer tous les indicateurs visuels
            document.querySelectorAll('.drag-over').forEach(el => {
                el.classList.remove('drag-over');
            });
        }

        function updateTaskCounts() {
            document.querySelectorAll('.kanban-column').forEach(column => {
                const count = column.querySelectorAll('.kanban-card').length;
                const countSpan = column.querySelector('.task-count');
                if (countSpan) {
                    countSpan.textContent = count;
                }
            });
        }

        function getColumnName(columnType) {
            const names = {
                'todo': 'À faire',
                'progress': 'En cours',
                'test': 'Test',
                'done': 'Terminé'
            };
            return names[columnType] || columnType;
        }

        function showDemoNotification(message) {
            // Créer et afficher une notification temporaire
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
            notification.textContent = message;
            document.body.appendChild(notification);
            
            // Animer l'entrée
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Supprimer après 3 secondes
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        // Animation au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        // Observer tous les éléments avec animation
        document.addEventListener('DOMContentLoaded', () => {
            const animatedElements = document.querySelectorAll('.fade-in, .slide-up');
            animatedElements.forEach(el => observer.observe(el));
            
            // Ajouter le smooth scroll pour les liens d'ancrage
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);
                    
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Gestionnaires d'événements pour la démo Kanban
            document.querySelectorAll('.kanban-card').forEach(card => {
                card.addEventListener('dragend', function() {
                    this.style.opacity = '1';
                    this.classList.remove('dragging');
                });
                
                card.addEventListener('dragstart', function() {
                    // Ajouter un petit délai pour l'effet visuel
                    setTimeout(() => {
                        document.querySelectorAll('.kanban-cards').forEach(zone => {
                            zone.classList.add('drop-zone-active');
                        });
                    }, 100);
                });
            });

            // Nettoyer les zones de drop quand le drag se termine
            document.addEventListener('dragend', function() {
                document.querySelectorAll('.kanban-cards').forEach(zone => {
                    zone.classList.remove('drop-zone-active', 'drag-over');
                });
            });

            // Initialiser les compteurs
            updateTaskCounts();
        });

        // Fonction pour gérer le menu mobile
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }
    </script>

    {{-- Alpine.js pour les interactions --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Styles additionnels pour les animations --}}
    <style>
        /* Animations CSS personnalisées */
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .fade-in.animate-in {
            opacity: 1;
            transform: translateY(0);
        }

        .slide-up {
            opacity: 0;
            transform: translateY(30px);
            animation: slideUp 0.8s ease-out forwards;
        }

        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Styles pour le toggle de thème */
        .theme-toggle input:checked + span {
            background-color: #374151;
        }

        .theme-toggle input:checked + span > span {
            transform: translateX(1.25rem);
        }

        /* Styles pour le glisser-déposer Kanban */
        .kanban-card {
            will-change: transform;
            transition: all 0.2s ease;
        }

        .kanban-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .kanban-card.dragging {
            transform: rotate(5deg) scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            z-index: 1000;
        }

        .kanban-cards {
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .kanban-cards.drop-zone-active {
            background-color: rgba(59, 130, 246, 0.1);
            border: 2px dashed rgba(59, 130, 246, 0.3);
        }

        .kanban-cards.drag-over {
            background-color: rgba(59, 130, 246, 0.2);
            border: 2px solid rgba(59, 130, 246, 0.5);
            transform: scale(1.02);
        }

        .drop-success {
            animation: dropSuccess 0.5s ease;
        }

        @keyframes dropSuccess {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); background-color: rgba(34, 197, 94, 0.2); }
            100% { transform: scale(1); }
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .slide-up {
                animation-delay: 0s !important;
            }
            
            .kanban-card {
                cursor: default;
            }
            
            .kanban-card:hover {
                transform: none;
            }
        }

        /* Performance optimizations */
        .cursor-move {
            will-change: transform;
            transition: transform 0.2s ease;
        }

        /* Mode sombre */
        @media (prefers-color-scheme: dark) {
            .hero-gradient {
                background: linear-gradient(135deg, #4c51bf 0%, #553c9a 100%);
            }
            
            .kanban-cards.drop-zone-active {
                background-color: rgba(59, 130, 246, 0.15);
            }
            
            .kanban-cards.drag-over {
                background-color: rgba(59, 130, 246, 0.25);
            }
        }

        /* Animations d'entrée pour le contenu */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Styles pour les notifications temporaires */
        .notification-enter {
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }

        .notification-show {
            transform: translateX(0);
        }

        .notification-exit {
            transform: translateX(100%);
        }

        /* Interactions touch sur mobile */
        @media (max-width: 768px) {
            .kanban-card {
                touch-action: manipulation;
            }
        }

        /* Styles pour l'accessibilité */
        .kanban-card:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        /* Animation du toggle de thème */
        .dark .fa-sun {
            opacity: 0;
            transform: rotate(180deg);
        }

        .dark .fa-moon {
            opacity: 1;
            transform: rotate(0deg);
        }

        .fa-sun, .fa-moon {
            transition: all 0.3s ease;
        }

        /* Styles pour les badges de fonctionnalités */
        .feature-badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
    </style>
</body>
</html>
