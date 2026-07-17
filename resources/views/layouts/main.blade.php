<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('seo.default_title', config('app.name', 'Diskrétne Dnes')))</title>

    @hasSection('meta')
        @yield('meta')
    @else
        <meta name="description" content="{{ config('seo.default_description', 'Diskrétne Dnes - erotické služby a inzeráty') }}">
        <meta property="og:title" content="@yield('title', config('seo.default_title', config('app.name')))" />
        <meta property="og:description" content="{{ config('seo.default_description', '') }}" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="{{ url()->current() }}" />
        <meta property="og:image" content="{{ asset(config('seo.default_image', 'images/uploads/diskretne-dnes-logo-black.png')) }}" />
        <meta property="og:site_name" content="{{ config('app.name', 'Diskrétne Dnes') }}" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="@yield('title', config('seo.default_title', config('app.name')))" />
        <meta name="twitter:description" content="{{ config('seo.default_description', '') }}" />
        <meta name="twitter:image" content="{{ asset(config('seo.default_image', 'images/uploads/diskretne-dnes-logo-black.png')) }}" />
        <link rel="canonical" href="{{ url()->current() }}" />
    @endif
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=2">

    <!-- DNS prefetch for external resources -->
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    
    <!-- Preload critical resources -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" as="style">
    
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet"/>
    <style>[x-cloak] { display: none !important; }</style>
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Dark mode inicializácia pred načítaním stránky
        (function() {
            const darkMode = localStorage.getItem('darkMode') === 'true';
            const htmlElement = document.documentElement;
            
            if (darkMode) {
                htmlElement.classList.add('dark');
            } else {
                htmlElement.classList.remove('dark');
            }
            
            // Debug log
            console.log('Dark mode initialized:', darkMode, 'HTML classes:', htmlElement.className);
        })();

        // Globálna funkcia pre aktualizáciu počtu obľúbených
        window.updateFavoritesCount = function() {
            fetch('/oblubene/count')
                .then(response => response.json())
                .then(data => {
                    // Aktualizácia cez Alpine.js
                    if (window.Alpine && window.Alpine.store) {
                        window.Alpine.store('favorites', { count: data.count });
                    }
                    // Fallback pre priame aktualizovanie
                    const event = new CustomEvent('favorites-updated', { detail: { count: data.count } });
                    window.dispatchEvent(event);
                })
                .catch(error => console.error('Error updating favorites count:', error));
        };
    </script>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-300">
    <x-impersonation-banner />
    <x-header />
    
    <main>
        @yield('content')
    </main>

    <x-footer />
</body>
</html> 