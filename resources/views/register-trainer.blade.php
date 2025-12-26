<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        <!-- Progress Indicator -->
        <div class="registration-progress">
            <div class="progress-text">
                <span id="progressText">סקציה 0 מתוך 4</span>
                <span id="progressPercentage">0%</span>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar" id="progressBar" style="width: 0%"></div>
            </div>
            <div class="progress-sections">
                <div class="progress-section-item" data-section="1">
                    <span class="section-status-icon">○</span>
                    <span class="section-name">פרטים אישיים</span>
                </div>
                <div class="progress-section-item" data-section="2">
                    <span class="section-status-icon">○</span>
                    <span class="section-name">סוגי אימונים</span>
                </div>
                <div class="progress-section-item" data-section="3">
                    <span class="section-status-icon">○</span>
                    <span class="section-name">תמחור</span>
                </div>
                <div class="progress-section-item" data-section="4">
                    <span class="section-status-icon">○</span>
                    <span class="section-name">פרטים נוספים</span>
                </div>
            </div>
        </div>

        <form action="{{ route('trainers.store') }}" method="POST" enctype="multipart/form-data" class="form-container" id="trainerRegistrationForm">
            @csrf
            
            <!-- Section 1: Personal Details -->
            <div class="accordion-section" data-section="1">
                <div class="accordion-header" role="button" tabindex="0" aria-expanded="false" aria-controls="accordion-content-1">
                    <div class="accordion-header-left">
                        <span class="section-status-icon">○</span>
                        <h2 class="accordion-title">📋 פרטים אישיים</h2>
                    </div>
                    <span class="accordion-chevron">▾</span>
                </div>
                <div class="accordion-content" id="accordion-content-1">
                
                <div class="form-group">
                    <label for="full_name">שם מלא *</label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                </div>

                <div class="form-group">
                    <label for="age">גיל</label>
                    <input type="number" id="age" name="age" min="18" max="120" value="{{ old('age') }}">
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
                    <label for="experience_years">שנות ניסיון</label>
                    <input type="number" id="experience_years" name="experience_years" min="0" max="60" value="{{ old('experience_years') }}">
                </div>

                <div class="form-group">
                    <label for="main_specialization">התמחות עיקרית</label>
                    <input type="text" id="main_specialization" name="main_specialization" value="{{ old('main_specialization') }}">
                </div>

                <div class="form-group">
                    <label for="profile_image">תמונת פרופיל (אופציונלי)</label>
                    <label for="profile_image" class="file-upload-btn" style="display: block; padding: 1.5rem; border: 2px dashed var(--border-soft); border-radius: 12px; text-align: center; cursor: pointer; background: var(--bg-card); transition: all 0.2s ease;">
                        <i class="fas fa-camera" style="font-size: 2rem; margin-bottom: 0.5rem; display: block; color: var(--primary);"></i>
                        <span style="color: var(--text-main); font-weight: 500;">לחץ להעלאת תמונה</span>
                        <input type="file" id="profile_image" name="profile_image" accept="image/jpeg,image/png,image/jpg,image/gif" style="display: none;">
                    </label>
                    <div id="imagePreview" style="display: none; margin-top: 1rem; text-align: center;">
                        <img id="previewImg" src="" alt="תצוגה מקדימה" style="max-width: 200px; border-radius: 12px; border: 2px solid var(--primary); box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                    </div>
                    <small class="form-text text-muted" style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.5rem; display: block;">גודל מקסימלי: 5MB. פורמטים: JPG, PNG, GIF</small>
                    @if($errors->has('profile_image'))
                        <span class="error" style="color: var(--accent); font-size: 0.85rem; display: block; margin-top: 0.25rem;">{{ $errors->first('profile_image') }}</span>
                    @endif
                </div>
                </div>
            </div>

            <!-- Section 2: Training Types -->
            <div class="accordion-section training-types-card" data-section="2">
                <div class="accordion-header" role="button" tabindex="0" aria-expanded="false" aria-controls="accordion-content-2">
                    <div class="accordion-header-left">
                        <span class="section-status-icon">○</span>
                        <h2 class="accordion-title">💪 סוגי אימונים</h2>
                    </div>
                    <span class="accordion-chevron">▾</span>
                </div>
                <div class="accordion-content" id="accordion-content-2">
                <p class="form-section-subtitle">סוגי אימונים שאתה מציע (אפשר לבחור כמה)</p>

                <div class="training-types-select">
                    <div class="training-types-toggle" id="trainingTypesToggle">
                        <span id="trainingTypesSummary">בחר סוגי אימונים...</span>
                        <span class="training-types-chevron">▾</span>
                    </div>

                    <div class="training-types-dropdown" id="trainingTypesDropdown">
                        <input
                            type="text"
                            id="trainingTypesSearch"
                            class="training-types-search"
                            placeholder="חפש סוג אימון (למשל: חיטוב, ריצה, יוגה...)"
                        />

                        <ul class="training-types-options" id="trainingTypesList">
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">חדר כושר בסיסי</span><input type="checkbox" name="training_types[]" value="gym_basic"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">מסת שריר</span><input type="checkbox" name="training_types[]" value="hypertrophy"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">פאוורליפטינג</span><input type="checkbox" name="training_types[]" value="powerlifting"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">קרוספיט</span><input type="checkbox" name="training_types[]" value="crossfit"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">סטריט וורקאאוט / מתח מקבילים</span><input type="checkbox" name="training_types[]" value="street_workout"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">חיטוב / ירידה במשקל</span><input type="checkbox" name="training_types[]" value="weightloss"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אימוני HIIT</span><input type="checkbox" name="training_types[]" value="hiit"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אינטרוולים עצימים</span><input type="checkbox" name="training_types[]" value="intervals"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">מוביליטי וגמישות</span><input type="checkbox" name="training_types[]" value="mobility"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">יוגה</span><input type="checkbox" name="training_types[]" value="yoga"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">פילאטיס</span><input type="checkbox" name="training_types[]" value="pilates"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">שיקום / פיזיותרפיה</span><input type="checkbox" name="training_types[]" value="physio_rehab"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אימונים לכאבי גב</span><input type="checkbox" name="training_types[]" value="back_pain"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">נשים אחרי לידה</span><input type="checkbox" name="training_types[]" value="postnatal"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אימוני בית (משקל גוף)</span><input type="checkbox" name="training_types[]" value="home_bodyweight"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אימוני TRX</span><input type="checkbox" name="training_types[]" value="trx"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אימונים קצרים (20 דק׳)</span><input type="checkbox" name="training_types[]" value="short20"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">ריצה</span><input type="checkbox" name="training_types[]" value="running"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">ספרינטים</span><input type="checkbox" name="training_types[]" value="sprints"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">הכנה למרתון</span><input type="checkbox" name="training_types[]" value="marathon"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">רכיבה על אופניים</span><input type="checkbox" name="training_types[]" value="cycling"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">שחייה</span><input type="checkbox" name="training_types[]" value="swimming"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אגרוף</span><input type="checkbox" name="training_types[]" value="boxing"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">קיקבוקס</span><input type="checkbox" name="training_types[]" value="kickboxing"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">MMA</span><input type="checkbox" name="training_types[]" value="mma"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">קרב מגע</span><input type="checkbox" name="training_types[]" value="kravmaga"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אימונים זוגיים</span><input type="checkbox" name="training_types[]" value="couple"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אימונים קבוצתיים</span><input type="checkbox" name="training_types[]" value="group"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אימונים אונליין (זום)</span><input type="checkbox" name="training_types[]" value="online"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אימונים בחוץ / בפארק</span><input type="checkbox" name="training_types[]" value="outdoor"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">בוטקמפ</span><input type="checkbox" name="training_types[]" value="bootcamp"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">נשים בלבד</span><input type="checkbox" name="training_types[]" value="women_only"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">גברים בלבד</span><input type="checkbox" name="training_types[]" value="men_only"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">נוער</span><input type="checkbox" name="training_types[]" value="teens"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">ילדים</span><input type="checkbox" name="training_types[]" value="kids"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">גיל שלישי</span><input type="checkbox" name="training_types[]" value="seniors"></label></li>
                        </ul>
                    </div>
                </div>
                </div>
            </div>

            <!-- Section 3: Pricing -->
            <div class="accordion-section pricing-card" data-section="3">
                <div class="accordion-header" role="button" tabindex="0" aria-expanded="false" aria-controls="accordion-content-3">
                    <div class="accordion-header-left">
                        <span class="section-status-icon">○</span>
                        <h2 class="accordion-title">💰 תמחור</h2>
                    </div>
                    <span class="accordion-chevron">▾</span>
                </div>
                <div class="accordion-content" id="accordion-content-3">
                <div class="form-group">
                    <label for="price_per_session">מחיר לאימון בודד (ש"ח)</label>
                    <input type="number" id="price_per_session" name="price_per_session" min="0" value="{{ old('price_per_session') }}">
                </div>
                </div>
            </div>

            <!-- Section 4: Additional Details -->
            <div class="accordion-section" data-section="4">
                <div class="accordion-header" role="button" tabindex="0" aria-expanded="false" aria-controls="accordion-content-4">
                    <div class="accordion-header-left">
                        <span class="section-status-icon">○</span>
                        <h2 class="accordion-title">📸 פרטים נוספים</h2>
                    </div>
                    <span class="accordion-chevron">▾</span>
                </div>
                <div class="accordion-content" id="accordion-content-4">

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

            <button type="submit" class="btn btn-primary sticky-submit">שלח בקשה לאישור</button>
        </form>
    </main>

    <script src="/site/script.js"></script>
    <script>
        // Wait for both DOM and script.js to be fully loaded
        function initializePage() {
            if (typeof initTheme === 'function') {
                initTheme();
            } else {
                console.warn('initTheme function not found');
            }
            
            if (typeof initNavbarToggle === 'function') {
                initNavbarToggle();
            } else {
                console.warn('initNavbarToggle function not found');
            }
            
            if (typeof initTrainingTypesSelectorOnRegisterPage === 'function') {
                initTrainingTypesSelectorOnRegisterPage();
            } else {
                console.error('initTrainingTypesSelectorOnRegisterPage function not found - script.js may not be loaded yet');
                // Retry after a short delay
                setTimeout(function() {
                    if (typeof initTrainingTypesSelectorOnRegisterPage === 'function') {
                        console.log('Retrying initTrainingTypesSelectorOnRegisterPage...');
                        initTrainingTypesSelectorOnRegisterPage();
                    } else {
                        console.error('initTrainingTypesSelectorOnRegisterPage still not found after retry');
                    }
                }, 100);
            }
            
            // Initialize Accordion
            if (typeof initRegistrationAccordion === 'function') {
                initRegistrationAccordion();
            } else {
                console.warn('initRegistrationAccordion function not found');
            }
            
            // Initialize Progress Tracking
            if (typeof initRegistrationProgressTracking === 'function') {
                initRegistrationProgressTracking();
            } else {
                console.warn('initRegistrationProgressTracking function not found');
            }
            
            // Add form validation before submit
            const form = document.getElementById('trainerRegistrationForm');
            if (form && typeof validateRegistrationForm === 'function') {
                form.addEventListener('submit', function(e) {
                    if (!validateRegistrationForm()) {
                        e.preventDefault();
                        return false;
                    }
                });
            }
            
            // תצוגה מקדימה לתמונת פרופיל
            const profileImageInput = document.getElementById('profile_image');
            if (profileImageInput) {
                profileImageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const preview = document.getElementById('imagePreview');
                            const img = document.getElementById('previewImg');
                            if (preview && img) {
                                img.src = e.target.result;
                                preview.style.display = 'block';
                            }
                        }
                        reader.readAsDataURL(file);
                    } else {
                        const preview = document.getElementById('imagePreview');
                        if (preview) {
                            preview.style.display = 'none';
                        }
                    }
                });
            }
            
            // פתיחת הסקציה הראשונה אוטומטית בטלפון
            if (window.innerWidth <= 480) {
                setTimeout(function() {
                    const firstSection = document.querySelector('.accordion-section[data-section="1"]');
                    if (firstSection) {
                        firstSection.classList.add('active');
                        const content = firstSection.querySelector('.accordion-content');
                        if (content) {
                            content.style.maxHeight = '3000px';
                            content.style.padding = '0 1.75rem 1.5rem 1.75rem';
                        }
                        // עדכון הפרוגרס אם הפונקציה קיימת
                        if (typeof initRegistrationProgressTracking === 'function') {
                            initRegistrationProgressTracking();
                        }
                    }
                }, 200);
            }
        }
        
        // Ensure DOM is ready before initializing
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                // Wait a bit to ensure script.js is loaded
                setTimeout(initializePage, 50);
            });
        } else {
            // DOM is already ready, but wait for script.js
            setTimeout(initializePage, 50);
        }
    </script>
    @include('partials.accessibility-panel')
</body>
</html>

