<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    // Nom et signature de la commande
    protected $signature = 'sitemap:generate {--path=public/sitemap.xml : Le chemin où sauvegarder le sitemap}';

    // Description de la commande
    protected $description = 'Génère un fichier sitemap.xml pour l\'application Kanboard';

    // Exécuter la commande
    public function handle(): int
    {
        $this->info('Génération du sitemap en cours...');

        try {
            $urls = $this->generateSitemapUrls();
            $xml = $this->generateSitemapXml($urls);
            
            $path = $this->option('path');
            $fullPath = base_path($path);
            
            // Créer le répertoire si nécessaire
            $directory = dirname($fullPath);
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
            
            File::put($fullPath, $xml);
            
            $this->info("Sitemap généré avec succès dans : {$path}");
            $this->info("Nombre d'URLs incluses : " . count($urls));
            
            // Affichage des URLs incluses
            $this->table(
                ['URL', 'Priorité', 'Fréquence'],
                collect($urls)->map(fn($url) => [
                    'url' => $url['url'],
                    'priority' => $url['priority'],
                    'changefreq' => $url['changefreq']
                ])->toArray()
            );
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("Erreur lors de la génération du sitemap : " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    // Génère la liste des URLs à inclure dans le sitemap
    private function generateSitemapUrls(): array
    {
        $urls = [];
        
        // Page d'accueil - priorité maximale
        $urls[] = [
            'url' => URL::to('/'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0'
        ];

        // Pages publiques d'authentification
        $publicRoutes = [
            'login' => [
                'changefreq' => 'monthly', 
                'priority' => '0.8',
                'description' => 'Page de connexion'
            ],
            'register' => [
                'changefreq' => 'monthly', 
                'priority' => '0.8',
                'description' => 'Page d\'inscription'
            ],
            'password.request' => [
                'changefreq' => 'monthly', 
                'priority' => '0.6',
                'description' => 'Demande de réinitialisation de mot de passe'
            ],
        ];

        foreach ($publicRoutes as $routeName => $options) {
            if (Route::has($routeName)) {
                $urls[] = [
                    'url' => route($routeName),
                    'lastmod' => now()->toAtomString(),
                    'changefreq' => $options['changefreq'],
                    'priority' => $options['priority']
                ];
            }
        }

        // Pages de l'application (nécessitent une authentification)
        $appRoutes = [
            'dashboard' => [
                'changefreq' => 'daily', 
                'priority' => '0.9',
                'description' => 'Tableau de bord principal'
            ],
            'settings.profile' => [
                'changefreq' => 'weekly', 
                'priority' => '0.7',
                'description' => 'Paramètres du profil'
            ],
            'settings.password' => [
                'changefreq' => 'monthly', 
                'priority' => '0.5',
                'description' => 'Changement de mot de passe'
            ],
            'settings.appearance' => [
                'changefreq' => 'monthly', 
                'priority' => '0.5',
                'description' => 'Paramètres d\'apparence'
            ],
        ];

        foreach ($appRoutes as $routeName => $options) {
            if (Route::has($routeName)) {
                $urls[] = [
                    'url' => route($routeName),
                    'lastmod' => now()->toAtomString(),
                    'changefreq' => $options['changefreq'],
                    'priority' => $options['priority']
                ];
            }
        }

        return $urls;
    }

    // Génère le XML du sitemap
    private function generateSitemapXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $xml .= ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"';
        $xml .= ' xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9';
        $xml .= ' http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

        foreach ($urls as $url) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($url['url']) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . "\n";
            $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . "\n";
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';
        
        return $xml;
    }
}
