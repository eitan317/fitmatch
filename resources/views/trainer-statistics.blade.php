@php
use Illuminate\Support\Facades\Storage;
@endphp
<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>סטטיסטיקות מפורטות - פאנל מאמן</title>
    <link rel="stylesheet" href="/site/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @include('partials.schema-ld')
</head>
<body class="admin-dashboard-body">
    @include('partials.navbar')

    <div class="admin-dashboard-wrapper">
        <div class="admin-top-bar">
            <div class="admin-top-bar-content">
                <div class="admin-breadcrumbs">
                    <a href="{{ route('trainer.dashboard') }}" class="admin-breadcrumb-item" style="text-decoration: none; color: var(--text-muted);">פאנל מאמן</a>
                    <span class="admin-breadcrumb-separator">/</span>
                    <span class="admin-breadcrumb-item active">סטטיסטיקות מפורטות</span>
                </div>
                <div class="admin-user-info">
                    <span class="admin-user-name">{{ $trainer->full_name }}</span>
                    <span class="admin-user-badge">מאמן</span>
                </div>
            </div>
        </div>

        <main class="admin-dashboard-main">
            <!-- Detailed Statistics -->
            <section class="admin-section">
                <div class="admin-section-header">
                    <div>
                        <h1 class="admin-section-title">סטטיסטיקות מפורטות</h1>
                        <p class="admin-section-subtitle">נתונים מפורטים על הפרופיל שלך</p>
                    </div>
                </div>

                <div class="admin-stats-slider-container" id="detailedStatsSlider">
                    <div class="admin-stats-slider-track">
                        <div class="admin-stat-card">
                            <div class="admin-stat-icon-wrapper" style="background: rgba(0, 217, 255, 0.1);">
                                <i class="fas fa-eye" style="color: var(--primary);"></i>
                            </div>
                            <div class="admin-stat-content">
                                <div class="admin-stat-value">{{ number_format($stats['total_views']) }}</div>
                                <div class="admin-stat-label">סה"כ צפיות</div>
                                <div class="admin-stat-trend">
                                    <i class="fas fa-info-circle"></i>
                                    <span>כל הזמנים</span>
                                </div>
                            </div>
                        </div>

                        <div class="admin-stat-card">
                            <div class="admin-stat-icon-wrapper" style="background: rgba(0, 217, 255, 0.1);">
                                <i class="fas fa-calendar-day" style="color: var(--primary);"></i>
                            </div>
                            <div class="admin-stat-content">
                                <div class="admin-stat-value">{{ number_format($stats['views_today']) }}</div>
                                <div class="admin-stat-label">צפיות היום</div>
                                <div class="admin-stat-trend">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>היום</span>
                                </div>
                            </div>
                        </div>

                        <div class="admin-stat-card">
                            <div class="admin-stat-icon-wrapper" style="background: rgba(0, 217, 255, 0.1);">
                                <i class="fas fa-calendar-week" style="color: var(--primary);"></i>
                            </div>
                            <div class="admin-stat-content">
                                <div class="admin-stat-value">{{ number_format($stats['views_this_week']) }}</div>
                                <div class="admin-stat-label">צפיות השבוע</div>
                                <div class="admin-stat-trend">
                                    <i class="fas fa-chart-line"></i>
                                    <span>7 ימים אחרונים</span>
                                </div>
                            </div>
                        </div>

                        <div class="admin-stat-card">
                            <div class="admin-stat-icon-wrapper" style="background: rgba(0, 217, 255, 0.1);">
                                <i class="fas fa-calendar-alt" style="color: var(--primary);"></i>
                            </div>
                            <div class="admin-stat-content">
                                <div class="admin-stat-value">{{ number_format($stats['views_this_month']) }}</div>
                                <div class="admin-stat-label">צפיות החודש</div>
                                <div class="admin-stat-trend">
                                    <i class="fas fa-chart-line"></i>
                                    <span>החודש הנוכחי</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Rating Distribution -->
            <section class="admin-section">
                <div class="admin-section-header">
                    <h2 class="admin-section-title">התפלגות דירוגים</h2>
                </div>

                <div class="admin-all-trainers-container">
                    @foreach([5, 4, 3, 2, 1] as $rating)
                        <div class="admin-trainer-card">
                            <div class="admin-trainer-card-body">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                                    <div>
                                        <h3 style="color: var(--text-main); margin-bottom: 0.5rem;">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= $rating ? '' : '-o' }}" style="color: {{ $i <= $rating ? '#ffc107' : '#6c757d' }};"></i>
                                            @endfor
                                        </h3>
                                        <p style="color: var(--text-muted); margin: 0;">{{ $stats['rating_distribution'][$rating] }} ביקורות</p>
                                    </div>
                                    <div style="font-size: 2rem; font-weight: 700; color: var(--primary);">
                                        {{ $stats['total_reviews'] > 0 ? number_format(($stats['rating_distribution'][$rating] / $stats['total_reviews']) * 100, 1) : 0 }}%
                                    </div>
                                </div>
                                <div style="background: rgba(74, 158, 255, 0.1); height: 8px; border-radius: 4px; overflow: hidden;">
                                    <div style="background: var(--primary); height: 100%; width: {{ $stats['total_reviews'] > 0 ? ($stats['rating_distribution'][$rating] / $stats['total_reviews']) * 100 : 0 }}%; transition: width 0.3s ease;"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- Recent Views -->
            <section class="admin-section">
                <div class="admin-section-header">
                    <h2 class="admin-section-title">צפיות אחרונות</h2>
                    <p class="admin-section-subtitle">10 הצפיות האחרונות בפרופיל שלך</p>
                </div>

                <div class="admin-all-trainers-container">
                    @forelse($recentViews as $view)
                        <div class="admin-trainer-card">
                            <div class="admin-trainer-card-body">
                                <div class="admin-trainer-quick-info">
                                    <div class="admin-quick-info-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>{{ $view->viewed_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @if($view->user)
                                        <div class="admin-quick-info-item">
                                            <i class="fas fa-user"></i>
                                            <span>{{ $view->user->name }}</span>
                                        </div>
                                    @else
                                        <div class="admin-quick-info-item">
                                            <i class="fas fa-user-secret"></i>
                                            <span>אורח</span>
                                        </div>
                                    @endif
                                    @if($view->ip_address)
                                        <div class="admin-quick-info-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span>{{ $view->ip_address }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="admin-empty-state">
                            <div class="admin-empty-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <h3 class="admin-empty-title">אין צפיות עדיין</h3>
                            <p class="admin-empty-description">עדיין לא היו צפיות בפרופיל שלך</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </main>
    </div>

    <script src="/site/script.js"></script>
    <script>
        initTheme && initTheme();
        initNavbarToggle && initNavbarToggle();

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof initMobileSlider === 'function') {
                initMobileSlider('#detailedStatsSlider', { cardsPerView: 1 });
            }
        });
    </script>
    @include('partials.cookie-consent')
    @include('partials.accessibility-panel')
</body>
</html>

