@php
use Illuminate\Support\Facades\Storage;
@endphp
<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>עריכת מאמן - פאנל מנהל</title>
    @include('partials.adsense-verification')
    @include('partials.adsense')
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
                    <a href="{{ route('admin.trainers.index') }}" class="admin-breadcrumb-item" style="text-decoration: none; color: var(--text-muted);">פאנל מנהל</a>
                    <span class="admin-breadcrumb-separator">/</span>
                    <span class="admin-breadcrumb-item active">עריכת מאמן</span>
                </div>
                <div class="admin-user-info">
                    <span class="admin-user-name">{{ Auth::user()->name }}</span>
                    <span class="admin-user-badge">מנהל מערכת</span>
                </div>
            </div>
        </div>

        <main class="admin-dashboard-main">
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

            <section class="admin-section">
                <div class="admin-section-header">
                    <div>
                        <h1 class="admin-section-title">עריכת מאמן: {{ $trainer->full_name }}</h1>
                        <p class="admin-section-subtitle">עדכן את פרטי המאמן</p>
                    </div>
                </div>

                <form action="{{ route('admin.trainers.update', $trainer) }}" method="POST" enctype="multipart/form-data" class="form-container">
                    @csrf

                    <!-- Trainer Status -->
                    <div class="admin-trainer-card" style="margin-bottom: 2rem;">
                        <div class="admin-trainer-card-header">
                            <h3 style="color: var(--text-main); margin: 0;">סטטוס מאמן</h3>
                        </div>
                        <div class="admin-trainer-card-body">
                            <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                                <span style="color: var(--text-muted);">סטטוס נוכחי: <strong style="color: var(--text-main);">{{ $trainer->status }}</strong></span>
                                @if($trainer->status !== 'blocked')
                                    <form action="{{ route('admin.trainers.block', $trainer) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="admin-btn admin-btn-danger" onclick="return confirm('האם אתה בטוח שברצונך לחסום את המאמן?');">
                                            <i class="fas fa-ban"></i> חסום מאמן
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.trainers.unblock', $trainer) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="admin-btn admin-btn-success">
                                            <i class="fas fa-unlock"></i> שחרר מאמן
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Subscription Plan -->
                    <div class="admin-trainer-card" style="margin-bottom: 2rem;">
                        <div class="admin-trainer-card-header">
                            <h3 style="color: var(--text-main); margin: 0;">מנוי</h3>
                        </div>
                        <div class="admin-trainer-card-body">
                            <div class="form-group">
                                <label for="subscription_plan_id">תכנית מנוי</label>
                                <select name="subscription_plan_id" id="subscription_plan_id" class="form-control">
                                    <option value="">ללא מנוי</option>
                                    @foreach(\App\Models\SubscriptionPlan::all() as $plan)
                                        <option value="{{ $plan->id }}" {{ $trainer->subscription_plan_id == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->name }} ({{ $plan->max_training_types ?? 'ללא הגבלה' }} סוגי אימונים)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Details -->
                    <div class="admin-trainer-card" style="margin-bottom: 2rem;">
                        <div class="admin-trainer-card-header">
                            <h3 style="color: var(--text-main); margin: 0;">פרטים אישיים</h3>
                        </div>
                        <div class="admin-trainer-card-body">
                            <div class="form-group">
                                <label for="full_name">שם מלא *</label>
                                <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $trainer->full_name) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="city">עיר *</label>
                                <input type="text" id="city" name="city" value="{{ old('city', $trainer->city) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="phone">טלפון</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone', $trainer->phone) }}" placeholder="050-1234567">
                            </div>

                            <div class="form-group">
                                <label for="age">גיל</label>
                                <input type="number" id="age" name="age" min="18" max="120" value="{{ old('age', $trainer->age) }}">
                            </div>

                            <div class="form-group">
                                <label for="experience_years">שנות ניסיון</label>
                                <input type="number" id="experience_years" name="experience_years" min="0" max="60" value="{{ old('experience_years', $trainer->experience_years) }}">
                            </div>

                            <div class="form-group">
                                <label for="main_specialization">התמחות עיקרית</label>
                                <input type="text" id="main_specialization" name="main_specialization" value="{{ old('main_specialization', $trainer->main_specialization) }}">
                            </div>

                            <div class="form-group">
                                <label for="price_per_session">מחיר לאימון בודד (ש"ח)</label>
                                <input type="number" id="price_per_session" name="price_per_session" min="0" value="{{ old('price_per_session', $trainer->price_per_session) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Training Types -->
                    <div class="admin-trainer-card" style="margin-bottom: 2rem;">
                        <div class="admin-trainer-card-header">
                            <h3 style="color: var(--text-main); margin: 0;">סוגי אימונים</h3>
                        </div>
                        <div class="admin-trainer-card-body">
                            @php
                                $selectedTypes = old('training_types', $trainer->training_types ?? []);
                            @endphp
                            <div class="training-types-list">
                                <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="strength_training" {{ in_array('strength_training', $selectedTypes) ? 'checked' : '' }}><span>אימוני כוח</span></label>
                                <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="gym_basic" {{ in_array('gym_basic', $selectedTypes) ? 'checked' : '' }}><span>חדר כושר בסיסי</span></label>
                                <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="hypertrophy" {{ in_array('hypertrophy', $selectedTypes) ? 'checked' : '' }}><span>מסת שריר</span></label>
                                <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="weightloss" {{ in_array('weightloss', $selectedTypes) ? 'checked' : '' }}><span>חיטוב / ירידה במשקל</span></label>
                                <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="hiit" {{ in_array('hiit', $selectedTypes) ? 'checked' : '' }}><span>אימוני HIIT</span></label>
                                <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="online" {{ in_array('online', $selectedTypes) ? 'checked' : '' }}><span>אימונים אונליין (זום)</span></label>
                                <!-- Add more training types as needed -->
                            </div>
                        </div>
                    </div>

                    <!-- Social Media & Bio -->
                    <div class="admin-trainer-card" style="margin-bottom: 2rem;">
                        <div class="admin-trainer-card-header">
                            <h3 style="color: var(--text-main); margin: 0;">פרטים נוספים</h3>
                        </div>
                        <div class="admin-trainer-card-body">
                            <div class="form-group">
                                <label for="instagram">אינסטגרם</label>
                                <input type="text" id="instagram" name="instagram" value="{{ old('instagram', $trainer->instagram) }}">
                            </div>

                            <div class="form-group">
                                <label for="tiktok">טיקטוק</label>
                                <input type="text" id="tiktok" name="tiktok" value="{{ old('tiktok', $trainer->tiktok) }}">
                            </div>

                            <div class="form-group">
                                <label for="bio">תיאור קצר</label>
                                <textarea id="bio" name="bio" rows="4">{{ old('bio', $trainer->bio) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Images Management -->
                    <div class="admin-trainer-card" style="margin-bottom: 2rem;">
                        <div class="admin-trainer-card-header">
                            <h3 style="color: var(--text-main); margin: 0;">ניהול תמונות</h3>
                        </div>
                        <div class="admin-trainer-card-body">
                            @php
                                $imageUrl = null;
                                if ($trainer->profile_image_path) {
                                    $imageUrl = Storage::disk('public')->url($trainer->profile_image_path);
                                    if (!str_starts_with($imageUrl, 'http')) {
                                        $imageUrl = url($imageUrl);
                                    }
                                }
                            @endphp
                            
                            @if($imageUrl)
                                <div style="margin-bottom: 1rem;">
                                    <img src="{{ $imageUrl }}" alt="תמונת פרופיל" style="max-width: 200px; border-radius: 8px; border: 2px solid var(--primary);">
                                </div>
                            @else
                                <p style="color: var(--text-muted);">אין תמונת פרופיל למאמן זה</p>
                            @endif

                            <div class="form-group">
                                <label for="new_image">הוסף תמונת פרופיל חדשה</label>
                                <input type="file" id="new_image" name="new_image" accept="image/*">
                                <small style="color: var(--text-muted);">התמונה תישמר כתמונת הפרופיל</small>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="fas fa-save"></i> שמור שינויים
                        </button>
                        <a href="{{ route('admin.trainers.index') }}" class="admin-btn admin-btn-secondary">
                            <i class="fas fa-arrow-right"></i> חזור לרשימה
                        </a>
                    </div>
                </form>
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
