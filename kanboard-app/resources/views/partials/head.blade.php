<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

{{-- CSRF Token --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Inclusion des balises SEO --}}
@include('partials.seo-meta', [
    'title' => $title ?? null,
    'description' => $description ?? null,
    'keywords' => $keywords ?? null,
    'canonicalUrl' => $canonicalUrl ?? null,
    'ogImage' => $ogImage ?? null,
    'noindex' => $noindex ?? false
])

{{-- Favicon --}}
<link rel="icon" type="image/svg+xml" href="{{ asset('images/Kanboard_icon.svg') }}">
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/Kanboard_icon.svg') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">

{{-- Preconnect pour les fonts --}}
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

{{-- DNS prefetch pour optimiser les performances --}}
<link rel="dns-prefetch" href="//fonts.bunny.net">

{{-- Vite Assets --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])

{{-- Flux Appearance (thème clair/sombre) --}}
@fluxAppearance

{{-- Balises de sécurité --}}
<meta http-equiv="X-Content-Type-Options" content="nosniff">
<meta http-equiv="X-Frame-Options" content="DENY">
<meta http-equiv="X-XSS-Protection" content="1; mode=block">
<meta name="referrer" content="strict-origin-when-cross-origin">
