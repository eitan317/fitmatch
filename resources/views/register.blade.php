<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>טופס הרשמה למאמני כושר</title>
    @include('partials.adsense-verification')
    @include('partials.adsense')
    <link rel="stylesheet" href="/site/style.css">
    @include('partials.schema-ld')
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        <h1>הרשמה כמאמן כושר</h1>
        <p>מלא את הפרטים שלך והבקשה תישלח לאישור מנהל המערכת.</p>

        <form id="trainer-register-form" class="form-container">
            <div class="form-card">
                <h2 class="form-section-title">📋 פרטים אישיים</h2>
                
                <div class="form-group">
                    <label for="fullName">שם מלא *</label>
                    <input type="text" id="fullName" name="fullName" required>
                </div>

                <div class="form-group">
                    <label for="age">גיל *</label>
                    <input type="number" id="age" name="age" min="16" max="80" required>
                </div>

                <div class="form-group">
                    <label for="city">עיר *</label>
                    <input type="text" id="city" name="city" required>
                </div>

                <div class="form-group">
                    <label for="phone">טלפון *</label>
                    <input type="tel" id="phone" name="phone" required placeholder="050-1234567">
                </div>

                <div class="form-group">
                    <label for="experienceYears">שנות ניסיון *</label>
                    <input type="number" id="experienceYears" name="experienceYears" min="0" max="60" required>
                </div>

                <div class="form-group">
                    <label for="mainSpecialization">התמחות עיקרית *</label>
                    <input type="text" id="mainSpecialization" name="mainSpecialization" required>
                </div>
            </div>

            <div class="form-card training-types-card">
                <div class="form-section-title">💪 סוגי אימונים</div>
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
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">חדר כושר בסיסי</span><input type="checkbox" name="trainingTypes" value="gym_basic"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">מסת שריר</span><input type="checkbox" name="trainingTypes" value="hypertrophy"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">פאוורליפטינג</span><input type="checkbox" name="trainingTypes" value="powerlifting"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">קרוספיט</span><input type="checkbox" name="trainingTypes" value="crossfit"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">סטריט וורקאאוט / מתח מקבילים</span><input type="checkbox" name="trainingTypes" value="street_workout"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">חיטוב / ירידה במשקל</span><input type="checkbox" name="trainingTypes" value="weightloss"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אימוני HIIT</span><input type="checkbox" name="trainingTypes" value="hiit"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אינטרוולים עצימים</span><input type="checkbox" name="trainingTypes" value="intervals"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">מוביליטי וגמישות</span><input type="checkbox" name="trainingTypes" value="mobility"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">יוגה</span><input type="checkbox" name="trainingTypes" value="yoga"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">פילאטיס</span><input type="checkbox" name="trainingTypes" value="pilates"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">שיקום / פיזיותרפיה</span><input type="checkbox" name="trainingTypes" value="physio_rehab"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אימונים לכאבי גב</span><input type="checkbox" name="trainingTypes" value="back_pain"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">נשים אחרי לידה</span><input type="checkbox" name="trainingTypes" value="postnatal"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אימוני בית (משקל גוף)</span><input type="checkbox" name="trainingTypes" value="home_bodyweight"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אימוני TRX</span><input type="checkbox" name="trainingTypes" value="trx"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אימונים קצרים (20 דק׳)</span><input type="checkbox" name="trainingTypes" value="short20"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">ריצה</span><input type="checkbox" name="trainingTypes" value="running"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">ספרינטים</span><input type="checkbox" name="trainingTypes" value="sprints"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">הכנה למרתון</span><input type="checkbox" name="trainingTypes" value="marathon"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">רכיבה על אופניים</span><input type="checkbox" name="trainingTypes" value="cycling"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">שחייה</span><input type="checkbox" name="trainingTypes" value="swimming"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אגרוף</span><input type="checkbox" name="trainingTypes" value="boxing"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">קיקבוקס</span><input type="checkbox" name="trainingTypes" value="kickboxing"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">MMA</span><input type="checkbox" name="trainingTypes" value="mma"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">קרב מגע</span><input type="checkbox" name="trainingTypes" value="kravmaga"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אימונים זוגיים</span><input type="checkbox" name="trainingTypes" value="couple"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אימונים קבוצתיים</span><input type="checkbox" name="trainingTypes" value="group"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אימונים אונליין (זום)</span><input type="checkbox" name="trainingTypes" value="online"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">אימונים בחוץ / בפארק</span><input type="checkbox" name="trainingTypes" value="outdoor"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">בוטקמפ</span><input type="checkbox" name="trainingTypes" value="bootcamp"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">נשים בלבד</span><input type="checkbox" name="trainingTypes" value="women_only"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">גברים בלבד</span><input type="checkbox" name="trainingTypes" value="men_only"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">נוער</span><input type="checkbox" name="trainingTypes" value="teens"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">ילדים</span><input type="checkbox" name="trainingTypes" value="kids"></label></li>
                            <li class="training-type-item"><label class="training-type-option"><span class="option-label">גיל שלישי</span><input type="checkbox" name="trainingTypes" value="seniors"></label></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="form-card pricing-card">
                <h2 class="form-section-title">💰 תמחור</h2>

                <div class="form-group">
                    <label for="pricePerSession">מחיר לאימון בודד (ש"ח) *</label>
                    <input type="number" id="pricePerSession" name="pricePerSession" min="0" required>
                </div>
            </div>

            <div class="form-card">
                <h2 class="form-section-title">📸 פרטים נוספים</h2>

                <div class="form-group">
                    <label for="profileImage">תמונת פרופיל (אופציונלי)</label>
                    <input type="file" id="profileImage" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="instagram">אינסטגרם (אופציונלי)</label>
                    <input type="text" id="instagram" name="instagram">
                </div>

                <div class="form-group">
                    <label for="tiktok">טיקטוק (אופציונלי)</label>
                    <input type="text" id="tiktok" name="tiktok">
                </div>

                <div class="form-group">
                    <label for="bio">תיאור קצר (אופציונלי)</label>
                    <textarea id="bio" name="bio" rows="4" placeholder="ספר קצת עליך, סגנון האימונים שלך והניסיון שלך."></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" id="trainer-submit-button">שלח בקשה לאישור</button>
        </form>

        <div id="trainer-register-message" class="form-message"></div>
    </main>

    <script src="/site/script.js"></script>
    <script>
        // Theme + navbar
        initTheme && initTheme();
        initNavbarToggle && initNavbarToggle();

        (function () {
            const form = document.getElementById("trainer-register-form");
            if (!form) return;

            const messageBox = document.getElementById("trainer-register-message");
            const fileInput = document.getElementById("profileImage");

            function getPendingTrainers() {
                const raw = localStorage.getItem("pendingTrainers");
                if (!raw) return [];
                try {
                    const arr = JSON.parse(raw);
                    return Array.isArray(arr) ? arr : [];
                } catch (e) {
                    return [];
                }
            }

            function savePendingTrainers(list) {
                localStorage.setItem("pendingTrainers", JSON.stringify(list));
            }

            function generateTrainerId() {
                return "t_" + Date.now() + "_" + Math.floor(Math.random() * 100000);
            }

            function showMessage(text, isError) {
                if (messageBox) {
                    messageBox.textContent = text;
                    messageBox.className = "form-message " + (isError ? "error" : "success");
                } else {
                    alert(text);
                }
            }

            form.addEventListener("submit", function (event) {
                event.preventDefault();

                const fullName = form.fullName.value.trim();
                const age = parseInt(form.age.value, 10);
                const city = form.city.value.trim();
                const phone = form.phone.value.trim();
                const experienceYears = parseInt(form.experienceYears.value, 10);
                const mainSpecialization = form.mainSpecialization.value.trim();
                const pricePerSession = parseFloat(form.pricePerSession.value);
                const instagram = form.instagram.value.trim();
                const tiktok = form.tiktok.value.trim();
                const bio = form.bio.value.trim();

                // Collect training types
                const typeNodes = form.querySelectorAll('input[name="trainingTypes"]:checked');
                const trainingTypes = Array.from(typeNodes).map(input => input.value);

                if (!fullName || !city || !phone || isNaN(age) || isNaN(experienceYears) || isNaN(pricePerSession)) {
                    showMessage("אנא מלא את כל שדות החובה.", true);
                    return;
                }

                const file = fileInput && fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;

                function saveTrainer(profileImageBase64) {
                    const pending = getPendingTrainers();

                    // Get current user email for ownerEmail
                    const ownerEmail = (typeof getCurrentUserEmail === 'function' ? getCurrentUserEmail() : '') || '';

                    // Map form field names to existing field names for admin compatibility
                    const trainer = {
                        id: generateTrainerId(),
                        fullName: fullName,
                        age: age,
                        city: city,
                        phone: phone,
                        experience: experienceYears, // Map experienceYears -> experience
                        experienceYears: experienceYears,
                        specialization: mainSpecialization, // Map mainSpecialization -> specialization
                        mainSpecialization: mainSpecialization,
                        price: pricePerSession, // Map pricePerSession -> price
                        pricePerSession: pricePerSession,
                        instagram: instagram,
                        tiktok: tiktok,
                        bio: bio,
                        profileImageBase64: profileImageBase64 || "",
                        ratingSum: 0,
                        ratingCount: 0,
                        ownerEmail: ownerEmail.toLowerCase(),
                        // Optional fields that may be used by admin:
                        isOnline: false,
                        isForTeens: false,
                        isForWomen: false,
                        trainingTypes: trainingTypes,
                        createdAt: Date.now()
                    };

                    pending.push(trainer);
                    savePendingTrainers(pending);
                    form.reset();
                    showMessage("הבקשה נשלחה בהצלחה וממתינה לאישור מנהל.", false);
                }

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const base64 = e.target && e.target.result ? String(e.target.result) : "";
                        saveTrainer(base64);
                    };
                    reader.onerror = function () {
                        saveTrainer("");
                    };
                    reader.readAsDataURL(file);
                } else {
                    saveTrainer("");
                }
            });
        })();
    </script>
</body>
</html>
