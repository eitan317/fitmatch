<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>עריכת פרופיל מאמן</title>
    <link rel="stylesheet" href="/site/style.css">
    @include('partials.schema-ld')
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        <h1>עריכת פרופיל מאמן</h1>
        <p>עדכן את הפרטים שלך ושמור.</p>

        @php
            $currentTrainer = \App\Models\Trainer::where('owner_email', Auth::user()->email)->first();
        @endphp

        @if($currentTrainer)
            <div class="form-message {{ $currentTrainer->status === 'active' ? 'success' : ($currentTrainer->status === 'pending_payment' ? 'error' : 'warning') }}" style="margin-bottom: 2rem; padding: 1rem; border-radius: 10px;">
                @if($currentTrainer->status === 'trial')
                    <h3 style="margin-top: 0;"><i class="fas fa-hourglass-half"></i> חודש ניסיון</h3>
                    <p>אתה כעת בחודש ניסיון. לאחר {{ $currentTrainer->trial_ends_at ? $currentTrainer->trial_ends_at->diffForHumans() : '30 יום' }} תתבקש לשלם 20₪ בביט.</p>
                    @if($currentTrainer->trial_ends_at)
                        <p><strong>תאריך סיום ניסיון:</strong> {{ $currentTrainer->trial_ends_at->format('d/m/Y') }}</p>
                    @endif
                @elseif($currentTrainer->status === 'pending_payment')
                    <h3 style="margin-top: 0;"><i class="fas fa-money-bill-wave"></i> נדרש תשלום</h3>
                    <p><strong>יש לשלם 20₪ בביט כדי להמשיך להציג את הפרופיל שלך באתר.</strong></p>
                    <div style="background: rgba(255,255,255,0.1); padding: 1rem; border-radius: 8px; margin-top: 1rem;">
                        <p style="margin: 0.5rem 0;"><strong>סכום:</strong> 20₪</p>
                        <p style="margin: 0.5rem 0;"><strong>אמצעי תשלום:</strong> Bit בלבד</p>
                        <p style="margin: 0.5rem 0;"><strong>מספר Bit:</strong> 0527020113</p>
                        <p style="margin-top: 1rem; font-size: 0.9rem; opacity: 0.9;">לאחר התשלום, שלח הודעה למנהל המערכת לאישור.</p>
                    </div>
                @elseif($currentTrainer->status === 'blocked')
                    <h3 style="margin-top: 0;"><i class="fas fa-ban"></i> חשבון חסום</h3>
                    <p>החשבון שלך נחסם. אנא צור קשר עם מנהל המערכת למידע נוסף.</p>
                @elseif($currentTrainer->status === 'active')
                    <h3 style="margin-top: 0;"><i class="fas fa-check-circle"></i> חשבון פעיל</h3>
                    <p>החשבון שלך פעיל והפרופיל שלך מוצג באתר.</p>
                    @if($currentTrainer->last_payment_at)
                        <p><strong>תאריך תשלום אחרון:</strong> {{ $currentTrainer->last_payment_at->format('d/m/Y') }}</p>
                    @endif
                @endif
            </div>
        @endif

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

    <script src="/site/script.js"></script>
    <script>
        initTheme && initTheme();
        initNavbarToggle && initNavbarToggle();
        initEditTrainerPage && initEditTrainerPage();
    </script>
</body>
</html>

