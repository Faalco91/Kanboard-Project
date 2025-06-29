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

        {{-- Assets --}}
        @vite(['resources/css/welcome.css'])

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
    <body>
        <header role="banner">
            @if (Route::has('login'))
                <nav class="navbar" role="navigation" aria-label="Navigation principale">
                    <div class="navbar-left">
                        <a href="{{ url('/') }}" aria-label="Retour à l'accueil Kanboard">
                            <img src="{{ asset('images/Kanboard_icon.svg') }}" 
                                 alt="Logo Kanboard" 
                                 width="80" 
                                 height="80" 
                                 class="logo">
                        </a>
                    </div>
                    <ul class="navbar-center" role="menubar">
                        <li role="none">
                            <a href="{{ url('/') }}" 
                               role="menuitem" 
                               aria-current="page">
                                Accueil
                            </a>
                        </li>
                        <li role="none">
                            <a href="{{ url('/about') }}" 
                               role="menuitem">
                                À propos
                            </a>
                        </li>
                        <li role="none">
                            <a href="{{ url('/features') }}" 
                               role="menuitem">
                                Fonctionnalités
                            </a>
                        </li>
                        <li role="none">
                            <a href="{{ url('/contact') }}" 
                               role="menuitem">
                                Contact
                            </a>
                        </li>
                    </ul>
                    <div class="navbar-right">
                        @auth
                            <a href="{{ url('/dashboard') }}" 
                               class="nav-link dashboard-link"
                               aria-label="Accéder à votre tableau de bord">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" 
                               class="nav-link"
                               aria-label="Se connecter à votre compte">
                                Connexion
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" 
                                   class="nav-link btn register-btn"
                                   aria-label="Créer un nouveau compte">
                                    Inscription
                                </a>
                            @endif
                        @endauth
                    </div>
                </nav>
            @endif
        </header>

        <div class="main-container">
            <main class="content-wrapper" role="main">
                <section class="presentation">
                    <img src="{{ asset('images/Kanboard_logo.svg') }}" 
                         alt="Logo Kanboard - Organisez, Visualisez, Avancez" 
                         width="400" 
                         height="200" 
                         class="hero-logo">
                    
                    <div class="hero-content">
                        <h1 class="hero-title">Organisez. Visualisez. Avancez.</h1>
                        <div class="hero-description">
                            <p class="lead">Kanboard, votre tableau de bord inspiré de la méthode Kanban.</p>
                            <p>Glissez, déposez, priorisez… chaque tâche trouve sa place, en solo ou en équipe.</p>
                        </div>

                        {{-- Boutons d'action --}}
                        <div class="hero-cta">
                            @auth
                                <a href="{{ route('dashboard') }}" 
                                   class="cta-button primary"
                                   aria-label="Accéder à votre tableau de bord">
                                    <span>🚀</span> Tableau de bord
                                </a>
                            @else
                                <a href="{{ route('register') }}" 
                                   class="cta-button primary"
                                   aria-label="Commencer gratuitement avec Kanboard">
                                    <span>✨</span> Commencer gratuitement
                                </a>
                                <a href="{{ route('login') }}" 
                                   class="cta-button secondary"
                                   aria-label="Se connecter à votre compte">
                                    J'ai déjà un compte
                                </a>
                            @endauth
                        </div>
                    </div>
                </section>

                {{-- Section des fonctionnalités --}}
                <section class="features-section" aria-labelledby="features-heading">
                    <div class="section-header">
                        <h2 id="features-heading" class="section-title">Pourquoi choisir Kanboard ?</h2>
                        <p class="section-subtitle">Une solution complète pour gérer vos projets efficacement</p>
                    </div>
                    
                    <div class="features-grid">
                        <article class="feature-card">
                            <div class="feature-icon">📋</div>
                            <h3 class="feature-title">Tableaux Kanban</h3>
                            <p class="feature-description">
                                Organisez vos projets avec des colonnes personnalisables et un glisser-déposer intuitif.
                            </p>
                        </article>
                        
                        <article class="feature-card">
                            <div class="feature-icon">👥</div>
                            <h3 class="feature-title">Collaboration</h3>
                            <p class="feature-description">
                                Travaillez en équipe avec des mises à jour en temps réel et une synchronisation automatique.
                            </p>
                        </article>
                        
                        <article class="feature-card">
                            <div class="feature-icon">📱</div>
                            <h3 class="feature-title">Responsive</h3>
                            <p class="feature-description">
                                Accédez à vos projets depuis n'importe quel appareil : ordinateur, tablette ou mobile.
                            </p>
                        </article>
                        
                        <article class="feature-card">
                            <div class="feature-icon">🔄</div>
                            <h3 class="feature-title">Mode hors-ligne</h3>
                            <p class="feature-description">
                                Continuez à travailler sans connexion. Vos modifications se synchronisent automatiquement.
                            </p>
                        </article>

                        <article class="feature-card">
                            <div class="feature-icon">📊</div>
                            <h3 class="feature-title">Statistiques</h3>
                            <p class="feature-description">
                                Suivez les performances avec des rapports détaillés et des métriques avancées.
                            </p>
                        </article>
                        
                        <article class="feature-card">
                            <div class="feature-icon">🔒</div>
                            <h3 class="feature-title">Sécurité</h3>
                            <p class="feature-description">
                                Vos données sont protégées avec un chiffrement avancé et des sauvegardes automatiques.
                            </p>
                        </article>
                    </div>
                </section>

                {{-- Section démo --}}
                <section class="demo-section" aria-labelledby="demo-heading">
                    <div class="demo-content">
                        <h2 id="demo-heading" class="section-title">Kanboard en action</h2>
                        <p class="section-subtitle">
                            Découvrez comment Kanboard transforme votre gestion de projets
                        </p>
                        <div class="demo-placeholder">
                            <div class="demo-board">
                                <div class="demo-column">
                                    <h4>À faire</h4>
                                    <div class="demo-card">Nouvelle fonctionnalité</div>
                                    <div class="demo-card">Correction bug</div>
                                </div>
                                <div class="demo-column">
                                    <h4>En cours</h4>
                                    <div class="demo-card">Design interface</div>
                                </div>
                                <div class="demo-column">
                                    <h4>Terminé</h4>
                                    <div class="demo-card">Setup projet</div>
                                    <div class="demo-card">Documentation</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>

        <footer role="contentinfo">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-brand">
                        <img src="{{ asset('images/Kanboard_icon.svg') }}" 
                             alt="Logo Kanboard" 
                             width="40" 
                             height="40" 
                             class="footer-logo">
                        <span class="footer-title">Kanboard</span>
                    </div>
                    <p class="footer-description">
                        La solution moderne pour gérer vos projets avec la méthode Kanban.
                    </p>
                </div>
                
                <nav class="footer-nav" aria-label="Liens utiles">
                    <div class="footer-column">
                        <h4>Produit</h4>
                        <ul>
                            <li><a href="{{ url('/features') }}">Fonctionnalités</a></li>
                            <li><a href="{{ url('/pricing') }}">Tarifs</a></li>
                            <li><a href="{{ url('/help') }}">Aide</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-column">
                        <h4>Légal</h4>
                        <ul>
                            <li><a href="{{ url('/privacy') }}">Confidentialité</a></li>
                            <li><a href="{{ url('/terms') }}">Conditions</a></li>
                            <li><a href="{{ url('/sitemap.xml') }}">Plan du site</a></li>
                        </ul>
                    </div>
                </nav>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Kanboard. Tous droits réservés.</p>
                <div class="footer-social">
                    <a href="#" aria-label="Twitter">🐦</a>
                    <a href="#" aria-label="LinkedIn">💼</a>
                    <a href="#" aria-label="GitHub">💻</a>
                </div>
            </div>
        </footer>

        @if (Route::has('login'))
            <div class="footer-spacer" aria-hidden="true"></div>
        @endif

        {{-- Script pour l'animation des cartes --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Animation des cartes au scroll
                const observer = new IntersectionObserver(function(entries) {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('animate-in');
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });
                
                document.querySelectorAll('.feature-card').forEach(card => {
                    observer.observe(card);
                });
            });
        </script>
    </body>
</html>
