@php
use Illuminate\Support\Facades\Storage;
@endphp
<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>פאנל מנהל - FitMatch</title>
    <link rel="stylesheet" href="/site/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @include('partials.schema-ld')
</head>
<body class="admin-dashboard-body">
    @include('partials.navbar')

    <div class="admin-dashboard-wrapper">
        <!-- Admin Top Bar -->
        <div class="admin-top-bar">
            <div class="admin-top-bar-content">
                <div class="admin-breadcrumbs">
                    <span class="admin-breadcrumb-item">פאנל מנהל</span>
                    <span class="admin-breadcrumb-separator">/</span>
                    <span class="admin-breadcrumb-item active">ניהול מאמנים</span>
                </div>
                <div class="admin-user-info">
                    <span class="admin-user-name">{{ Auth::user()->name }}</span>
                    <span class="admin-user-badge">מנהל מערכת</span>
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

            <!-- Platform Overview Section -->
            <section class="admin-section">
                <div class="admin-section-header">
                    <h1 class="admin-section-title">סקירת פלטפורמה</h1>
                    <p class="admin-section-subtitle">נתונים כלליים על הפלטפורמה</p>
                </div>

                <div class="admin-stats-slider-container" id="adminStatsSlider">
                    <div class="admin-stats-slider-track">
                        <div class="admin-stat-card">
                            <div class="admin-stat-icon-wrapper" style="background: rgba(0, 217, 255, 0.1);">
                                <i class="fas fa-users" style="color: var(--primary);"></i>
                            </div>
                            <div class="admin-stat-content">
                                <div class="admin-stat-value">{{ number_format($stats['total_users']) }}</div>
                                <div class="admin-stat-label">משתמשים רשומים</div>
                                <div class="admin-stat-trend">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>כל המשתמשים במערכת</span>
                                </div>
                            </div>
                        </div>

                        <div class="admin-stat-card">
                            <div class="admin-stat-icon-wrapper" style="background: rgba(0, 217, 255, 0.1);">
                                <i class="fas fa-dumbbell" style="color: var(--primary);"></i>
                            </div>
                            <div class="admin-stat-content">
                                <div class="admin-stat-value">{{ number_format($stats['total_trainers']) }}</div>
                                <div class="admin-stat-label">סה"כ מאמנים</div>
                                <div class="admin-stat-trend">
                                    <i class="fas fa-info-circle"></i>
                                    <span>
                                        @if(isset($stats['total_visible_trainers']) && $stats['total_visible_trainers'] > 0)
                                            {{ $stats['total_visible_trainers'] }} נראים למשתמשים ({{ $stats['active_trainers'] }} פעילים, {{ $stats['trial_trainers'] }} ניסיון)
                                        @elseif($stats['active_trainers'] > 0 || $stats['trial_trainers'] > 0)
                                            {{ $stats['active_trainers'] }} פעילים, {{ $stats['trial_trainers'] }} ניסיון
                                        @else
                                            אין מאמנים פעילים
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="admin-stat-card admin-stat-card-warning">
                            <div class="admin-stat-icon-wrapper" style="background: rgba(255, 193, 7, 0.15);">
                                <i class="fas fa-clock" style="color: #ffc107;"></i>
                            </div>
                            <div class="admin-stat-content">
                                <div class="admin-stat-value">{{ number_format($stats['pending_payment_trainers']) }}</div>
                                <div class="admin-stat-label">ממתינים לתשלום</div>
                                <div class="admin-stat-trend">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>דורש טיפול</span>
                                </div>
                            </div>
                        </div>

                        <div class="admin-stat-card admin-stat-card-success">
                            <div class="admin-stat-icon-wrapper" style="background: rgba(40, 167, 69, 0.15);">
                                <i class="fas fa-check-circle" style="color: #28a745;"></i>
                            </div>
                            <div class="admin-stat-content">
                                <div class="admin-stat-value">{{ number_format($stats['active_trainers']) }}</div>
                                <div class="admin-stat-label">מאמנים פעילים</div>
                                <div class="admin-stat-trend">
                                    <i class="fas fa-check"></i>
                                    <span>פעילים בפלטפורמה</span>
                                </div>
                            </div>
                        </div>

                        <div class="admin-stat-card">
                            <div class="admin-stat-icon-wrapper" style="background: rgba(0, 217, 255, 0.1);">
                                <i class="fas fa-hourglass-half" style="color: var(--primary);"></i>
                            </div>
                            <div class="admin-stat-content">
                                <div class="admin-stat-value">{{ number_format($stats['trial_trainers']) }}</div>
                                <div class="admin-stat-label">בחודש ניסיון</div>
                                <div class="admin-stat-trend">
                                    <i class="fas fa-info-circle"></i>
                                    <span>30 יום חינם</span>
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
                                        <i class="fas fa-chart-line"></i>
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

                        <div class="admin-stat-card">
                            <div class="admin-stat-icon-wrapper" style="background: rgba(0, 217, 255, 0.1);">
                                <i class="fas fa-calendar-week" style="color: var(--primary);"></i>
                            </div>
                            <div class="admin-stat-content">
                                <div class="admin-stat-value">{{ number_format($stats['trainers_this_month']) }}</div>
                                <div class="admin-stat-label">הרשמות החודש</div>
                                <div class="admin-stat-trend">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>{{ $stats['trainers_last_7_days'] }} ב-7 ימים האחרונים</span>
                                </div>
                            </div>
                        </div>

                        <div class="admin-stat-card">
                            <div class="admin-stat-icon-wrapper" style="background: rgba(0, 217, 255, 0.1);">
                                <i class="fas fa-eye" style="color: var(--primary);"></i>
                            </div>
                            <div class="admin-stat-content">
                                <div class="admin-stat-value">{{ number_format($stats['total_page_views']) }}</div>
                                <div class="admin-stat-label">צפיות באתר</div>
                                <div class="admin-stat-trend">
                                    <i class="fas fa-chart-line"></i>
                                    <span>{{ $stats['page_views_today'] }} היום, {{ $stats['page_views_this_month'] }} החודש</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Pending Trainers Section -->
            <section class="admin-section">
                <div class="admin-section-header">
                    <div>
                        <h2 class="admin-section-title">בקשות מאמנים ממתינות</h2>
                        <p class="admin-section-subtitle">בקשות חדשות הדורשות אישור או דחייה</p>
                    </div>
                    <div class="admin-section-badge admin-section-badge-warning">
                        {{ $stats['pending_trainers'] }} ממתינים
                    </div>
                </div>

                <div class="admin-pending-slider-container" id="adminPendingSlider">
                    <div class="admin-pending-slider-track">
                @forelse($pendingTrainers as $trainer)
                    <div class="admin-trainer-card admin-trainer-card-pending">
                        <div class="admin-trainer-card-header">
                            <div class="admin-trainer-identity">
                                <div class="admin-trainer-avatar">
                                    @php
                                        $imageUrl = null;
                                        if ($trainer->profile_image_path) {
                                            // Try multiple URL generation methods
                                            try {
                                                // Method 1: Use Storage::url() (uses APP_URL from config)
                                                $imageUrl = Storage::disk('public')->url($trainer->profile_image_path);
                                            } catch (\Exception $e) {
                                                // Method 2: Fallback to asset() helper
                                                try {
                                                    $imageUrl = asset('storage/' . $trainer->profile_image_path);
                                                } catch (\Exception $e2) {
                                                    // Method 3: Direct URL construction
                                                    $imageUrl = url('storage/' . $trainer->profile_image_path);
                                                }
                                            }
                                        }
                                    @endphp
                                    @if($imageUrl)
                                        <img src="{{ $imageUrl }}" alt="{{ $trainer->full_name }}" loading="lazy" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex'; console.error('Failed to load image:', '{{ $imageUrl }}');">
                                        <div class="admin-trainer-avatar-placeholder" style="display: none;">
                                            {{ substr($trainer->full_name, 0, 1) }}
                                        </div>
                                    @else
                                        <div class="admin-trainer-avatar-placeholder">
                                            {{ substr($trainer->full_name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="admin-trainer-identity-info">
                                    <h3 class="admin-trainer-name">
                                        {{ $trainer->full_name }}
                                        @if($trainer->status === 'pending_payment' || ($trainer->status === 'trial' && $trainer->trial_ends_at && $trainer->trial_ends_at->isPast()))
                                            <span style="color: #dc3545; font-size: 0.85rem; margin-right: 0.5rem; font-weight: normal;">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                לא שילם
                                            </span>
                                        @endif
                                    </h3>
                                    <div class="admin-trainer-meta">
                                        <span class="admin-trainer-location">
                                            <i class="fas fa-map-marker-alt"></i>
                                            {{ $trainer->city }}
                                        </span>
                                        <span class="admin-trainer-date">
                                            <i class="fas fa-calendar"></i>
                                            {{ $trainer->created_at->format('d/m/Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="admin-trainer-status-badge admin-trainer-status-pending">
                                <i class="fas fa-clock"></i>
                                ממתין לאישור
                            </div>
                        </div>

                        <div class="admin-trainer-card-body">
                            <div class="admin-trainer-info-grid">
                                <div class="admin-trainer-info-group">
                                    <div class="admin-info-label">
                                        <i class="fas fa-user"></i>
                                        פרטים אישיים
                                    </div>
                                    <div class="admin-info-content">
                                        @if($trainer->age)
                                            <div class="admin-info-item">
                                                <span class="admin-info-key">גיל:</span>
                                                <span class="admin-info-value">{{ $trainer->age }} שנים</span>
                                            </div>
                                        @endif
                                        @if($trainer->phone)
                                            <div class="admin-info-item">
                                                <span class="admin-info-key">טלפון:</span>
                                                <span class="admin-info-value">
                                                    <a href="tel:{{ $trainer->phone }}" class="admin-link">{{ $trainer->phone }}</a>
                                                </span>
                                            </div>
                                        @endif
                                        @if($trainer->owner_email)
                                            <div class="admin-info-item">
                                                <span class="admin-info-key">אימייל:</span>
                                                <span class="admin-info-value">
                                                    <a href="mailto:{{ $trainer->owner_email }}" class="admin-link">{{ $trainer->owner_email }}</a>
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="admin-trainer-info-group">
                                    <div class="admin-info-label">
                                        <i class="fas fa-briefcase"></i>
                                        פרטים מקצועיים
                                    </div>
                                    <div class="admin-info-content">
                                        @if($trainer->experience_years)
                                            <div class="admin-info-item">
                                                <span class="admin-info-key">ניסיון:</span>
                                                <span class="admin-info-value">{{ $trainer->experience_years }} שנים</span>
                                            </div>
                                        @endif
                                        @if($trainer->main_specialization)
                                            <div class="admin-info-item">
                                                <span class="admin-info-key">התמחות עיקרית:</span>
                                                <span class="admin-info-value">{{ $trainer->main_specialization }}</span>
                                            </div>
                                        @endif
                                        @if($trainer->price_per_session)
                                            <div class="admin-info-item">
                                                <span class="admin-info-key">מחיר לשעה:</span>
                                                <span class="admin-info-value admin-info-value-highlight">{{ number_format($trainer->price_per_session) }} ₪</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($trainer->training_types && count($trainer->training_types) > 0)
                                    <div class="admin-trainer-info-group admin-trainer-info-group-full">
                                        <div class="admin-info-label">
                                            <i class="fas fa-dumbbell"></i>
                                            סוגי אימונים
                                        </div>
                                        <div class="admin-training-types">
                                            @foreach($trainer->getTrainingTypesWithLabels() as $type)
                                                <span class="admin-training-type-badge">{{ $type['label'] }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($trainer->instagram || $trainer->tiktok)
                                    <div class="admin-trainer-info-group">
                                        <div class="admin-info-label">
                                            <i class="fas fa-share-alt"></i>
                                            רשתות חברתיות
                                        </div>
                                        <div class="admin-info-content">
                                            @if($trainer->instagram)
                                                <div class="admin-info-item">
                                                    <span class="admin-info-key">Instagram:</span>
                                                    <span class="admin-info-value">
                                                        <a href="https://instagram.com/{{ $trainer->instagram }}" target="_blank" class="admin-link">{{ $trainer->instagram }}</a>
                                                    </span>
                                                </div>
                                            @endif
                                            @if($trainer->tiktok)
                                                <div class="admin-info-item">
                                                    <span class="admin-info-key">TikTok:</span>
                                                    <span class="admin-info-value">
                                                        <a href="https://tiktok.com/@{{ $trainer->tiktok }}" target="_blank" class="admin-link">{{ $trainer->tiktok }}</a>
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if($trainer->bio)
                                    <div class="admin-trainer-info-group admin-trainer-info-group-full">
                                        <div class="admin-info-label">
                                            <i class="fas fa-file-alt"></i>
                                            תיאור מקצועי
                                        </div>
                                        <div class="admin-trainer-bio">
                                            {{ $trainer->bio }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="admin-trainer-card-footer">
                            <div class="admin-trainer-actions">
                                <form action="{{ route('admin.trainers.approve', $trainer) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="admin-btn admin-btn-primary" onclick="return confirm('האם אתה בטוח שברצונך לאשר את המאמן?')">
                                        <i class="fas fa-check"></i>
                                        אשר מאמן
                                    </button>
                                </form>
                                <form action="{{ route('admin.trainers.reject', $trainer) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="admin-btn admin-btn-danger" onclick="return confirm('האם אתה בטוח שברצונך לדחות את המאמן? פעולה זו תמחק את הבקשה!')">
                                        <i class="fas fa-times"></i>
                                        דחה בקשה
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="admin-empty-state">
                        <div class="admin-empty-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h3 class="admin-empty-title">אין בקשות ממתינות</h3>
                        <p class="admin-empty-description">כל הבקשות טופלו או שאין בקשות חדשות</p>
                    </div>
                @endforelse
                    </div>
                </div>
            </section>

            <!-- Active Trainers Section -->
            <section class="admin-section">
                <div class="admin-section-header">
                    <div>
                        <h2 class="admin-section-title">מאמנים פעילים ({{ count($activeTrainers) }})</h2>
                        <p class="admin-section-subtitle">מאמנים פעילים בפלטפורמה</p>
                    </div>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <div class="admin-section-badge admin-section-badge-success">
                            {{ $stats['active_trainers'] }} פעילים
                        </div>
                        <form action="{{ route('admin.trainers.cleanup-all') }}" method="POST" style="display: inline;" onsubmit="return confirm('⚠️ אתה בטוח שברצונך למחוק את כל המאמנים הפעילים והניסיון? פעולה זו לא ניתנת לביטול!');">
                            @csrf
                            <button type="submit" class="admin-btn admin-btn-danger" style="white-space: nowrap;">
                                <i class="fas fa-trash-alt"></i>
                                מחק הכל
                            </button>
                        </form>
                    </div>
                </div>

                <div class="admin-active-slider-container" id="adminActiveSlider">
                    <div class="admin-active-slider-track">
                    @forelse($activeTrainers as $trainer)
                        <div class="admin-trainer-card admin-trainer-card-approved">
                            <div class="admin-trainer-card-header">
                                <div class="admin-trainer-identity">
                                    <div class="admin-trainer-avatar admin-trainer-avatar-small">
                                        @php
                                            $imageUrl = null;
                                            if ($trainer->profile_image_path) {
                                                // Try multiple URL generation methods
                                                try {
                                                    // Method 1: Use Storage::url() (uses APP_URL from config)
                                                    $imageUrl = Storage::disk('public')->url($trainer->profile_image_path);
                                                } catch (\Exception $e) {
                                                    // Method 2: Fallback to asset() helper
                                                    try {
                                                        $imageUrl = asset('storage/' . $trainer->profile_image_path);
                                                    } catch (\Exception $e2) {
                                                        // Method 3: Direct URL construction
                                                        $imageUrl = url('storage/' . $trainer->profile_image_path);
                                                    }
                                                }
                                            }
                                        @endphp
                                        @if($imageUrl)
                                            <img src="{{ $imageUrl }}" alt="{{ $trainer->full_name }}" loading="lazy" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex'; console.error('Failed to load image:', '{{ $imageUrl }}');">
                                            <div class="admin-trainer-avatar-placeholder" style="display: none;">
                                                {{ substr($trainer->full_name, 0, 1) }}
                                            </div>
                                        @else
                                            <div class="admin-trainer-avatar-placeholder">
                                                {{ substr($trainer->full_name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="admin-trainer-identity-info">
                                        <h3 class="admin-trainer-name">{{ $trainer->full_name }}</h3>
                                        <div class="admin-trainer-meta">
                                            <span class="admin-trainer-location">
                                                <i class="fas fa-map-marker-alt"></i>
                                                {{ $trainer->city }}
                                            </span>
                                            <span class="admin-trainer-date">
                                                <i class="fas fa-calendar"></i>
                                                נרשם ב-{{ $trainer->created_at->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="admin-trainer-status-badge admin-trainer-status-approved">
                                    <i class="fas fa-check-circle"></i>
                                    פעיל
                                </div>
                            </div>

                            <div class="admin-trainer-card-body">
                                <div class="admin-trainer-quick-info">
                                    @if($trainer->main_specialization)
                                        <div class="admin-quick-info-item">
                                            <i class="fas fa-briefcase"></i>
                                            <span>{{ $trainer->main_specialization }}</span>
                                        </div>
                                    @endif
                                    @if($trainer->experience_years)
                                        <div class="admin-quick-info-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>{{ $trainer->experience_years }} שנות ניסיון</span>
                                        </div>
                                    @endif
                                    @if($trainer->average_rating)
                                        <div class="admin-quick-info-item">
                                            <i class="fas fa-star"></i>
                                            <span>{{ number_format($trainer->average_rating, 1) }} ({{ $trainer->reviews->count() }} ביקורות)</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="admin-trainer-card-footer">
                                <div class="admin-trainer-actions">
                                    <a href="{{ route('trainers.show', $trainer) }}" target="_blank" class="admin-btn admin-btn-secondary">
                                        <i class="fas fa-external-link-alt"></i>
                                        צפה בפרופיל
                                    </a>
                                    <button type="button" class="admin-btn admin-btn-danger" onclick="openDeleteModal({{ $trainer->id }}, '{{ addslashes($trainer->full_name) }}')">
                                        <i class="fas fa-trash-alt"></i>
                                        מחק מאמן
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="admin-empty-state">
                            <div class="admin-empty-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3 class="admin-empty-title">אין מאמנים פעילים</h3>
                            <p class="admin-empty-description">עדיין לא יש מאמנים פעילים בפלטפורמה</p>
                        </div>
                    @endforelse
                    </div>
                </div>
            </section>

            <!-- All Trainers Section -->
            <section class="admin-section">
                <div class="admin-section-header">
                    <div>
                        <h2 class="admin-section-title">כל המאמנים ({{ count($allTrainers) }})</h2>
                        <p class="admin-section-subtitle">כל המאמנים במערכת ללא פילטרים</p>
                    </div>
                    <div class="admin-section-badge admin-section-badge-info">
                        {{ $stats['total_trainers'] }} סה"כ
                    </div>
                </div>

                <div class="admin-all-trainers-container">
                    @forelse($allTrainers as $trainer)
                        <div class="admin-trainer-card admin-trainer-card-all">
                            <div class="admin-trainer-card-header">
                                <div class="admin-trainer-identity">
                                    <div class="admin-trainer-avatar">
                                        @php
                                            $imageUrl = null;
                                            if ($trainer->profile_image_path) {
                                                try {
                                                    $imageUrl = Storage::disk('public')->url($trainer->profile_image_path);
                                                } catch (\Exception $e) {
                                                    try {
                                                        $imageUrl = asset('storage/' . $trainer->profile_image_path);
                                                    } catch (\Exception $e2) {
                                                        $imageUrl = url('storage/' . $trainer->profile_image_path);
                                                    }
                                                }
                                            }
                                        @endphp
                                        @if($imageUrl)
                                            <img src="{{ $imageUrl }}" alt="{{ $trainer->full_name }}" loading="lazy" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="admin-trainer-avatar-placeholder" style="display: none;">
                                                    {{ substr($trainer->full_name, 0, 1) }}
                                                </div>
                                            @else
                                                <div class="admin-trainer-avatar-placeholder">
                                                    {{ substr($trainer->full_name, 0, 1) }}
                                                </div>
                                            @endif
                                    </div>
                                    <div class="admin-trainer-identity-info">
                                        <h3 class="admin-trainer-name">{{ $trainer->full_name }}</h3>
                                        <div class="admin-trainer-meta">
                                            <span class="admin-trainer-location">
                                                <i class="fas fa-map-marker-alt"></i>
                                                {{ $trainer->city }}
                                            </span>
                                            <span class="admin-trainer-date">
                                                <i class="fas fa-calendar"></i>
                                                {{ $trainer->created_at->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="admin-trainer-status-badge 
                                    @if($trainer->status === 'active') admin-trainer-status-approved
                                    @elseif($trainer->status === 'trial') admin-trainer-status-trial
                                    @elseif($trainer->status === 'pending') admin-trainer-status-pending
                                    @elseif($trainer->status === 'pending_payment') admin-trainer-status-payment
                                    @elseif($trainer->status === 'blocked') admin-trainer-status-blocked
                                    @else admin-trainer-status-other
                                    @endif">
                                    <i class="fas 
                                        @if($trainer->status === 'active') fa-check-circle
                                        @elseif($trainer->status === 'trial') fa-clock
                                        @elseif($trainer->status === 'pending') fa-hourglass-half
                                        @elseif($trainer->status === 'pending_payment') fa-credit-card
                                        @elseif($trainer->status === 'blocked') fa-ban
                                        @else fa-info-circle
                                        @endif"></i>
                                    @if($trainer->status === 'active')
                                        פעיל
                                    @elseif($trainer->status === 'trial')
                                        ניסיון
                                    @elseif($trainer->status === 'pending')
                                        ממתין לאישור
                                    @elseif($trainer->status === 'pending_payment')
                                        ממתין לתשלום
                                    @elseif($trainer->status === 'blocked')
                                        חסום
                                    @else
                                        {{ $trainer->status }}
                                    @endif
                                    @if(!$trainer->approved_by_admin)
                                        <span style="color: #ffc107; margin-right: 5px;">(לא מאושר)</span>
                                    @endif
                                </div>
                            </div>

                            <div class="admin-trainer-card-body">
                                <div class="admin-trainer-quick-info">
                                    @if($trainer->main_specialization)
                                        <div class="admin-quick-info-item">
                                            <i class="fas fa-briefcase"></i>
                                            <span>{{ $trainer->main_specialization }}</span>
                                        </div>
                                    @endif
                                    @if($trainer->phone)
                                        <div class="admin-quick-info-item">
                                            <i class="fas fa-phone"></i>
                                            <span>{{ $trainer->phone }}</span>
                                        </div>
                                    @endif
                                    @if($trainer->price_per_session)
                                        <div class="admin-quick-info-item">
                                            <i class="fas fa-shekel-sign"></i>
                                            <span>{{ number_format($trainer->price_per_session) }} ₪</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="admin-trainer-card-footer">
                                <div class="admin-trainer-actions">
                                    <a href="{{ route('trainers.show', $trainer) }}" class="admin-btn admin-btn-secondary">
                                        <i class="fas fa-eye"></i>
                                        צפה בפרופיל
                                    </a>
                                    @if($trainer->status === 'pending' && !$trainer->approved_by_admin)
                                        <form action="{{ route('admin.trainers.approve', $trainer) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="admin-btn admin-btn-primary">
                                                <i class="fas fa-check"></i>
                                                אשר
                                            </button>
                                        </form>
                                    @endif
                                    @if($trainer->status === 'pending_payment')
                                        <form action="{{ route('admin.trainers.approve-payment', $trainer) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="admin-btn admin-btn-success">
                                                <i class="fas fa-credit-card"></i>
                                                אשר תשלום
                                            </button>
                                        </form>
                                    @endif
                                    <button type="button" class="admin-btn admin-btn-danger" onclick="openDeleteModal({{ $trainer->id }}, '{{ addslashes($trainer->full_name) }}')">
                                        <i class="fas fa-trash"></i>
                                        מחק
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="admin-empty-state">
                            <div class="admin-empty-icon">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <h3 class="admin-empty-title">אין מאמנים במערכת</h3>
                            <p class="admin-empty-description">עדיין לא נרשמו מאמנים</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </main>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteTrainerModal" class="admin-delete-modal" style="display: none;">
        <div class="admin-delete-modal-overlay" onclick="closeDeleteModal()"></div>
        <div class="admin-delete-modal-content">
            <div class="admin-delete-modal-header">
                <div class="admin-delete-modal-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2 class="admin-delete-modal-title">מחיקת מאמן</h2>
            </div>
            <div class="admin-delete-modal-body">
                <p class="admin-delete-modal-question">
                    האם אתה בטוח שברצונך למחוק את המאמן?
                </p>
                <div class="admin-delete-modal-trainer-name" id="deleteTrainerName"></div>
                <div class="admin-delete-modal-warning">
                    <p class="admin-delete-modal-warning-title">פעולה זו תמחק לצמיתות:</p>
                    <ul class="admin-delete-modal-warning-list">
                        <li>פרופיל המאמן</li>
                        <li>כל הביקורות הקשורות למאמן</li>
                        <li>תמונת הפרופיל</li>
                    </ul>
                    <p class="admin-delete-modal-warning-note">לא ניתן לבטל פעולה זו לאחר ביצועה.</p>
                </div>
            </div>
            <div class="admin-delete-modal-footer">
                <form id="deleteTrainerForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="admin-btn admin-btn-secondary" onclick="closeDeleteModal()">
                        <i class="fas fa-times"></i>
                        ביטול
                    </button>
                    <button type="submit" class="admin-btn admin-btn-danger">
                        <i class="fas fa-trash-alt"></i>
                        מחק לצמיתות
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="/site/script.js"></script>
    <script>
        initTheme && initTheme();
        initNavbarToggle && initNavbarToggle();

        // Delete Modal Functions
        function openDeleteModal(trainerId, trainerName) {
            const modal = document.getElementById('deleteTrainerModal');
            const form = document.getElementById('deleteTrainerForm');
            const nameDisplay = document.getElementById('deleteTrainerName');
            
            form.action = `/admin/trainers/${trainerId}`;
            nameDisplay.textContent = trainerName;
            
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteTrainerModal');
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });

        // Initialize admin sliders
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof initMobileSlider === 'function') {
                initMobileSlider('#adminStatsSlider', { cardsPerView: 1 });
                initMobileSlider('#adminPendingSlider', { cardsPerView: 1 });
                initMobileSlider('#adminActiveSlider', { cardsPerView: 1 });
            }
        });
    </script>
    @include('partials.cookie-consent')
    @include('partials.accessibility-panel')
</body>
</html>
