<?php

namespace App\Providers;

use App\Console\Commands\GenerateSitemap;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class SeoServiceProvider extends ServiceProvider
{
    // Register services.
    public function register(): void
    {
        // Enregistrement de la commande de génération du sitemap
        $this->commands([
            GenerateSitemap::class,
        ]);
    }

    // Bootstrap services.
    public function boot(): void
    {
        // Partager les données SEO communes avec toutes les vues
        View::composer('*', function ($view) {
            $seoData = [
                'site_name' => config('app.name', 'Kanboard'),
                'site_description' => 'Kanboard - Votre nouvelle application de gestion de projet préférée. Organisez vos tâches avec un système Kanban moderne et efficace.',
                'site_keywords' => 'kanboard, kanban, gestion projet, tâches, organisation, productivité, collaboration, Laravel',
                'site_url' => config('app.url'),
                'site_image' => asset('images/kanboard-og.png'), // Image Open Graph
            ];
            
            $view->with('seoData', $seoData);
        });

        // Composer spécifique pour la page d'accueil
        View::composer('welcome', function ($view) {
            $homePageSeo = [
                'title' => 'Kanboard - Application de gestion de projet Kanban',
                'description' => 'Découvrez Kanboard, l\'application de gestion de projet qui révolutionne votre façon de travailler. Interface Kanban intuitive, collaboration en temps réel, et bien plus.',
                'canonical_url' => route('home'),
            ];
            
            $view->with('homePageSeo', $homePageSeo);
        });
    }
}
