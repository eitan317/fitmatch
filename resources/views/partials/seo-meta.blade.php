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
    
    $url = $url ?? url()->current();
    $type = $type ?? 'website';
@endphp

<!-- Primary Meta Tags -->
<title>{{ $title }}</title>
<meta name="title" content="{{ $title }}">
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<link rel="canonical" href="{{ $url }}">

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

