@php
use Illuminate\Support\Facades\Storage;
@endphp
<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>פרופיל מאמן - {{ $trainer->full_name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/site/style.css">
    @include('partials.schema-ld')
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        <div style="margin-bottom: 20px;">
            <a href="{{ route('trainers.index') }}" class="btn btn-secondary">חזור לרשימת המאמנים</a>
        </div>

        <div class="profile-layout">
            <div class="trainer-profile">
                <div class="trainer-profile-header">
                    <div class="trainer-profile-image-container">
                        @if($trainer->profile_image_path)
                            <img src="{{ Storage::disk('public')->url($trainer->profile_image_path) }}" alt="{{ $trainer->full_name }}" class="trainer-profile-image-large" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="trainer-avatar-large" style="display: none;">{{ substr($trainer->full_name, 0, 1) }}</div>
                        @else
                            <div class="trainer-avatar-large">{{ substr($trainer->full_name, 0, 1) }}</div>
                        @endif
                    </div>
                    <div class="trainer-profile-info">
                        <h1>{{ $trainer->full_name }}</h1>
                        <p class="trainer-location">📍 {{ $trainer->city }}</p>
                        @if($trainer->age)
                            <p class="trainer-experience">גיל: {{ $trainer->age }}</p>
                        @endif
                        @if($trainer->experience_years)
                            <p class="trainer-experience">ניסיון: {{ $trainer->experience_years }} שנים</p>
                        @endif
                        @if($trainer->main_specialization)
                            <p class="trainer-specialization">התמחות: {{ $trainer->main_specialization }}</p>
                        @endif
                        @if($trainer->average_rating)
                            <div class="trainer-rating">
                                <div class="star-row-small">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star {{ $i <= round($trainer->average_rating) ? 'filled' : '' }}">★</span>
                                    @endfor
                                </div>
                                <p class="rating-text">{{ number_format($trainer->average_rating, 1) }} ({{ $trainer->rating_count }} ביקורות)</p>
                            </div>
                        @endif
                        @if($trainer->price_per_session)
                            <div class="price-large">{{ number_format($trainer->price_per_session) }} ₪ לשעה</div>
                        @endif
                        @if($trainer->training_types && count($trainer->training_types) > 0)
                            <div class="trainer-profile-badges">
                                @foreach($trainer->getTrainingTypesWithLabels() as $typeData)
                                    <span class="badge badge-type">{{ $typeData['label'] }}</span>
                                @endforeach
                            </div>
                        @endif
                        @if($trainer->instagram || $trainer->tiktok)
                            <div class="social-links">
                                @if($trainer->instagram)
                                    <a href="https://instagram.com/{{ $trainer->instagram }}" target="_blank" class="social-link">Instagram</a>
                                @endif
                                @if($trainer->tiktok)
                                    <a href="https://tiktok.com/@{{ $trainer->tiktok }}" target="_blank" class="social-link">TikTok</a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                @if($trainer->bio)
                    <div class="trainer-bio">
                        <h3>אודות</h3>
                        <p>{{ $trainer->bio }}</p>
                    </div>
                @endif

                @if($trainer->phone)
                    <div class="contact-buttons">
                        <a href="tel:{{ $trainer->phone }}" class="btn btn-primary">צור קשר</a>
                    </div>
                @endif
            </div>

            <div class="profile-section">
                <h2 class="section-title">ביקורות</h2>

                <div id="reviews-summary">
                    @if($trainer->average_rating)
                        <p>דירוג ממוצע: {{ number_format($trainer->average_rating, 1) }} מתוך 5 ({{ $trainer->rating_count }} ביקורות)</p>
                    @else
                        <p>אין ביקורות עדיין</p>
                    @endif
                </div>

                <div id="reviews-list" class="reviews-list">
                    @forelse($trainer->reviews as $review)
                        <div class="review-card">
                            <div class="review-header">
                                <span class="review-author">{{ $review->author_name }}</span>
                                <span class="review-date">{{ $review->created_at->format('d/m/Y') }}</span>
                            </div>
                            <div class="review-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="star {{ $i <= $review->rating ? 'filled' : '' }}">★</span>
                                @endfor
                            </div>
                            <p class="review-text">{{ $review->text }}</p>
                        </div>
                    @empty
                        <p>אין ביקורות עדיין</p>
                    @endforelse
                </div>

                @auth
                <div class="review-form">
                    <h3>הוסף ביקורת</h3>
                    <form action="{{ route('reviews.store', $trainer) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="author_name">שם:</label>
                            <input type="text" id="author_name" name="author_name" required>
                        </div>

                        <div class="form-group">
                            <label for="rating">דירוג:</label>
                            <select id="rating" name="rating" required>
                                <option value="">בחר דירוג</option>
                                <option value="5">⭐⭐⭐⭐⭐</option>
                                <option value="4">⭐⭐⭐⭐</option>
                                <option value="3">⭐⭐⭐</option>
                                <option value="2">⭐⭐</option>
                                <option value="1">⭐</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="text">ביקורת:</label>
                            <textarea id="text" name="text" rows="3" placeholder="כתוב כאן מה דעתך על המאמן..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">שלח ביקורת</button>
                        @if(session('success'))
                            <p style="color: green; margin-top: 10px;">{{ session('success') }}</p>
                        @endif
                        @if($errors->any())
                            <div style="color: red; margin-top: 10px;">
                                @foreach($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                    </form>
                </div>
                @else
                <div class="review-form" style="background: rgba(148, 163, 184, 0.1); padding: 1.5rem; border-radius: 12px; text-align: center;">
                    <h3>הוסף ביקורת</h3>
                    <p style="margin-bottom: 1rem;">על מנת לכתוב ביקורת, אנא <a href="{{ route('login') }}" style="color: var(--primary); text-decoration: underline;">התחבר</a> או <a href="{{ route('register') }}" style="color: var(--primary); text-decoration: underline;">הירשם</a></p>
                </div>
                @endauth
            </div>
        </div>
    </main>

    @include('partials.footer')
    @include('partials.cookie-consent')
    @include('partials.accessibility-panel')

    <script src="/site/script.js"></script>
    <script>
        initTheme && initTheme();
        initNavbarToggle && initNavbarToggle();
    </script>
</body>
</html>
