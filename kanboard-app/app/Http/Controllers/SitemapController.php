<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class SitemapController extends Controller
{
    // Génère et retourne le sitemap XML
    public function index(): Response
    {
        $urls = $this->generateSitemapUrls();
        
        $xml = $this->generateSitemapXml($urls);
        
        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

    //Génère la liste des URLs à inclure dans le sitemap
    private function generateSitemapUrls(): array
    {
        $urls = [];
        
        // Page d'accueil
        $urls[] = [
            'url' => URL::to('/'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0'
        ];

        // Pages publiques statiques
        $publicRoutes = [
            'login' => ['changefreq' => 'monthly', 'priority' => '0.8'],
            'register' => ['changefreq' => 'monthly', 'priority' => '0.8'],
            'password.request' => ['changefreq' => 'monthly', 'priority' => '0.6'],
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

        // Pages protégées (accessibles aux utilisateurs connectés)

        $protectedRoutes = [
            'dashboard' => ['changefreq' => 'daily', 'priority' => '0.9'],
            'settings.profile' => ['changefreq' => 'weekly', 'priority' => '0.7'],
            'settings.password' => ['changefreq' => 'monthly', 'priority' => '0.5'],
            'settings.appearance' => ['changefreq' => 'monthly', 'priority' => '0.5'],
        ];

        foreach ($protectedRoutes as $routeName => $options) {
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
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($url['url']) . "</loc>\n";
            $xml .= "    <lastmod>" . $url['lastmod'] . "</lastmod>\n";
            $xml .= "    <changefreq>" . $url['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $url['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
