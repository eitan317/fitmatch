<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>חודש ניסיון - FitMatch</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/site/style.css">
    <style>
        /* Mobile Responsive Styles for Trial Info Page */
        @media (max-width: 768px) {
            /* Info cards padding */
            .trial-info-card {
                padding: 1.5rem !important;
            }
            
            /* Price size */
            .trial-price {
                font-size: 1.25rem !important;
            }
            
            /* Phone number */
            .trial-phone {
                font-size: 1.1rem !important;
            }
        }
        
        @media (max-width: 480px) {
            /* Info cards padding - even smaller */
            .trial-info-card {
                padding: 1rem !important;
            }
            
            /* Price size */
            .trial-price {
                font-size: 1.1rem !important;
            }
            
            /* Phone number */
            .trial-phone {
                font-size: 1rem !important;
            }
            
            /* Page title */
            .page-title {
                font-size: 1.5rem !important;
            }
            
            /* Description text */
            .form-container p[style*="font-size: 1.1rem"] {
                font-size: 1rem !important;
            }
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        @if(session('success'))
            <div class="form-message success" style="margin-bottom: 2rem;">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="form-container">
            <div class="form-card">
                <h1 class="page-title" style="text-align: center; margin-bottom: 1rem;">
                    <i class="fas fa-hourglass-half"></i>
                    חודש ניסיון חינם
                </h1>

                <div class="trial-info-card" style="background: linear-gradient(135deg, rgba(0, 217, 255, 0.1), rgba(74, 158, 255, 0.1)); padding: 2rem; border-radius: 16px; margin-bottom: 2rem; border: 2px solid rgba(0, 217, 255, 0.3);">
                    <h2 style="color: var(--primary); margin-bottom: 1rem;">
                        <i class="fas fa-user"></i>
                        פרטי המאמן
                    </h2>
                    <div style="display: grid; gap: 1rem;">
                        <div>
                            <strong>שם מלא:</strong> {{ $trainer->full_name }}
                        </div>
                        @if($trainer->phone)
                            <div>
                                <strong>מספר טלפון:</strong> {{ $trainer->phone }}
                            </div>
                        @endif
                        @if($trainer->city)
                            <div>
                                <strong>עיר:</strong> {{ $trainer->city }}
                            </div>
                        @endif
                        @if($trainer->trial_started_at)
                            <div>
                                <strong>תאריך התחלת ניסיון:</strong> {{ $trainer->trial_started_at->format('d/m/Y') }}
                            </div>
                        @endif
                        @if($trainer->trial_ends_at)
                            <div>
                                <strong>תאריך סיום ניסיון:</strong> {{ $trainer->trial_ends_at->format('d/m/Y') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="trial-info-card" style="background: rgba(255, 193, 7, 0.1); padding: 2rem; border-radius: 16px; margin-bottom: 2rem; border: 2px solid rgba(255, 193, 7, 0.3);">
                    <h2 style="color: #ffc107; margin-bottom: 1rem;">
                        <i class="fas fa-gift"></i>
                        חודש ניסיון חינם
                    </h2>
                    <p style="font-size: 1.1rem; margin-bottom: 1rem;">
                        אתה כעת בחודש ניסיון חינם למשך <strong>30 יום</strong>!
                    </p>
                    <p>
                        במהלך חודש הניסיון הפרופיל שלך לא יוצג באתר. לאחר סיום חודש הניסיון תתבקש לשלם כדי להמשיך להציג את הפרופיל שלך.
                    </p>
                </div>

                <div class="trial-info-card" style="background: rgba(40, 167, 69, 0.1); padding: 2rem; border-radius: 16px; margin-bottom: 2rem; border: 2px solid rgba(40, 167, 69, 0.3);">
                    <h2 style="color: #28a745; margin-bottom: 1rem;">
                        <i class="fas fa-money-bill-wave"></i>
                        תשלום לאחר חודש הניסיון
                    </h2>
                    <div class="trial-price" style="font-size: 1.5rem; font-weight: bold; color: #28a745; margin-bottom: 1rem;">
                        20 ₪ לחודש
                    </div>
                    <p style="margin-bottom: 1.5rem;">
                        לאחר סיום חודש הניסיון, יש לשלם <strong>20 ₪ לחודש</strong> דרך Bit בלבד.
                    </p>
                    
                    <div style="background: rgba(255, 255, 255, 0.1); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.2);">
                        <h3 style="margin-bottom: 1rem; color: var(--primary);">
                            <i class="fas fa-mobile-alt"></i>
                            פרטי תשלום
                        </h3>
                        <div style="display: grid; gap: 1rem; font-size: 1.1rem;">
                            <div>
                                <strong>אמצעי תשלום:</strong> Bit בלבד
                            </div>
                            <div>
                                <strong>מספר Bit:</strong> 
                                <span class="trial-phone" style="color: var(--primary); font-weight: bold; font-size: 1.2rem;">0527020113</span>
                            </div>
                            <div>
                                <strong>סכום:</strong> 20 ₪ לחודש
                            </div>
                        </div>
                    </div>
                </div>

                <div class="trial-info-card" style="background: rgba(148, 163, 184, 0.1); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid rgba(148, 163, 184, 0.3);">
                    <h3 style="margin-bottom: 1rem;">
                        <i class="fas fa-info-circle"></i>
                        מה קורה עכשיו?
                    </h3>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li style="padding: 0.5rem 0;">
                            <i class="fas fa-check" style="color: #28a745; margin-left: 0.5rem;"></i>
                            הפרופיל שלך נרשם בהצלחה במערכת
                        </li>
                        <li style="padding: 0.5rem 0;">
                            <i class="fas fa-check" style="color: #28a745; margin-left: 0.5rem;"></i>
                            אתה בחודש ניסיון חינם למשך 30 יום
                        </li>
                        <li style="padding: 0.5rem 0;">
                            <i class="fas fa-check" style="color: #28a745; margin-left: 0.5rem;"></i>
                            לאחר סיום חודש הניסיון תתבקש לשלם 20 ₪ דרך Bit
                        </li>
                        <li style="padding: 0.5rem 0;">
                            <i class="fas fa-check" style="color: #28a745; margin-left: 0.5rem;"></i>
                            לאחר אישור התשלום על ידי מנהל המערכת, הפרופיל שלך יוצג באתר
                        </li>
                    </ul>
                </div>

                <div style="text-align: center;">
                    <a href="{{ route('trainers.index') }}" class="btn btn-primary" style="margin: 0.5rem;">
                        <i class="fas fa-home"></i>
                        חזור לדף הבית
                    </a>
                    <a href="{{ route('trainers.edit', $trainer) }}" class="btn btn-secondary" style="margin: 0.5rem;">
                        <i class="fas fa-edit"></i>
                        ערוך פרופיל
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script src="/site/script.js"></script>
    <script>
        if (typeof initTheme === 'function') initTheme();
        if (typeof initNavbarToggle === 'function') initNavbarToggle();
    </script>
</body>
</html>

