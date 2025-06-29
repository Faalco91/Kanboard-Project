@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'canonicalUrl' => null,
    'ogImage' => null,
    'noindex' => false
])

@php
    $pageTitle = $title ? $title . ' - ' . $seoData['site_name'] : $seoData['site_name'];
    $pageDescription = $description ?? $seoData['site_description'];
    $pageKeywords = $keywords ?? $seoData['site_keywords'];
    $pageCanonical = $canonicalUrl ?? request()->url();
    $pageOgImage = $ogImage ?? $seoData['site_image'];
@endphp

{{-- Balises META de base --}}
<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDescription }}">
<meta name="keywords" content="{{ $pageKeywords }}">
<meta name="author" content="{{ $seoData['site_name'] }}">

{{-- Balises de contrôle des robots --}}
@if($noindex)
    <meta name="robots" content="noindex, nofollow">
@else
    <meta name="robots" content="index, follow">
@endif

{{-- URL canonique --}}
<link rel="canonical" href="{{ $pageCanonical }}">

{{-- Open Graph (Facebook, LinkedIn, etc.) --}}
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDescription }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ $pageCanonical }}">
<meta property="og:image" content="{{ $pageOgImage }}">
<meta property="og:site_name" content="{{ $seoData['site_name'] }}">

{{-- Twitter Cards --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $pageDescription }}">
<meta name="twitter:image" content="{{ $pageOgImage }}">

{{-- Balises pour mobile --}}
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#1f2937">

{{-- Liens vers sitemap et RSS (optionnel) --}}
<link rel="sitemap" type="application/xml" title="Sitemap" href="{{ route('sitemap') }}">

{{-- Données structurées JSON-LD --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebApplication",
    "name": "{{ $seoData['site_name'] }}",
    "description": "{{ $pageDescription }}",
    "url": "{{ $seoData['site_url'] }}",
    "applicationCategory": "BusinessApplication",
    "operatingSystem": "Web Browser",
    "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "EUR"
    },
    "featureList": [
        "Gestion de projets Kanban",
        "Collaboration en équipe",
        "Interface responsive",
        "Mode hors-ligne",
        "Notifications en temps réel"
    ]
}
</script>
