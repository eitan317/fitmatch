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

        <!-- Progress Indicator -->
        <div class="wizard-progress">
            <div class="wizard-progress-bar">
                <div class="wizard-progress-fill" id="wizardProgressFill" style="width: 25%"></div>
            </div>
            <div class="wizard-progress-text">
                <span id="wizardStepText">שלב 1 מתוך 4</span>
                <span id="wizardStepPercentage">25%</span>
            </div>
        </div>

        <form action="{{ route('trainers.store') }}" method="POST" enctype="multipart/form-data" class="wizard-form" id="trainerRegistrationForm">
            @csrf
            
            <!-- Step 1: Personal Details -->
            <div class="wizard-step active" data-step="1">
                <div class="wizard-step-header">
                    <h2>📋 פרטים אישיים</h2>
                </div>
                <div class="wizard-step-content">
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

            <!-- Step 2: Training Types -->
            <div class="wizard-step" data-step="2">
                <div class="wizard-step-header">
                    <h2>💪 סוגי אימונים</h2>
                    <p class="wizard-step-subtitle">בחר את סוגי האימונים שאתה מציע (אפשר לבחור כמה)</p>
                </div>
                <div class="wizard-step-content">
                    <div class="training-types-container">
                        <input
                            type="text"
                            id="trainingTypesSearch"
                            class="training-types-search-input"
                            placeholder="חפש סוג אימון..."
                        />
                        
                        <div class="training-types-list">
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

            <!-- Step 3: Pricing -->
            <div class="wizard-step" data-step="3">
                <div class="wizard-step-header">
                    <h2>💰 תמחור</h2>
                </div>
                <div class="wizard-step-content">
                    <div class="form-group">
                        <label for="price_per_session">מחיר לאימון בודד (ש"ח)</label>
                        <input type="number" id="price_per_session" name="price_per_session" min="0" value="{{ old('price_per_session') }}">
                    </div>
                </div>
            </div>

            <!-- Step 4: Additional Details -->
            <div class="wizard-step" data-step="4">
                <div class="wizard-step-header">
                    <h2>📸 פרטים נוספים</h2>
                </div>
                <div class="wizard-step-content">
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

                    <div class="form-group">
                        <label for="profile_image">תמונת פרופיל (אופציונלי)</label>
                        <label for="profile_image" class="file-upload-btn">
                            <i class="fas fa-camera"></i>
                            <span>לחץ להעלאת תמונה</span>
                            <input type="file" id="profile_image" name="profile_image" accept="image/jpeg,image/png,image/jpg,image/gif" style="display: none;">
                        </label>
                        <div id="imagePreview" style="display: none; margin-top: 1rem; text-align: center;">
                            <img id="previewImg" src="" alt="תצוגה מקדימה" style="max-width: 200px; border-radius: 12px; border: 2px solid var(--primary); box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                        </div>
                        <div id="imageUploadError" style="display: none; margin-top: 0.5rem; padding: 0.75rem; background: rgba(220, 38, 38, 0.1); border: 1px solid var(--accent); border-radius: 8px; color: var(--accent); font-size: 0.85rem;"></div>
                        <small class="form-text text-muted" style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.5rem; display: block;">פורמטים מותרים: JPG, PNG, GIF</small>
                        @if($errors->has('profile_image'))
                            <span class="error" style="color: var(--accent); font-size: 0.85rem; display: block; margin-top: 0.25rem;">{{ $errors->first('profile_image') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="wizard-navigation">
                <button type="button" class="btn btn-secondary wizard-btn-back" id="wizardBtnBack" style="display: none;">
                    ← חזרה
                </button>
                <button type="button" class="btn btn-primary wizard-btn-next" id="wizardBtnNext">
                    הבא →
                </button>
                <button type="submit" class="btn btn-primary wizard-btn-submit" id="wizardBtnSubmit" style="display: none;">
                    שלח בקשה לאישור
                </button>
            </div>
        </form>
    </main>

    <script src="/site/script.js"></script>
    <script>
        // Step-based Wizard Implementation
        (function() {
            const form = document.getElementById('trainerRegistrationForm');
            if (!form) return;

            let currentStep = 1;
            const totalSteps = 4;
            const steps = form.querySelectorAll('.wizard-step');
            const btnNext = document.getElementById('wizardBtnNext');
            const btnBack = document.getElementById('wizardBtnBack');
            const btnSubmit = document.getElementById('wizardBtnSubmit');
            const progressFill = document.getElementById('wizardProgressFill');
            const stepText = document.getElementById('wizardStepText');
            const stepPercentage = document.getElementById('wizardStepPercentage');

            // Form state persistence
            const formState = {
                step1: {},
                step2: {},
                step3: {},
                step4: {}
            };

            // Save form state
            function saveFormState() {
                // Step 1
                formState.step1 = {
                    full_name: document.getElementById('full_name').value,
                    city: document.getElementById('city').value,
                    phone: document.getElementById('phone').value,
                    age: document.getElementById('age').value,
                    experience_years: document.getElementById('experience_years').value,
                    main_specialization: document.getElementById('main_specialization').value
                };

                // Step 2 - training types
                const trainingTypes = [];
                form.querySelectorAll('input[name="training_types[]"]:checked').forEach(cb => {
                    trainingTypes.push(cb.value);
                });
                formState.step2 = { training_types: trainingTypes };

                // Step 3
                formState.step3 = {
                    price_per_session: document.getElementById('price_per_session').value
                };

                // Step 4
                formState.step4 = {
                    instagram: document.getElementById('instagram').value,
                    tiktok: document.getElementById('tiktok').value,
                    bio: document.getElementById('bio').value
                };
            }

            // Restore form state
            function restoreFormState() {
                // Step 1
                if (formState.step1.full_name) document.getElementById('full_name').value = formState.step1.full_name;
                if (formState.step1.city) document.getElementById('city').value = formState.step1.city;
                if (formState.step1.phone) document.getElementById('phone').value = formState.step1.phone;
                if (formState.step1.age) document.getElementById('age').value = formState.step1.age;
                if (formState.step1.experience_years) document.getElementById('experience_years').value = formState.step1.experience_years;
                if (formState.step1.main_specialization) document.getElementById('main_specialization').value = formState.step1.main_specialization;

                // Step 2
                if (formState.step2.training_types) {
                    formState.step2.training_types.forEach(value => {
                        const checkbox = form.querySelector(`input[name="training_types[]"][value="${value}"]`);
                        if (checkbox) checkbox.checked = true;
                    });
                }

                // Step 3
                if (formState.step3.price_per_session) document.getElementById('price_per_session').value = formState.step3.price_per_session;

                // Step 4
                if (formState.step4.instagram) document.getElementById('instagram').value = formState.step4.instagram;
                if (formState.step4.tiktok) document.getElementById('tiktok').value = formState.step4.tiktok;
                if (formState.step4.bio) document.getElementById('bio').value = formState.step4.bio;
            }

            // Validate current step
            function validateStep(step) {
                if (step === 1) {
                    const fullName = document.getElementById('full_name').value.trim();
                    const city = document.getElementById('city').value.trim();
                    if (!fullName || !city) {
                        alert('אנא מלא את שדות החובה: שם מלא ועיר');
                        return false;
                    }
                } else if (step === 2) {
                    const checkedTypes = form.querySelectorAll('input[name="training_types[]"]:checked');
                    if (checkedTypes.length === 0) {
                        alert('אנא בחר לפחות סוג אימון אחד');
                        return false;
                    }
                }
                return true;
            }

            // Update progress
            function updateProgress() {
                const percentage = (currentStep / totalSteps) * 100;
                if (progressFill) progressFill.style.width = percentage + '%';
                if (stepText) stepText.textContent = `שלב ${currentStep} מתוך ${totalSteps}`;
                if (stepPercentage) stepPercentage.textContent = Math.round(percentage) + '%';
            }

            // Show step
            function showStep(step) {
                steps.forEach((s, index) => {
                    if (index + 1 === step) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });

                // Update buttons
                if (btnBack) {
                    btnBack.style.display = step > 1 ? 'inline-block' : 'none';
                }
                if (btnNext) {
                    btnNext.style.display = step < totalSteps ? 'inline-block' : 'none';
                }
                if (btnSubmit) {
                    btnSubmit.style.display = step === totalSteps ? 'inline-block' : 'none';
                }

                updateProgress();

                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            // Next button
            if (btnNext) {
                btnNext.addEventListener('click', function() {
                    saveFormState();
                    if (validateStep(currentStep)) {
                        currentStep++;
                        restoreFormState();
                        showStep(currentStep);
                    }
                });
            }

            // Back button
            if (btnBack) {
                btnBack.addEventListener('click', function() {
                    saveFormState();
                    currentStep--;
                    restoreFormState();
                    showStep(currentStep);
                });
            }

            // Training types search
            const searchInput = document.getElementById('trainingTypesSearch');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const query = e.target.value.toLowerCase();
                    const checkboxes = form.querySelectorAll('.training-type-checkbox');
                    checkboxes.forEach(cb => {
                        const text = cb.querySelector('span').textContent.toLowerCase();
                        cb.style.display = text.includes(query) ? 'block' : 'none';
                    });
                });
            }

            // Image preview
            const profileImageInput = document.getElementById('profile_image');
            if (profileImageInput) {
                profileImageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    const errorDiv = document.getElementById('imageUploadError');
                    const preview = document.getElementById('imagePreview');
                    const img = document.getElementById('previewImg');
                    
                    if (errorDiv) {
                        errorDiv.style.display = 'none';
                        errorDiv.textContent = '';
                    }
                    
                    if (file) {
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                        if (!allowedTypes.includes(file.type)) {
                            if (errorDiv) {
                                errorDiv.textContent = 'סוג קובץ לא נתמך. אנא בחר תמונה בפורמט JPG, PNG או GIF';
                                errorDiv.style.display = 'block';
                            }
                            this.value = '';
                            if (preview) preview.style.display = 'none';
                            return;
                        }
                        
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            if (preview && img) {
                                img.src = e.target.result;
                                preview.style.display = 'block';
                            }
                        };
                        reader.onerror = function() {
                            if (errorDiv) {
                                errorDiv.textContent = 'שגיאה בקריאת הקובץ. אנא נסה שוב';
                                errorDiv.style.display = 'block';
                            }
                        };
                        reader.readAsDataURL(file);
                    } else {
                        if (preview) preview.style.display = 'none';
                    }
                });
            }

            // Initialize
            showStep(1);

            // Initialize theme if available
            if (typeof initTheme === 'function') {
                initTheme();
            }
            if (typeof initNavbarToggle === 'function') {
                initNavbarToggle();
            }
        })();
    </script>
    @include('partials.accessibility-panel')
</body>
</html>
