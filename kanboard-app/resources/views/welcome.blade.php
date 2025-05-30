<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
        @vite(['resources/css/welcome.css'])
    </head>
    <body>
        <header>

            @if (Route::has('login'))
                <nav class="navbar">
                    <div class="navbar-left">
                        <a href="{{ url('/') }}" class="">
                            <img src="{{ asset('images/Kanboard_icon.svg') }}" alt="logo" width="80" class="logo">
                        </a>
                    </div>
                    <ul class="navbar-center">
                        <li>
                            <a href="{{ url('/') }}" class="">
                                Accueil
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/') }}" class="">
                                À propos
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/') }}" class="">
                                Contacts
                            </a>
                        </li>
                    </ul>
                    <div class="navbar-right">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="nav-link">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="nav-link border-t-neutral-700">
                                Log in
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="nav-link btn register-btn">
                                    Register
                                </a>
                            @endif
                        @endauth
                    </div>

                </nav>
            @endif
        </header>
        <div class="main-container">
            <main class="content-wrapper">
                <section class="presentation">
                    <img src="{{ asset('images/Kanboard_logo.svg') }}" alt="logo" width="400" class="logo">
                    <div class="hero-content">
                        <h1 class="hero-title">Organisez. Visualisez. Avancez.</h1>
                        <div class="hero-description">
                            <p>Kanboard, votre tableau de bord inspiré de la méthode Kanban.</p>
                            <p>Glissez, déposez, priorisez… chaque tâche trouve sa place, en solo ou en équipe.</p>
                        </div>
                    </div>
                </section>
            </main>
        </div>
        <footer>    
            <p>© 2025 Kanboard. Tous droits réservés.</p>
        </footer>
        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif
    </body>
</html>
