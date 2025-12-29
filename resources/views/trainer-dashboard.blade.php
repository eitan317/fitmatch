@php
use Illuminate\Support\Facades\Storage;
@endphp
<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>פאנל מאמן - FitMatch</title>
    <link rel="stylesheet" href="/site/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @include('partials.schema-ld')
</head>
<body class="admin-dashboard-body">
    @include('partials.navbar')

    <div class="admin-dashboard-wrapper">
        <!-- Trainer Top Bar -->
        <div class="admin-top-bar">
            <div class="admin-top-bar-content">
                <div class="admin-breadcrumbs">
                    <span class="admin-breadcrumb-item">פאנל מאמן</span>
                    <span class="admin-breadcrumb-separator">/</span>
                    <span class="admin-breadcrumb-item active">לוח בקרה</span>
                </div>
                <div class="admin-user-info">
                    <span class="admin-user-name">{{ $trainer->full_name }}</span>
                    <span class="admin-user-badge">מאמן</span>
                </div>
            </div>
        </div>

        <main class="admin-dashboard-main">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="admin-notification admin-notification-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="admin-notification admin-notification-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Statistics Section -->
            <section class="admin-section">
                <div class="admin-section-header">
                    <div>
                        <h1 class="admin-section-title">סטטיסטיקות הפרופיל</h1>
                        <p class="admin-section-subtitle">נתונים על הפרופיל שלך</p>
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <a href="{{ route('trainer.profile.edit') }}" class="admin-btn admin-btn-primary">
                            <i class="fas fa-edit"></i>
                            ערוך פרופיל
                        </a>
                        <a href="{{ route('trainer.statistics') }}" class="admin-btn admin-btn-secondary">
                            <i class="fas fa-chart-bar"></i>
                            סטטיסטיקות מפורטות
                        </a>
                    </div>
                </div>

                <div class="admin-stats-slider-container" id="trainerStatsSlider">
                    <div class="admin-stats-slider-track">
                        <div class="admin-stat-card">
                            <div class="admin-stat-icon-wrapper" style="background: rgba(0, 217, 255, 0.1);">
                                <i class="fas fa-eye" style="color: var(--primary);"></i>
                            </div>
                            <div class="admin-stat-content">
                                <div class="admin-stat-value">{{ number_format($stats['total_views']) }}</div>
                                <div class="admin-stat-label">סה"כ צפיות</div>
                                <div class="admin-stat-trend">
                                    <i class="fas fa-chart-line"></i>
                                    <span>{{ $stats['views_today'] }} היום, {{ $stats['views_this_month'] }} החודש</span>
                                </div>
                            </div>
                        </div>

                        <div class="admin-stat-card">
                            <div class="admin-stat-icon-wrapper" style="background: rgba(0, 217, 255, 0.1);">
                                <i class="fas fa-star" style="color: var(--primary);"></i>
                            </div>
                            <div class="admin-stat-content">
                                @if($stats['total_reviews'] > 0)
                                    <div class="admin-stat-value">{{ number_format($stats['average_rating'], 1) }}</div>
                                    <div class="admin-stat-label">דירוג ממוצע</div>
                                    <div class="admin-stat-trend">
                                        <i class="fas fa-comments"></i>
                                        <span>מתוך {{ number_format($stats['total_reviews']) }} ביקורות</span>
                                    </div>
                                @else
                                    <div class="admin-stat-value">-</div>
                                    <div class="admin-stat-label">דירוג ממוצע</div>
                                    <div class="admin-stat-trend">
                                        <i class="fas fa-info-circle"></i>
                                        <span>אין ביקורות עדיין</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="admin-stat-card admin-stat-card-success">
                            <div class="admin-stat-icon-wrapper" style="background: rgba(40, 167, 69, 0.15);">
                                <i class="fas fa-thumbs-up" style="color: #28a745;"></i>
                            </div>
                            <div class="admin-stat-content">
                                <div class="admin-stat-value">{{ number_format($stats['positive_reviews']) }}</div>
                                <div class="admin-stat-label">ביקורות חיוביות</div>
                                <div class="admin-stat-trend">
                                    <i class="fas fa-check"></i>
                                    <span>4-5 כוכבים</span>
                                </div>
                            </div>
                        </div>

                        <div class="admin-stat-card admin-stat-card-warning">
                            <div class="admin-stat-icon-wrapper" style="background: rgba(255, 193, 7, 0.15);">
                                <i class="fas fa-thumbs-down" style="color: #ffc107;"></i>
                            </div>
                            <div class="admin-stat-content">
                                <div class="admin-stat-value">{{ number_format($stats['negative_reviews']) }}</div>
                                <div class="admin-stat-label">ביקורות שליליות</div>
                                <div class="admin-stat-trend">
                                    <i class="fas fa-info-circle"></i>
                                    <span>1-2 כוכבים</span>
                                </div>
                            </div>
                        </div>

                        <div class="admin-stat-card">
                            <div class="admin-stat-icon-wrapper" style="background: rgba(0, 217, 255, 0.1);">
                                <i class="fas fa-comments" style="color: var(--primary);"></i>
                            </div>
                            <div class="admin-stat-content">
                                <div class="admin-stat-value">{{ number_format($stats['total_reviews']) }}</div>
                                <div class="admin-stat-label">סה"כ ביקורות</div>
                                <div class="admin-stat-trend">
                                    <i class="fas fa-arrow-up"></i>
                                    <span><a href="{{ route('trainer.reviews') }}" style="color: var(--text-muted); text-decoration: none;">צפה בכל הביקורות</a></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Recent Reviews Section -->
            <section class="admin-section">
                <div class="admin-section-header">
                    <div>
                        <h2 class="admin-section-title">ביקורות אחרונות</h2>
                        <p class="admin-section-subtitle">הביקורות האחרונות שקיבלת</p>
                    </div>
                    <div class="admin-section-badge admin-section-badge-info">
                        <a href="{{ route('trainer.reviews') }}" style="color: inherit; text-decoration: none;">
                            צפה בכל הביקורות
                        </a>
                    </div>
                </div>

                <div class="admin-all-trainers-container">
                    @forelse($recentReviews as $review)
                        <div class="admin-trainer-card">
                            <div class="admin-trainer-card-header">
                                <div class="admin-trainer-identity">
                                    <div class="admin-trainer-avatar admin-trainer-avatar-small">
                                        <div class="admin-trainer-avatar-placeholder">
                                            {{ substr($review->author_name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="admin-trainer-identity-info">
                                        <h3 class="admin-trainer-name">{{ $review->author_name }}</h3>
                                        <div class="admin-trainer-meta">
                                            <span class="admin-trainer-date">
                                                <i class="fas fa-calendar"></i>
                                                {{ $review->created_at->format('d/m/Y') }}
                                            </span>
                                            <span style="color: #ffc107; margin-right: 0.5rem;">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                                @endfor
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="admin-trainer-card-body">
                                <div class="admin-trainer-bio">
                                    {{ $review->text }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="admin-empty-state">
                            <div class="admin-empty-icon">
                                <i class="fas fa-comments"></i>
                            </div>
                            <h3 class="admin-empty-title">אין ביקורות עדיין</h3>
                            <p class="admin-empty-description">עדיין לא קיבלת ביקורות על הפרופיל שלך</p>
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

        // Initialize trainer dashboard sliders
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof initMobileSlider === 'function') {
                initMobileSlider('#trainerStatsSlider', { cardsPerView: 1 });
            }
        });
    </script>
    @include('partials.cookie-consent')
    @include('partials.accessibility-panel')
</body>
</html>

