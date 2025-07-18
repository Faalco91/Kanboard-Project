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
            
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    {{-- Assets --}}
    @vite(['resources/css/app.css'])

    {{-- Styles inline --}}
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .feature-card {
            @apply bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700;
            @apply hover:shadow-lg transition-all duration-300 hover:-translate-y-2;
        }
        
        .demo-board {
            @apply bg-white dark:bg-gray-800 rounded-lg p-4 shadow-inner border border-gray-200 dark:border-gray-700;
        }
        
        .demo-column {
            @apply bg-gray-50 dark:bg-gray-700 rounded-lg p-3 min-h-32;
        }
        
        .demo-card {
            @apply bg-white dark:bg-gray-600 rounded p-2 mb-2 text-sm shadow-sm;
        }
        
        .theme-toggle {
            @apply relative inline-block w-12 h-6 cursor-pointer;
        }
        
        .theme-slider {
            @apply absolute inset-0 bg-gray-300 dark:bg-gray-600 rounded-full transition-colors duration-300;
            @apply before:absolute before:content-[''] before:w-5 before:h-5 before:left-0.5 before:top-0.5;
            @apply before:bg-white before:rounded-full before:transition-transform before:duration-300;
        }
        
        .dark .theme-slider {
            @apply before:translate-x-6;
        }
        
        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }
        
        .slide-up {
            animation: slideUp 0.8s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    {{-- JSON-LD Structured Data --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebApplication",
        "name": "Kanboard",
        "description": "Kanboard, votre tableau de bord inspiré de la méthode Kanban",
        "url": "{{ url('/') }}",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web Browser",
        "featureList": [
            "Tableaux Kanban personnalisables",
            "Collaboration en équipe en temps réel",
            "Interface responsive",
            "Mode hors-ligne"
        ]
    }
    </script>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100" x-data="{ darkMode: false }" x-init="darkMode = document.documentElement.classList.contains('dark')">
    
    {{-- Navigation --}}
    <header role="banner" class="sticky top-0 z-40 bg-white/80 dark:bg-gray-900/80 backdrop-blur-lg border-b border-gray-200 dark:border-gray-700">
        @if (Route::has('login'))
            <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" role="navigation" aria-label="Navigation principale">
                <div class="flex justify-between items-center h-16">
                    {{-- Logo --}}
                    <div class="flex items-center">
                        <a href="{{ url('/') }}" aria-label="Retour à l'accueil Kanboard" class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-columns text-white text-sm"></i>
                            </div>
                            <span class="font-bold text-xl text-gray-900 dark:text-gray-100">Kanboard</span>
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
                        <div x-data="themeToggle()">
                            <label class="theme-toggle" title="Basculer le thème">
                                <input type="checkbox" x-model="isDark" @change="toggle()" class="sr-only">
                                <span class="theme-slider relative">
                                    <i class="fas fa-sun absolute left-1 top-1 text-xs text-yellow-500 transition-opacity duration-300" 
                                       :class="{ 'opacity-0': isDark, 'opacity-100': !isDark }"></i>
                                    <i class="fas fa-moon absolute right-1 top-1 text-xs text-blue-400 transition-opacity duration-300" 
                                       :class="{ 'opacity-100': isDark, 'opacity-0': !isDark }"></i>
                                </span>
                            </label>
                        </div>
                    </div>

                    {{-- Actions utilisateur --}}
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" 
                               class="hidden sm:inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" 
                               class="hidden sm:inline-block text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors">
                                Connexion
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg shadow-sm hover:shadow-md transition-all duration-200 transform hover:scale-105">
                                    <i class="fas fa-rocket mr-2"></i>
                                    Commencer
                                </a>
                            @endif
                        @endauth
                        
                        {{-- Menu mobile --}}
                        <div class="md:hidden" x-data="{ open: false }">
                            <button @click="open = !open" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <i class="fas fa-bars" x-show="!open"></i>
                                <i class="fas fa-times" x-show="open"></i>
                            </button>
                            
                            {{-- Menu mobile dropdown --}}
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition
                                 class="absolute top-16 right-4 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2">
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
            </nav>
        @endif
    </header>

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

                    {{-- CTA Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center slide-up" style="animation-delay: 0.4s;">
                        @auth
                            <a href="{{ route('dashboard') }}" 
                               class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                Accéder au Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" 
                               class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-rocket mr-2"></i>
                                Commencer gratuitement
                            </a>
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center px-8 py-4 bg-white/20 text-white font-semibold rounded-xl border border-white/30 hover:bg-white/30 transition-all duration-200">
                                J'ai déjà un compte
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </section>

        {{-- Section des fonctionnalités --}}
        <section id="features" class="py-16 sm:py-24 bg-white dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                        Pourquoi choisir Kanboard ?
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                        Une solution complète pour gérer vos projets efficacement avec la méthode Kanban
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <article class="feature-card">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-columns text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3">
                            Tableaux Kanban
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Organisez vos projets avec des colonnes personnalisables et un glisser-déposer intuitif pour une productivité maximale.
                        </p>
                    </article>
                    
                    <article class="feature-card">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-users text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3">
                            Collaboration
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Travaillez en équipe avec des mises à jour en temps réel et une synchronisation automatique entre tous les membres.
                        </p>
                    </article>
                    
                    <article class="feature-card">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-mobile-alt text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3">
                            Responsive
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Accédez à vos projets depuis n'importe quel appareil : ordinateur, tablette ou mobile avec un design adaptatif.
                        </p>
                    </article>
                    
                    <article class="feature-card">
                        <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-sync-alt text-orange-600 dark:text-orange-400 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3">
                            Mode hors-ligne
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Continuez à travailler sans connexion. Vos modifications se synchronisent automatiquement une fois reconnecté.
                        </p>
                    </article>

                    <article class="feature-card">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-chart-line text-red-600 dark:text-red-400 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3">
                            Statistiques
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Suivez les performances avec des rapports détaillés et des métriques avancées pour optimiser votre productivité.
                        </p>
                    </article>
                    
                    <article class="feature-card">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-shield-alt text-indigo-600 dark:text-indigo-400 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3">
                            Sécurité
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Vos données sont protégées avec un chiffrement avancé et des sauvegardes automatiques pour une tranquillité d'esprit totale.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        {{-- Section démo --}}
        <section id="demo" class="py-16 sm:py-24 bg-gray-50 dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                        Kanboard en action
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                        Découvrez comment Kanboard transforme votre gestion de projets avec une interface intuitive
                    </p>
                </div>
                
                <div class="demo-board max-w-4xl mx-auto">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="demo-column">
                            <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                                À faire
                            </h4>
                            <div class="demo-card">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-medium">Nouvelle fonctionnalité</span>
                                    <span class="text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded">Frontend</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Développer l'interface utilisateur</p>
                            </div>
                            <div class="demo-card">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-medium">Correction bug</span>
                                    <span class="text-xs bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-2 py-1 rounded">Urgent</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Résoudre le problème de connexion</p>
                            </div>
                        </div>
                        
                        <div class="demo-column">
                            <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                <span class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></span>
                                En cours
                            </h4>
                            <div class="demo-card">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-medium">Design interface</span>
                                    <span class="text-xs bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-2 py-1 rounded">Design</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Créer les maquettes UI/UX</p>
                            </div>
                        </div>
                        
                        <div class="demo-column">
                            <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                Terminé
                            </h4>
                            <div class="demo-card">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-medium">Setup projet</span>
                                    <span class="text-xs bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded">Config</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Configuration initiale terminée</p>
                            </div>
                            <div class="demo-card">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-medium">Documentation</span>
                                    <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-2 py-1 rounded">Docs</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Guide utilisateur rédigé</p>
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

        {{-- Section CTA finale --}}
        <section class="py-16 sm:py-24 bg-gradient-to-r from-blue-600 to-purple-600">
            <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">
                    Prêt à transformer votre productivité ?
                </h2>
                <p class="text-xl text-white/90 mb-8">
                    Rejoignez des milliers d'équipes qui utilisent déjà Kanboard pour organiser leur travail
                </p>
                
                @guest
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('register') }}" 
                           class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                            <i class="fas fa-rocket mr-2"></i>
                            Commencer gratuitement
                        </a>
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center px-8 py-4 bg-white/20 text-white font-semibold rounded-xl border border-white/30 hover:bg-white/30 transition-all duration-200">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Se connecter
                        </a>
                    </div>
                @else
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Accéder au Dashboard
                    </a>
                @endguest
            </div>
        </section>
    </main>

    {{-- Footer --}}
    <footer role="contentinfo" class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                {{-- Logo et description --}}
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-columns text-white text-sm"></i>
                        </div>
                        <span class="font-bold text-xl text-gray-900 dark:text-gray-100">Kanboard</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4 max-w-md">
                        La solution moderne pour gérer vos projets avec la méthode Kanban. Simple, efficace et collaboratif.
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
                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Produit</h4>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Fonctionnalités</a></li>
                        <li><a href="#demo" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Démo</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Tarifs</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Aide</a></li>
                    </ul>
                </div>
                
                {{-- Liens légaux --}}
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Légal</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Confidentialité</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Conditions</a></li>
                        <li><a href="#contact" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Contact</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-200 dark:border-gray-700 mt-8 pt-8 flex flex-col sm:flex-row justify-between items-center">
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                    &copy; {{ date('Y') }} Kanboard. Tous droits réservés.
                </p>
                <div class="mt-4 sm:mt-0">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        Fait avec ❤️ par l'équipe Kanboard
                    </p>
                </div>
            </div>
        </div>
    </footer>

    {{-- Scripts --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <script>
        // Fonction Alpine pour le toggle du thème
        function themeToggle() {
            return {
                isDark: localStorage.getItem('kanboard-theme') === 'dark' || 
                       (!localStorage.getItem('kanboard-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
                
                toggle() {
                    this.isDark = !this.isDark;
                    this.apply();
                },
                
                apply() {
                    if (this.isDark) {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('kanboard-theme', 'dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('kanboard-theme', 'light');
                    }
                }
            }
        }
        
        // Animation des cartes au scroll
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('slide-up');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            document.querySelectorAll('.feature-card').forEach(card => {
                observer.observe(card);
            });
            
            // Smooth scroll pour les liens d'ancrage
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
