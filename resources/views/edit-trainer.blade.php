<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>עריכת פרופיל מאמן</title>
    <link rel="stylesheet" href="{{ asset('site/style.css') }}">
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        <h1>עריכת פרופיל מאמן</h1>
        <p>עדכן את הפרטים שלך ושמור.</p>

        <form id="edit-trainer-form">
            <div class="form-group">
                <label for="edit-fullName">שם מלא *</label>
                <input type="text" id="edit-fullName" required>
            </div>

            <div class="form-group">
                <label for="edit-age">גיל *</label>
                <input type="number" id="edit-age" min="16" max="80" required>
            </div>

            <div class="form-group">
                <label for="edit-city">עיר *</label>
                <input type="text" id="edit-city" required>
            </div>

            <div class="form-group">
                <label for="edit-phone">טלפון *</label>
                <input type="tel" id="edit-phone" required placeholder="050-1234567">
            </div>

            <div class="form-group">
                <label for="edit-experienceYears">שנות ניסיון *</label>
                <input type="number" id="edit-experienceYears" min="0" max="60" required>
            </div>

            <div class="form-group">
                <label for="edit-mainSpecialization">התמחות עיקרית *</label>
                <input type="text" id="edit-mainSpecialization" required>
            </div>

            <div class="form-group">
                <label for="edit-pricePerSession">מחיר לאימון בודד (ש"ח) *</label>
                <input type="number" id="edit-pricePerSession" min="0" required>
            </div>

            <div class="form-group">
                <label for="edit-instagram">אינסטגרם (אופציונלי)</label>
                <input type="text" id="edit-instagram">
            </div>

            <div class="form-group">
                <label for="edit-tiktok">טיקטוק (אופציונלי)</label>
                <input type="text" id="edit-tiktok">
            </div>

            <div class="form-group">
                <label for="edit-bio">תיאור קצר (אופציונלי)</label>
                <textarea id="edit-bio" rows="4" placeholder="ספר קצת עליך, סגנון האימונים שלך והניסיון שלך."></textarea>
            </div>

            <button type="submit" class="btn btn-primary">שמור שינויים</button>
            <div id="edit-trainer-message" class="form-message"></div>
        </form>
    </main>

    <script src="{{ asset('site/script.js') }}"></script>
    <script>
        initTheme && initTheme();
        initNavbarToggle && initNavbarToggle();
        initEditTrainerPage && initEditTrainerPage();
    </script>
</body>
</html>

