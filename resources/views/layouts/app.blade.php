
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#230C31">

    @php
        $pageTitle  = $title ?? 'VK Uniformes | Uniformes Profesionales';
        $pageDesc   = $description ?? 'Descubre uniformes profesionales: scrubs médicos, mandiles, gorritos y diseños estampados. Comodidad, calidad y estilo para profesionales de la salud y gastronomía.';
        $pageImage  = $ogImage ?? asset('images/og-cover.png');
        $pageUrl    = url()->current();
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDesc }}">

    {{-- =============================================
         OPEN GRAPH — para WhatsApp, Facebook, etc.
    ============================================== --}}
    <meta property="og:type"        content="website">
    <meta property="og:site_name"   content="VK Uniformes">
    <meta property="og:title"       content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    <meta property="og:image"       content="{{ $pageImage }}">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt"   content="VK Uniformes — Uniformes Profesionales">
    <meta property="og:url"         content="{{ $pageUrl }}">
    <meta property="og:locale"      content="es_ES">

    {{-- =============================================
         TWITTER / X CARD
    ============================================== --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDesc }}">
    <meta name="twitter:image"       content="{{ $pageImage }}">

    {{-- =============================================
         FAVICON COMPLETO
    ============================================== --}}
    {{-- ICO clásico para máxima compatibilidad con navegadores --}}
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    {{-- PNG icons --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16.png') }}">
    {{-- Apple Touch Icon --}}
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    {{-- SVG favicon (Chrome, Firefox, Edge modernos) --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    {{-- Manifest PWA --}}
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    {{-- =============================================
         SEO ADICIONAL & DATOS ESTRUCTURADOS (JSON-LD)
    ============================================== --}}
    <link rel="canonical" href="{{ $pageUrl }}">
    <meta name="robots" content="index, follow">

    <script type="application/ld+json">
    {
        "@php echo '"@context"'; @endphp: "https://schema.org",
        "@type": "Organization",
        "name": "VK Uniformes",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('images/logo-vk.png') }}",
        "image": "{{ asset('images/og-cover.png') }}",
        "description": "Uniformes profesionales médicos, mandiles, gorritos y prendas para el sector salud y gastronomía."
    }
    </script>

    {{-- ESTILOS Y JAVASCRIPT --}}
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    {{-- ESTILOS LIVEWIRE --}}
    @livewireStyles
</head>

<body class="storefront min-h-screen bg-white font-sans text-vk-text antialiased">

    {{-- =========================================
         HEADER PRINCIPAL
    ========================================== --}}
    <a class="store-skip" href="#app">Saltar al contenido</a>
    <div class="vk-scroll-progress" data-scroll-progress aria-hidden="true"></div>
    <button type="button" class="vk-back-top" data-back-top hidden aria-label="Volver al inicio">↑</button>
    <x-store-header />

    {{-- =========================================
         CONTENIDO DINÁMICO LIVEWIRE
    ========================================== --}}
    <div id="app" tabindex="-1" class="min-h-[60vh]">

        {{ $slot }}

    </div>

    {{-- =========================================
         FOOTER
    ========================================== --}}
    <x-store-footer />

    {{-- SCRIPTS LIVEWIRE --}}
    @livewireScripts

</body>
</html>
