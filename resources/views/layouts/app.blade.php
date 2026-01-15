<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @include('partials.adsense')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Font Awesome for icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Site CSS for navbar -->
        <link rel="stylesheet" href="/site/style.css?v={{ file_exists(public_path('site/style.css')) ? filemtime(public_path('site/style.css')) : time() }}">

        <!-- Schema.org JSON-LD Structured Data -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@graph": [
                {
                    "@type": "Organization",
                    "name": "FitMatch",
                    "url": "{{ config('app.url') }}",
                    "email": "fitmatchcoil@gmail.com",
                    "telephone": "+972527020113",
                    "areaServed": {
                        "@type": "Country",
                        "name": "Israel"
                    },
                    "sameAs": [
                        "https://www.instagram.com/fitmatch.org.il/"
                    ]
                },
                {
                    "@type": "WebSite",
                    "name": "FitMatch",
                    "url": "{{ config('app.url') }}",
                    "potentialAction": {
                        "@type": "SearchAction",
                        "target": {
                            "@type": "EntryPoint",
                            "urlTemplate": "{{ url('/trainers') }}?search={search_term_string}"
                        },
                        "query-input": "required name=search_term_string"
                    }
                }
            ]
        }
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('partials.navbar')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        @include('partials.cookie-consent')
        @include('partials.accessibility-panel')
        
        <!-- Site JavaScript for navbar toggle -->
        <script src="/site/script.js?v={{ file_exists(public_path('site/script.js')) ? filemtime(public_path('site/script.js')) : time() }}"></script>
        <script>
            // Initialize navbar toggle on page load
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof initNavbarToggle === 'function') {
                    initNavbarToggle();
                }
            });
        </script>
    </body>
</html>
