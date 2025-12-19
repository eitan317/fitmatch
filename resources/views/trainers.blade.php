<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>מצא מאמן כושר</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('site/style.css') }}">
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        <h1 class="page-title">מצא מאמן כושר</h1>

        <form method="GET" action="{{ route('trainers.index') }}" class="trainers-filters">
            <div class="filters-row-main">
                <!-- Search by name / city -->
                <div class="filter-block">
                    <label for="search">חיפוש</label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="חפש מאמן לפי שם או עיר..."
                    />
                </div>

                <!-- Price range -->
                <div class="filter-block">
                    <label>טווח מחיר (לשעה)</label>
                    <div class="price-range">
                        <input type="number" id="min_price" name="min_price" value="{{ request('min_price') }}" placeholder="מינימום" min="0" />
                        <span>-</span>
                        <input type="number" id="max_price" name="max_price" value="{{ request('max_price') }}" placeholder="מקסימום" min="0" />
                    </div>
                </div>

                <!-- Training type quick filter -->
                <div class="filter-block">
                    <label>סוג אימון</label>
                    <select id="training_type" name="training_type">
                        <option value="">כל הסוגים</option>
                        <option value="weightloss" {{ request('training_type') == 'weightloss' ? 'selected' : '' }}>חיטוב / ירידה במשקל</option>
                        <option value="gym_basic" {{ request('training_type') == 'gym_basic' ? 'selected' : '' }}>חדר כושר</option>
                        <option value="running" {{ request('training_type') == 'running' ? 'selected' : '' }}>ריצה</option>
                        <option value="home_bodyweight" {{ request('training_type') == 'home_bodyweight' ? 'selected' : '' }}>אימוני בית</option>
                        <option value="group" {{ request('training_type') == 'group' ? 'selected' : '' }}>אימונים קבוצתיים</option>
                        <option value="online" {{ request('training_type') == 'online' ? 'selected' : '' }}>אימונים אונליין</option>
                        <option value="yoga" {{ request('training_type') == 'yoga' ? 'selected' : '' }}>יוגה / פילאטיס</option>
                    </select>
                </div>

                <div class="filter-block">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary">חפש</button>
                    <a href="{{ route('trainers.index') }}" class="btn btn-outline">נקה</a>
                </div>
            </div>
        </form>

        <div id="public-trainers-container" class="trainers-grid">
            @forelse($trainers as $trainer)
                <div class="trainer-card">
                    <div class="trainer-card-image">
                        @if($trainer->profile_image_path)
                            <img src="{{ asset('storage/' . $trainer->profile_image_path) }}" alt="{{ $trainer->full_name }}" class="trainer-profile-img">
                        @else
                            <div class="trainer-avatar">{{ substr($trainer->full_name, 0, 1) }}</div>
                        @endif
                    </div>
                    <div class="trainer-card-header">
                        <div class="trainer-info">
                            <h3 class="trainer-name">{{ $trainer->full_name }}</h3>
                            <p class="trainer-city">{{ $trainer->city }}</p>
                        </div>
                        @if($trainer->subscriptionPlan && $trainer->subscriptionPlan->badge_text)
                            <div style="background: var(--primary); color: white; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; margin-top: 0.5rem;">
                                {{ $trainer->subscriptionPlan->badge_text }}
                            </div>
                        @endif
                    </div>
                    @if($trainer->main_specialization)
                        <p class="trainer-info"><strong>התמחות:</strong> {{ $trainer->main_specialization }}</p>
                    @endif
                    @if($trainer->experience_years)
                        <p class="trainer-info"><strong>ניסיון:</strong> {{ $trainer->experience_years }} שנים</p>
                    @endif
                    @if($trainer->price_per_session)
                        <p class="price">{{ number_format($trainer->price_per_session) }} ₪ לשעה</p>
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
                    @if($trainer->training_types && count($trainer->training_types) > 0)
                        <div class="trainer-tags">
                            @foreach(array_slice($trainer->getTrainingTypesWithLabels(), 0, 3) as $typeData)
                                <span class="badge badge-type">{{ $typeData['label'] }}</span>
                            @endforeach
                        </div>
                    @endif
                    <div class="trainer-card-actions">
                        <a href="{{ route('trainers.show', $trainer) }}" class="btn btn-primary">צפה בפרופיל</a>
                    </div>
                </div>
            @empty
                <div class="no-trainers">
                    <p>לא נמצאו מאמנים.</p>
                </div>
            @endforelse
        </div>
    </main>

    @include('partials.footer')

    <script src="{{ asset('site/script.js') }}"></script>
    <script>
        initTheme && initTheme();
        initNavbarToggle && initNavbarToggle();
    </script>
</body>
</html>
