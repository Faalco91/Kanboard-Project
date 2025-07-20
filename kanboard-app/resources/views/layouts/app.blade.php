<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- SEO Meta Tags --}}
    <title>@yield('title', config('app.name', 'Kanboard'))</title>
    <meta name="description" content="@yield('description', 'Kanboard - Votre application de gestion de projet Kanban préférée')">
    <meta name="author" content="Kanboard Team">
    <meta name="robots" content="index, follow">
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    {{-- Script critique pour éviter le flash du thème --}}
    <script>
        // Application immédiate du thème avant le rendu
        (function() {
            const savedTheme = localStorage.getItem('kanboard-theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
            
            // Configuration globale
            window.Kanboard = {
                csrfToken: '{{ csrf_token() }}',
                userId: {{ auth()->id() ?? 'null' }},
                currentTheme: theme
            };
        })();
    </script>
    
    {{-- Styles --}}
    @vite(['resources/css/app.css'])
    @stack('styles')
    
    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100" x-data="{ darkMode: false }" x-init="darkMode = document.documentElement.classList.contains('dark')">
    <div class="min-h-screen">
        {{-- Navigation --}}
        @include('layouts.navigation')
        
        {{-- Header de page --}}
        @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset
        
        {{-- Messages flash --}}
        @include('layouts.flash-messages')
        
        {{-- Contenu principal --}}
        <main class="relative">
            {{ $slot }}
        </main>
        
        {{-- Footer optionnel --}}
        @isset($footer)
            <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $footer }}
                </div>
            </footer>
        @endisset
    </div>
    
    {{-- SortableJS pour le drag & drop --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    
    {{-- Scripts --}}
    @vite(['resources/js/app.js'])
    
    {{-- Script pour le dark mode--}}
    <script>
        // Fonction Alpine pour le toggle du thème
        window.themeToggle = function() {
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
                    
                    // Mettre à jour la config globale
                    if (window.Kanboard) {
                        window.Kanboard.currentTheme = this.isDark ? 'dark' : 'light';
                    }
                    
                    // Déclencher un événement pour les autres composants
                    window.dispatchEvent(new CustomEvent('theme-changed', {
                        detail: { theme: this.isDark ? 'dark' : 'light' }
                    }));
                }
            }
        };
        
        // Écouter les changements du thème système
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (!localStorage.getItem('kanboard-theme')) {
                // Si aucune préférence stockée, suivre le système
                const event = new CustomEvent('system-theme-changed', {
                    detail: { isDark: e.matches }
                });
                window.dispatchEvent(event);
            }
        });
        
        // Fonction utilitaire pour les notifications
        window.showNotification = function(message, type = 'info', duration = 3000) {
            const notification = document.createElement('div');
            notification.className = `
                fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full
                transition-transform duration-300 ease-in-out max-w-sm
            `;
            
            // Styles selon le type
            switch(type) {
                case 'success':
                    notification.className += ' bg-green-500 text-white';
                    break;
                case 'error':
                    notification.className += ' bg-red-500 text-white';
                    break;
                case 'warning':
                    notification.className += ' bg-yellow-500 text-black';
                    break;
                default:
                    notification.className += ' bg-blue-500 text-white';
            }
            
            notification.innerHTML = `
                <div class="flex items-center justify-between">
                    <span class="font-medium">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:opacity-75">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animation d'entrée
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 10);
            
            // Suppression automatique
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            }, duration);
        };
        
        // Gestion des erreurs JavaScript
        window.addEventListener('error', function(e) {
            console.error('Erreur JavaScript:', e.error);
            if (window.Kanboard?.debug) {
                showNotification('Une erreur JavaScript s\'est produite', 'error');
            }
        });
        
        // Monitoring des performances (en développement)
        @if(app()->environment('local'))
            window.addEventListener('load', function() {
                if ('performance' in window && performance.timing) {
                    const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
                    console.log(`Temps de chargement: ${loadTime}ms`);
                }
            });
        @endif
        
        // Auto-dismiss des messages flash après 5 secondes
        document.addEventListener('DOMContentLoaded', function() {
            const flashMessages = document.querySelectorAll('[role="alert"]');
            flashMessages.forEach(message => {
                setTimeout(() => {
                    if (message.parentNode) {
                        message.style.opacity = '0';
                        message.style.transform = 'translateX(100%)';
                        setTimeout(() => message.remove(), 300);
                    }
                }, 5000);
            });
        });
    </script>
    
    {{-- Scripts spécifiques des pages --}}
    @stack('scripts')
</body>
</html>
