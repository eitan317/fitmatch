<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>בחר תכנית - FitMatch</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/site/style.css">
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

        @if(session('error'))
            <div class="form-message error" style="margin-bottom: 2rem;">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="form-container">
            <div style="text-align: center; margin-bottom: 2rem;">
                <h1 class="page-title" style="margin-bottom: 0.5rem;">
                    <i class="fas fa-crown"></i>
                    בחר תכנית
                </h1>
                <p style="font-size: 1.1rem; color: var(--text-muted);">
                    בחר את האופציה המתאימה לך
                </p>
            </div>

            <!-- Trainer Info Card -->
            <div style="background: linear-gradient(135deg, rgba(0, 217, 255, 0.1), rgba(74, 158, 255, 0.1)); padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem; border: 2px solid rgba(0, 217, 255, 0.3);">
                <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.2rem;">
                    <i class="fas fa-user"></i>
                    פרטי המאמן
                </h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
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
                </div>
            </div>

            <!-- Plan Options -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
                
                <!-- Pay Now Option -->
                <div style="background: var(--bg-card); border-radius: 20px; padding: 2.5rem; border: 2px solid rgba(40, 167, 69, 0.3); box-shadow: 0 8px 24px rgba(0,0,0,0.2); position: relative; transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 32px rgba(0,0,0,0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.2)';">
                    <div style="position: absolute; top: -15px; right: 20px; background: #28a745; color: white; padding: 0.5rem 1rem; border-radius: 999px; font-size: 0.85rem; font-weight: 600;">
                        מומלץ
                    </div>
                    
                    <div style="text-align: center; margin-bottom: 2rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">
                            <i class="fas fa-credit-card" style="color: #28a745;"></i>
                        </div>
                        <h2 style="font-size: 1.8rem; margin-bottom: 0.5rem; color: var(--text-main);">
                            תשלום עכשיו
                        </h2>
                        <div style="font-size: 2.5rem; font-weight: bold; color: #28a745; margin-bottom: 0.5rem;">
                            20 ₪
                        </div>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">
                            לחודש
                        </p>
                    </div>

                    <div style="margin-bottom: 2rem;">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="padding: 0.75rem 0; border-bottom: 1px solid rgba(148,163,184,0.2);">
                                <i class="fas fa-check" style="color: #28a745; margin-left: 0.75rem;"></i>
                                תשלום דרך Bit בלבד
                            </li>
                            <li style="padding: 0.75rem 0; border-bottom: 1px solid rgba(148,163,184,0.2);">
                                <i class="fas fa-check" style="color: #28a745; margin-left: 0.75rem;"></i>
                                הפרופיל שלך יוצג מיד לאחר אישור התשלום
                            </li>
                            <li style="padding: 0.75rem 0;">
                                <i class="fas fa-check" style="color: #28a745; margin-left: 0.75rem;"></i>
                                ללא תקופת המתנה
                            </li>
                        </ul>
                    </div>

                    <form action="{{ route('trainers.store-plan-choice') }}" method="POST">
                        @csrf
                        <input type="hidden" name="choice" value="pay_now">
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem; font-weight: 600;">
                            <i class="fas fa-arrow-left"></i>
                            בחר תשלום עכשיו
                        </button>
                    </form>
                </div>

                <!-- Trial Option -->
                @if(!$hasUsedTrial)
                <div style="background: var(--bg-card); border-radius: 20px; padding: 2.5rem; border: 2px solid rgba(0, 217, 255, 0.3); box-shadow: 0 8px 24px rgba(0,0,0,0.2); position: relative; transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 32px rgba(0,0,0,0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.2)';">
                    <div style="position: absolute; top: -15px; right: 20px; background: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 999px; font-size: 0.85rem; font-weight: 600;">
                        חינם
                    </div>
                    
                    <div style="text-align: center; margin-bottom: 2rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">
                            <i class="fas fa-hourglass-half" style="color: var(--primary);"></i>
                        </div>
                        <h2 style="font-size: 1.8rem; margin-bottom: 0.5rem; color: var(--text-main);">
                            חודש ניסיון
                        </h2>
                        <div style="font-size: 2.5rem; font-weight: bold; color: var(--primary); margin-bottom: 0.5rem;">
                            0 ₪
                        </div>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">
                            למשך 30 יום
                        </p>
                    </div>

                    <div style="margin-bottom: 2rem;">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="padding: 0.75rem 0; border-bottom: 1px solid rgba(148,163,184,0.2);">
                                <i class="fas fa-check" style="color: var(--primary); margin-left: 0.75rem;"></i>
                                חודש ניסיון חינם מלא
                            </li>
                            <li style="padding: 0.75rem 0; border-bottom: 1px solid rgba(148,163,184,0.2);">
                                <i class="fas fa-check" style="color: var(--primary); margin-left: 0.75rem;"></i>
                                הפרופיל שלך לא יוצג במהלך הניסיון
                            </li>
                            <li style="padding: 0.75rem 0; border-bottom: 1px solid rgba(148,163,184,0.2);">
                                <i class="fas fa-check" style="color: var(--primary); margin-left: 0.75rem;"></i>
                                לאחר 30 יום תתבקש לשלם 20₪ לחודש
                            </li>
                            <li style="padding: 0.75rem 0;">
                                <i class="fas fa-info-circle" style="color: #ffc107; margin-left: 0.75rem;"></i>
                                ניתן לבחור פעם אחת בלבד
                            </li>
                        </ul>
                    </div>

                    <form action="{{ route('trainers.store-plan-choice') }}" method="POST">
                        @csrf
                        <input type="hidden" name="choice" value="trial">
                        <button type="submit" class="btn" style="width: 100%; padding: 1rem; font-size: 1.1rem; font-weight: 600; background: rgba(0, 217, 255, 0.1); border: 2px solid var(--primary); color: var(--primary);">
                            <i class="fas fa-arrow-left"></i>
                            בחר חודש ניסיון
                        </button>
                    </form>
                </div>
                @else
                <!-- Trial Already Used Message -->
                <div style="background: rgba(148, 163, 184, 0.1); border-radius: 20px; padding: 2.5rem; border: 2px solid rgba(148, 163, 184, 0.3); text-align: center; display: flex; flex-direction: column; justify-content: center;">
                    <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h3 style="color: var(--text-muted); margin-bottom: 0.5rem;">
                        חודש ניסיון לא זמין
                    </h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">
                        כבר ניצלת את חודש הניסיון. יש לשלם כדי להמשיך.
                    </p>
                </div>
                @endif
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

