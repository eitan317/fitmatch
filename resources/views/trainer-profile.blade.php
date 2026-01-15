@php
use Illuminate\Support\Facades\Storage;
@endphp
<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>פרופיל מאמן - {{ $trainer->full_name }}</title>
    @include('partials.adsense-verification')
    @include('partials.adsense')
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
                            <img src="{{ $imageUrl }}" alt="{{ $trainer->full_name }}" class="trainer-profile-image-large" loading="lazy" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
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
                        
                        <!-- Like Button -->
                        <div class="trainer-like-section" style="margin-top: 1rem; display: flex; align-items: center; gap: 0.75rem;">
                            @auth
                                <button id="likeBtn" class="like-btn {{ $isLiked ?? false ? 'liked' : '' }}" data-trainer-id="{{ $trainer->id }}" style="background: none; border: 2px solid var(--primary); border-radius: 50px; padding: 0.5rem 1rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease; color: var(--text-main);">
                                    <i class="fas fa-heart" style="font-size: 1.2rem; {{ $isLiked ?? false ? 'color: #e74c3c;' : 'color: var(--primary);' }}"></i>
                                    <span id="likeCount" style="font-weight: 600;">{{ $trainer->likes_count ?? 0 }}</span>
                                </button>
                            @else
                                <div class="like-btn-disabled" style="background: rgba(74, 158, 255, 0.1); border: 2px solid var(--primary); border-radius: 50px; padding: 0.5rem 1rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted);">
                                    <i class="fas fa-heart" style="font-size: 1.2rem; color: var(--primary);"></i>
                                    <span style="font-weight: 600;">{{ $trainer->likes_count ?? 0 }}</span>
                                    <small style="font-size: 0.8rem; margin-right: 0.5rem;">(התחבר כדי לעשות לייק)</small>
                                </div>
                            @endauth
                        </div>
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
                        <div class="review-card" data-review-id="{{ $review->id }}">
                            <div class="review-header">
                                <span class="review-author">{{ $review->author_name }}</span>
                                <span class="review-date">{{ $review->created_at->format('d/m/Y') }}</span>
                            </div>
                            <div class="review-stars" data-rating="{{ $review->rating }}">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="star {{ $i <= $review->rating ? 'filled' : '' }}" data-star-value="{{ $i }}">★</span>
                                @endfor
                                @auth
                                    @if(Auth::user()->isAdmin())
                                        <span class="admin-edit-rating" style="margin-right: 10px; cursor: pointer; color: #3b82f6;" title="לחץ על כוכב לעדכון דירוג">
                                            <i class="fas fa-edit"></i>
                                        </span>
                                    @endif
                                @endauth
                            </div>
                            <p class="review-text">{{ $review->text }}</p>
                            @auth
                                @if(Auth::user()->isAdmin())
                                    <div class="admin-review-actions" style="margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(148, 163, 184, 0.2);">
                                        <form action="{{ route('reviews.destroy', $review) }}" method="POST" style="display: inline;" onsubmit="return confirm('האם אתה בטוח שברצונך למחוק ביקורת זו?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                                <i class="fas fa-trash"></i> מחק ביקורת
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
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

        // Admin review rating update
        document.addEventListener('DOMContentLoaded', function() {
            const reviewCards = document.querySelectorAll('.review-card');
            
            reviewCards.forEach(card => {
                const stars = Array.from(card.querySelectorAll('.star[data-star-value]'));
                const editIcon = card.querySelector('.admin-edit-rating');
                
                if (editIcon && stars.length > 0) {
                    let isEditMode = false;
                    const originalRating = parseInt(card.querySelector('.review-stars').dataset.rating);
                    let handlers = [];
                    
                    editIcon.addEventListener('click', function() {
                        isEditMode = !isEditMode;
                        
                        if (isEditMode) {
                            // Enable edit mode
                            stars.forEach(star => {
                                star.style.cursor = 'pointer';
                                star.style.opacity = '0.7';
                                
                                const mouseEnterHandler = function() {
                                    const value = parseInt(this.getAttribute('data-star-value'));
                                    highlightStars(stars, value);
                                };
                                
                                const clickHandler = function() {
                                    const newRating = parseInt(this.getAttribute('data-star-value'));
                                    updateReviewRating(card.dataset.reviewId, newRating, stars);
                                    isEditMode = false;
                                    // Remove all handlers
                                    stars.forEach((s, idx) => {
                                        s.removeEventListener('mouseenter', handlers[idx].mouseenter);
                                        s.removeEventListener('click', handlers[idx].click);
                                        s.style.cursor = 'default';
                                        s.style.opacity = '1';
                                    });
                                    handlers = [];
                                };
                                
                                star.addEventListener('mouseenter', mouseEnterHandler);
                                star.addEventListener('click', clickHandler);
                                
                                handlers.push({ mouseenter: mouseEnterHandler, click: clickHandler });
                            });
                        } else {
                            // Disable edit mode
                            stars.forEach((star, idx) => {
                                if (handlers[idx]) {
                                    star.removeEventListener('mouseenter', handlers[idx].mouseenter);
                                    star.removeEventListener('click', handlers[idx].click);
                                }
                                star.style.cursor = 'default';
                                star.style.opacity = '1';
                            });
                            highlightStars(stars, originalRating);
                            handlers = [];
                        }
                    });
                }
            });
        });

        function highlightStars(stars, rating) {
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.style.opacity = '1';
                    star.classList.add('filled');
                } else {
                    star.style.opacity = '0.3';
                    star.classList.remove('filled');
                }
            });
        }

        function updateReviewRating(reviewId, rating, stars) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                              document.querySelector('input[name="_token"]')?.value;
            
            fetch(`/reviews/${reviewId}/rating`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ rating: rating })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update stars display
                    stars.forEach((star, index) => {
                        if (index < rating) {
                            star.classList.add('filled');
                        } else {
                            star.classList.remove('filled');
                        }
                    });
                    alert('הדירוג עודכן בהצלחה');
                    location.reload(); // Reload to update average rating
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('שגיאה בעדכון הדירוג');
            });
        }

        // Like button functionality
        const likeBtn = document.getElementById('likeBtn');
        if (likeBtn) {
            likeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const trainerId = this.getAttribute('data-trainer-id');
                const heartIcon = this.querySelector('i');
                const countSpan = document.getElementById('likeCount');
                
                // Disable button during request
                this.disabled = true;
                this.style.opacity = '0.6';
                
                fetch(`/trainers/${trainerId}/like`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI
                        if (data.liked) {
                            this.classList.add('liked');
                            heartIcon.style.color = '#e74c3c';
                            heartIcon.style.animation = 'heartBeat 0.5s ease';
                        } else {
                            this.classList.remove('liked');
                            heartIcon.style.color = 'var(--primary)';
                        }
                        
                        // Update count
                        if (countSpan) {
                            countSpan.textContent = data.count;
                        }
                    } else {
                        alert(data.message || 'שגיאה בעדכון הלייק');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('שגיאה בעדכון הלייק');
                })
                .finally(() => {
                    // Re-enable button
                    this.disabled = false;
                    this.style.opacity = '1';
                });
            });
        }
    </script>
    <style>
        @keyframes heartBeat {
            0%, 100% { transform: scale(1); }
            25% { transform: scale(1.3); }
            50% { transform: scale(1.1); }
            75% { transform: scale(1.2); }
        }
        
        .like-btn:hover {
            background: rgba(74, 158, 255, 0.1) !important;
            transform: translateY(-2px);
        }
        
        .like-btn.liked {
            border-color: #e74c3c !important;
            background: rgba(231, 76, 60, 0.1) !important;
        }
    </style>
</body>
</html>
