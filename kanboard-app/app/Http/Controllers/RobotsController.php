<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function index(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            '',
            '# Protéger les zones d\'administration',
            'Disallow: /admin/',
            'Disallow: /settings/',
            'Disallow: /dashboard/',
            'Disallow: /api/',
            'Disallow: /profile/',
            '',
            '# Fichiers sensibles',
            'Disallow: /.env',
            'Disallow: /storage/',
            'Disallow: /vendor/',
            '',
            '# Sitemap',
            'Sitemap: ' . url('/sitemap.xml'),
        ];

        $content = implode("\n", $lines);

        return response($content, 200)
            ->header('Content-Type', 'text/plain')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');    }
}
