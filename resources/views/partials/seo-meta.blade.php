@php
    $title = $title ?? 'מצא מאמן כושר מקצועי - FitMatch';
    $description = $description ?? 'מצא מאמן כושר אישי מקצועי בקלות. מאות מאמנים מאומתים בכל סוגי האימונים. חיפוש לפי עיר, סוג אימון ומחיר. התחל עוד היום!';
    $keywords = $keywords ?? 'מאמן כושר, אימון אישי, מאמן כושר אישי, מאמני כושר, מצא מאמן כושר, אימון בית, מאמן כושר תל אביב';
    $image = $image ?? asset('images/hero-trainers.jpg');
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
<meta property="og:image" content="{{ $image }}">
<meta property="og:locale" content="he_IL">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{{ $url }}">
<meta property="twitter:title" content="{{ $title }}">
<meta property="twitter:description" content="{{ $description }}">
<meta property="twitter:image" content="{{ $image }}">

