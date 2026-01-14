@php
    $title = $title ?? 'מצא מאמן כושר מקצועי - FitMatch';
    $description = $description ?? 'מצא מאמן כושר אישי מקצועי בקלות. מאות מאמנים מאומתים בכל סוגי האימונים. חיפוש לפי עיר, סוג אימון ומחיר. התחל עוד היום!';
    $keywords = $keywords ?? 'מאמן כושר, אימון אישי, מאמן כושר אישי, מאמני כושר, מצא מאמן כושר, אימון בית, מאמן כושר תל אביב';
    
    // Fallback לתמונה - נסה hero-trainers.jpg, אחרת logo.png, אחרת favicon.ico
    if (!isset($image)) {
        if (file_exists(public_path('images/hero-trainers.jpg'))) {
            $image = asset('images/hero-trainers.jpg');
        } elseif (file_exists(public_path('logo.png'))) {
            $image = asset('logo.png');
        } else {
            $image = asset('favicon.ico');
        }
    }
    
    // ודא שה-URL הוא absolute (מוחלט) - זה חשוב ל-Open Graph!
    $imageUrl = $image;
    if (!str_starts_with($imageUrl, 'http')) {
        $imageUrl = url($imageUrl);
    }
    
    // Generate canonical URL using config('app.url') to ensure it always points to www.fitmatch.org.il
    // This prevents canonical URLs from using old Railway subdomains
    if (!isset($url)) {
        $baseUrl = rtrim(config('app.url'), '/');
        $currentPath = request()->getRequestUri();
        // Remove query string for canonical URL
        $path = parse_url($currentPath, PHP_URL_PATH);
        $url = $baseUrl . $path;
    }
    $type = $type ?? 'website';
    
    // Generate hreflang URLs for all language versions
    $supportedLocales = ['he', 'en', 'ru', 'ar'];
    $baseUrl = rtrim(config('app.url'), '/');
    $currentPath = parse_url($url, PHP_URL_PATH);
    
    // Remove existing language prefix if present
    $pathSegments = explode('/', trim($currentPath, '/'));
    if (!empty($pathSegments[0]) && in_array($pathSegments[0], $supportedLocales)) {
        array_shift($pathSegments);
    }
    $basePath = empty($pathSegments) ? '/' : '/' . implode('/', $pathSegments);
    
    // Generate language URLs
    $hreflangUrls = [];
    foreach ($supportedLocales as $locale) {
        if ($basePath === '/') {
            $hreflangUrls[$locale] = $baseUrl . '/' . $locale . '/';
        } else {
            $hreflangUrls[$locale] = $baseUrl . '/' . $locale . $basePath;
        }
    }
    // Also add backward-compatible URL (without prefix - defaults to Hebrew)
    $hreflangUrls['he-backward'] = $baseUrl . $basePath;
@endphp

<!-- Primary Meta Tags -->
<title>{{ $title }}</title>
<meta name="title" content="{{ $title }}">
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<link rel="canonical" href="{{ $url }}">

<!-- Hreflang Tags for Multi-Language Support -->
@foreach($hreflangUrls as $locale => $langUrl)
    @if($locale === 'he-backward')
        <link rel="alternate" hreflang="he" href="{{ $langUrl }}">
    @else
        <link rel="alternate" hreflang="{{ $locale }}" href="{{ $langUrl }}">
    @endif
@endforeach
<link rel="alternate" hreflang="x-default" href="{{ $hreflangUrls['he'] }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $imageUrl }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="{{ $title }}">
<meta property="og:locale" content="he_IL">
<meta property="og:site_name" content="FitMatch">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $url }}">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $imageUrl }}">
<meta name="twitter:image:alt" content="{{ $title }}">

