<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>טופס הרשמה למאמני כושר</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/site/style.css">
    @include('partials.schema-ld')
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        <h1>הרשמה כמאמן כושר</h1>
        <p>מלא את הפרטים שלך והבקשה תישלח לאישור מנהל המערכת.</p>

        @if(session('success'))
            <div class="form-message success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="form-message error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('trainers.store') }}" method="POST" enctype="multipart/form-data" class="form-container" id="trainerRegistrationForm">
            @csrf
            
            <!-- Section 1: Personal Details -->
            <div class="accordion-section" data-section="1">
                <div class="accordion-header" tabindex="0" role="button" aria-expanded="false">
                    <div class="accordion-header-left">
                        <span class="section-status-icon">📋</span>
                        <h2 class="accordion-title">פרטים אישיים</h2>
                    </div>
                    <i class="fas fa-chevron-down accordion-chevron"></i>
                </div>
                <div class="accordion-content">
                    <div class="form-group">
                        <label for="full_name">שם מלא *</label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="city">עיר *</label>
                        <input type="text" id="city" name="city" value="{{ old('city') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">טלפון</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="050-1234567">
                    </div>

                    <div class="form-group">
                        <label for="age">גיל</label>
                        <input type="number" id="age" name="age" min="18" max="120" value="{{ old('age') }}">
                    </div>

                    <div class="form-group">
                        <label for="experience_years">שנות ניסיון</label>
                        <input type="number" id="experience_years" name="experience_years" min="0" max="60" value="{{ old('experience_years') }}">
                    </div>

                    <div class="form-group">
                        <label for="main_specialization">התמחות עיקרית</label>
                        <input type="text" id="main_specialization" name="main_specialization" value="{{ old('main_specialization') }}">
                    </div>
                </div>
            </div>

            <!-- Section 2: Training Types -->
            <div class="accordion-section training-types-card" data-section="2">
                <div class="accordion-header" tabindex="0" role="button" aria-expanded="false">
                    <div class="accordion-header-left">
                        <span class="section-status-icon">💪</span>
                        <h2 class="accordion-title">סוגי אימונים</h2>
                    </div>
                    <i class="fas fa-chevron-down accordion-chevron"></i>
                </div>
                <div class="accordion-content">
                    <p class="form-section-subtitle">בחר את סוגי האימונים שאתה מציע (אפשר לבחור כמה)</p>
                    <div class="training-types-container">
                        <input
                            type="text"
                            id="trainingTypesSearch"
                            class="training-types-search-input"
                            placeholder="חפש סוג אימון..."
                        />
                        
                        <div class="training-types-list">
                            <!-- כוח וחוזק -->
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="strength_training"><span>אימוני כוח</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="gym_basic"><span>חדר כושר בסיסי</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="hypertrophy"><span>מסת שריר</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="powerlifting"><span>פאוורליפטינג</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="crossfit"><span>קרוספיט</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="street_workout"><span>סטריט וורקאאוט / מתח מקבילים</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="weightloss"><span>חיטוב / ירידה במשקל</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="hiit"><span>אימוני HIIT</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="intervals"><span>אינטרוולים עצימים</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="mobility"><span>מוביליטי וגמישות</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="yoga"><span>יוגה</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="pilates"><span>פילאטיס</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="physio_rehab"><span>שיקום / פיזיותרפיה</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="back_pain"><span>אימונים לכאבי גב</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="postnatal"><span>נשים אחרי לידה</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="home_bodyweight"><span>אימוני בית (משקל גוף)</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="trx"><span>אימוני TRX</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="short20"><span>אימונים קצרים (20 דק׳)</span></label>
                            <!-- אירובי וסיבולת לב ריאה -->
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="cardiovascular_endurance"><span>סיבולת לב ריאה</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="running"><span>ריצה</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="sprints"><span>ספרינטים</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="marathon"><span>הכנה למרתון</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="cycling"><span>רכיבה על אופניים</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="swimming"><span>שחייה</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="boxing"><span>אגרוף</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="kickboxing"><span>קיקבוקס</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="mma"><span>MMA</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="kravmaga"><span>קרב מגע</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="couple"><span>אימונים זוגיים</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="group"><span>אימונים קבוצתיים</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="online"><span>אימונים אונליין (זום)</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="outdoor"><span>אימונים בחוץ / בפארק</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="bootcamp"><span>בוטקמפ</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="women_only"><span>נשים בלבד</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="men_only"><span>גברים בלבד</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="teens"><span>נוער</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="kids"><span>ילדים</span></label>
                            <label class="training-type-checkbox"><input type="checkbox" name="training_types[]" value="seniors"><span>גיל שלישי</span></label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Pricing -->
            <div class="accordion-section pricing-card" data-section="3">
                <div class="accordion-header" tabindex="0" role="button" aria-expanded="false">
                    <div class="accordion-header-left">
                        <span class="section-status-icon">💰</span>
                        <h2 class="accordion-title">תמחור</h2>
                    </div>
                    <i class="fas fa-chevron-down accordion-chevron"></i>
                </div>
                <div class="accordion-content">
                    <div class="form-group">
                        <label for="price_per_session">מחיר לאימון בודד (ש"ח)</label>
                        <input type="number" id="price_per_session" name="price_per_session" min="0" value="{{ old('price_per_session') }}">
                    </div>
                </div>
            </div>

            <!-- Section 4: Additional Details -->
            <div class="accordion-section" data-section="4">
                <div class="accordion-header" tabindex="0" role="button" aria-expanded="false">
                    <div class="accordion-header-left">
                        <span class="section-status-icon">📸</span>
                        <h2 class="accordion-title">פרטים נוספים</h2>
                    </div>
                    <i class="fas fa-chevron-down accordion-chevron"></i>
                </div>
                <div class="accordion-content">
                    <div class="form-group">
                        <label for="instagram">אינסטגרם (אופציונלי)</label>
                        <input type="text" id="instagram" name="instagram" value="{{ old('instagram') }}">
                    </div>

                    <div class="form-group">
                        <label for="tiktok">טיקטוק (אופציונלי)</label>
                        <input type="text" id="tiktok" name="tiktok" value="{{ old('tiktok') }}">
                    </div>

                    <div class="form-group">
                        <label for="bio">תיאור קצר (אופציונלי)</label>
                        <textarea id="bio" name="bio" rows="4" placeholder="ספר קצת עליך, סגנון האימונים שלך והניסיון שלך.">{{ old('bio') }}</textarea>
                    </div>

                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary sticky-submit">
                שלח בקשה לאישור
            </button>
        </form>
    </main>

    <script src="/site/script.js"></script>
    <script>
        // Initialize accordion functionality
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof initRegistrationAccordion === 'function') {
                initRegistrationAccordion();
            }
            
            if (typeof initRegistrationProgressTracking === 'function') {
                initRegistrationProgressTracking();
            }

            // Training types search
            const searchInput = document.getElementById('trainingTypesSearch');
            if (searchInput) {
                const form = document.getElementById('trainerRegistrationForm');
                searchInput.addEventListener('input', function(e) {
                    const query = e.target.value.toLowerCase();
                    const checkboxes = form.querySelectorAll('.training-type-checkbox');
                    checkboxes.forEach(cb => {
                        const text = cb.querySelector('span').textContent.toLowerCase();
                        cb.style.display = text.includes(query) ? 'block' : 'none';
                    });
                });
            }


            // Form submission validation
            const form = document.getElementById('trainerRegistrationForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (typeof validateRegistrationForm === 'function') {
                        if (!validateRegistrationForm()) {
                            e.preventDefault();
                            return false;
                        }
                    }
                });
            }

            // Initialize theme and navbar if available
            if (typeof initTheme === 'function') {
                initTheme();
            }
            if (typeof initNavbarToggle === 'function') {
                initNavbarToggle();
            }
        });
    </script>
    @include('partials.accessibility-panel')
</body>
</html>
