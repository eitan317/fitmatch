@php
    $baseUrl = config('app.url');
    $appName = config('app.name', 'FitMatch');
    // Check if logo exists, otherwise use placeholder
    $logoUrl = file_exists(public_path('logo.png')) 
        ? $baseUrl . '/logo.png' 
        : ($baseUrl . '/favicon.ico');
    
    // Build JSON-LD structure
    $schemaData = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                'name' => $appName,
                'url' => $baseUrl,
                'logo' => $logoUrl,
                'description' => 'פלטפורמה למציאת מאמני כושר אישיים מקצועיים',
                'email' => 'fitmatchcoil@gmail.com',
                'telephone' => '+972527020113',
                'areaServed' => [
                    '@type' => 'Country',
                    'name' => 'Israel'
                ],
                'sameAs' => [
                    'https://www.instagram.com/fitmatch.org.il/',
                    'https://www.tiktok.com/@fitmatch912'
                ]
            ],
            [
                '@type' => 'WebSite',
                'name' => $appName,
                'url' => $baseUrl,
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => [
                        '@type' => 'EntryPoint',
                        'urlTemplate' => $baseUrl . '/trainers?search={search_term_string}'
                    ],
                    'query-input' => 'required name=search_term_string'
                ]
            ],
            [
                '@type' => 'LocalBusiness',
                '@id' => $baseUrl . '#business',
                'name' => $appName,
                'image' => $logoUrl,
                'url' => $baseUrl,
                'description' => 'פלטפורמה למציאת מאמני כושר אישיים מקצועיים בישראל'
            ]
        ]
    ];
@endphp

<script type="application/ld+json">{!! json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}</script>

