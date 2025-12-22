<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>מצא מאמן כושר</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/site/style.css">
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
                        
                        <optgroup label="🏋️ חדר כושר וכוח">
                            <option value="gym_basic" {{ request('training_type') == 'gym_basic' ? 'selected' : '' }}>חדר כושר בסיסי</option>
                            <option value="hypertrophy" {{ request('training_type') == 'hypertrophy' ? 'selected' : '' }}>מסת שריר</option>
                            <option value="powerlifting" {{ request('training_type') == 'powerlifting' ? 'selected' : '' }}>פאוורליפטינג</option>
                            <option value="crossfit" {{ request('training_type') == 'crossfit' ? 'selected' : '' }}>קרוספיט</option>
                            <option value="street_workout" {{ request('training_type') == 'street_workout' ? 'selected' : '' }}>סטריט וורקאאוט / מתח מקבילים</option>
                        </optgroup>
                        
                        <optgroup label="🔥 חיטוב וכושר">
                            <option value="weightloss" {{ request('training_type') == 'weightloss' ? 'selected' : '' }}>חיטוב / ירידה במשקל</option>
                            <option value="hiit" {{ request('training_type') == 'hiit' ? 'selected' : '' }}>אימוני HIIT</option>
                            <option value="intervals" {{ request('training_type') == 'intervals' ? 'selected' : '' }}>אינטרוולים עצימים</option>
                            <option value="bootcamp" {{ request('training_type') == 'bootcamp' ? 'selected' : '' }}>בוטקמפ</option>
                        </optgroup>
                        
                        <optgroup label="🧘 גמישות ושיקום">
                            <option value="mobility" {{ request('training_type') == 'mobility' ? 'selected' : '' }}>מוביליטי וגמישות</option>
                            <option value="yoga" {{ request('training_type') == 'yoga' ? 'selected' : '' }}>יוגה</option>
                            <option value="pilates" {{ request('training_type') == 'pilates' ? 'selected' : '' }}>פילאטיס</option>
                            <option value="physio_rehab" {{ request('training_type') == 'physio_rehab' ? 'selected' : '' }}>שיקום / פיזיותרפיה</option>
                            <option value="back_pain" {{ request('training_type') == 'back_pain' ? 'selected' : '' }}>אימונים לכאבי גב</option>
                            <option value="postnatal" {{ request('training_type') == 'postnatal' ? 'selected' : '' }}>נשים אחרי לידה</option>
                        </optgroup>
                        
                        <optgroup label="🏠 אימוני בית">
                            <option value="home_bodyweight" {{ request('training_type') == 'home_bodyweight' ? 'selected' : '' }}>אימוני בית (משקל גוף)</option>
                            <option value="trx" {{ request('training_type') == 'trx' ? 'selected' : '' }}>אימוני TRX</option>
                            <option value="short20" {{ request('training_type') == 'short20' ? 'selected' : '' }}>אימונים קצרים (20 דק׳)</option>
                        </optgroup>
                        
                        <optgroup label="🏃 אירובי וסיבולת">
                            <option value="running" {{ request('training_type') == 'running' ? 'selected' : '' }}>ריצה</option>
                            <option value="sprints" {{ request('training_type') == 'sprints' ? 'selected' : '' }}>ספרינטים</option>
                            <option value="marathon" {{ request('training_type') == 'marathon' ? 'selected' : '' }}>הכנה למרתון</option>
                            <option value="cycling" {{ request('training_type') == 'cycling' ? 'selected' : '' }}>רכיבה על אופניים</option>
                            <option value="swimming" {{ request('training_type') == 'swimming' ? 'selected' : '' }}>שחייה</option>
                        </optgroup>
                        
                        <optgroup label="🥊 קרב מגע">
                            <option value="boxing" {{ request('training_type') == 'boxing' ? 'selected' : '' }}>אגרוף</option>
                            <option value="kickboxing" {{ request('training_type') == 'kickboxing' ? 'selected' : '' }}>קיקבוקס</option>
                            <option value="mma" {{ request('training_type') == 'mma' ? 'selected' : '' }}>MMA</option>
                            <option value="kravmaga" {{ request('training_type') == 'kravmaga' ? 'selected' : '' }}>קרב מגע</option>
                        </optgroup>
                        
                        <optgroup label="👥 פורמטים מיוחדים">
                            <option value="couple" {{ request('training_type') == 'couple' ? 'selected' : '' }}>אימונים זוגיים</option>
                            <option value="group" {{ request('training_type') == 'group' ? 'selected' : '' }}>אימונים קבוצתיים</option>
                            <option value="online" {{ request('training_type') == 'online' ? 'selected' : '' }}>אימונים אונליין (זום)</option>
                            <option value="outdoor" {{ request('training_type') == 'outdoor' ? 'selected' : '' }}>אימונים בחוץ / בפארק</option>
                        </optgroup>
                        
                        <optgroup label="👨‍👩‍👧‍👦 אוכלוסיות יעד">
                            <option value="women_only" {{ request('training_type') == 'women_only' ? 'selected' : '' }}>נשים בלבד</option>
                            <option value="men_only" {{ request('training_type') == 'men_only' ? 'selected' : '' }}>גברים בלבד</option>
                            <option value="teens" {{ request('training_type') == 'teens' ? 'selected' : '' }}>נוער</option>
                            <option value="kids" {{ request('training_type') == 'kids' ? 'selected' : '' }}>ילדים</option>
                            <option value="seniors" {{ request('training_type') == 'seniors' ? 'selected' : '' }}>גיל שלישי</option>
                        </optgroup>
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

    <script src="/site/script.js"></script>
    <script>
        if (typeof initTheme === 'function') {
            initTheme();
        }
        if (typeof initNavbarToggle === 'function') {
            initNavbarToggle();
        }
    </script>
</body>
</html>
