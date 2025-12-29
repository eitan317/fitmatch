@php
use Illuminate\Support\Facades\Storage;
@endphp
<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ביקורות - פאנל מאמן</title>
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
                    <span class="admin-breadcrumb-item active">ביקורות</span>
                </div>
                <div class="admin-user-info">
                    <span class="admin-user-name">{{ $trainer->full_name }}</span>
                    <span class="admin-user-badge">מאמן</span>
                </div>
            </div>
        </div>

        <main class="admin-dashboard-main">
            <section class="admin-section">
                <div class="admin-section-header">
                    <div>
                        <h1 class="admin-section-title">כל הביקורות</h1>
                        <p class="admin-section-subtitle">ביקורות שקיבלת על הפרופיל שלך</p>
                    </div>
                </div>

                <!-- Filter by Rating -->
                <div style="margin-bottom: 2rem;">
                    <form method="GET" action="{{ route('trainer.reviews') }}" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                        <label style="color: var(--text-muted);">סינון לפי דירוג:</label>
                        <select name="rating" style="padding: 0.5rem 1rem; border-radius: 8px; background: rgba(30, 41, 59, 0.6); border: 1px solid rgba(74, 158, 255, 0.1); color: var(--text-main);">
                            <option value="">כל הדירוגים</option>
                            <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 כוכבים</option>
                            <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 כוכבים</option>
                            <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 כוכבים</option>
                            <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 כוכבים</option>
                            <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 כוכב</option>
                        </select>
                        <button type="submit" class="admin-btn admin-btn-primary">סנן</button>
                        @if(request('rating'))
                            <a href="{{ route('trainer.reviews') }}" class="admin-btn admin-btn-secondary">נקה סינון</a>
                        @endif
                    </form>
                </div>

                <div class="admin-all-trainers-container">
                    @forelse($reviews as $review)
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
                                                {{ $review->created_at->format('d/m/Y H:i') }}
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
                            <h3 class="admin-empty-title">אין ביקורות</h3>
                            <p class="admin-empty-description">
                                @if(request('rating'))
                                    לא נמצאו ביקורות עם הדירוג שנבחר
                                @else
                                    עדיין לא קיבלת ביקורות על הפרופיל שלך
                                @endif
                            </p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($reviews->hasPages())
                    <div style="margin-top: 2rem; display: flex; justify-content: center;">
                        {{ $reviews->links() }}
                    </div>
                @endif
            </section>
        </main>
    </div>

    <script src="/site/script.js"></script>
    <script>
        initTheme && initTheme();
        initNavbarToggle && initNavbarToggle();
    </script>
    @include('partials.cookie-consent')
    @include('partials.accessibility-panel')
</body>
</html>

