<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ברוכים הבאים - FitMatch</title>
    @include('partials.adsense-verification')
    @include('partials.adsense')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/site/style.css">
    @include('partials.schema-ld')
    <style>
        .welcome-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .welcome-card {
            background: var(--bg-card);
            border-radius: 24px;
            padding: 3rem;
            max-width: 600px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(0, 217, 255, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .welcome-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
        }
        
        .welcome-icon {
            font-size: 5rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .welcome-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--text-main) 0%, var(--primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .welcome-subtitle {
            font-size: 1.2rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .free-badge {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 999px;
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0, 217, 255, 0.4);
        }
        
        .welcome-features {
            text-align: right;
            margin: 2rem 0;
        }
        
        .welcome-feature {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 1rem;
            padding: 0.75rem 0;
            color: var(--text-main);
        }
        
        .welcome-feature i {
            color: var(--primary);
            font-size: 1.5rem;
        }
        
        .welcome-message {
            background: rgba(0, 217, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem 0;
            border-right: 4px solid var(--primary);
        }
        
        .welcome-message p {
            color: var(--text-main);
            font-size: 1rem;
            line-height: 1.8;
            margin: 0;
        }
        
        .welcome-actions {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        @media (max-width: 768px) {
            .welcome-card {
                padding: 2rem 1.5rem;
            }
            
            .welcome-title {
                font-size: 2rem;
            }
            
            .welcome-icon {
                font-size: 4rem;
            }
            
            .welcome-actions {
                flex-direction: column;
            }
            
            .welcome-actions .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        <div class="welcome-container">
            <div class="welcome-card">
                <div class="welcome-icon">
                    <i class="fas fa-heart"></i>
                </div>
                
                <h1 class="welcome-title">ברוכים הבאים ל-FitMatch!</h1>
                
                <div class="free-badge">
                    <i class="fas fa-gift"></i> הכל חינם!
                </div>
                
                <p class="welcome-subtitle">
                    תודה שנרשמת! הבקשה שלך נשלחה לאישור מנהל המערכת.
                </p>
                
                <div class="welcome-message">
                    <p>
                        <strong>🎉 כל השירותים שלנו חינמיים לחלוטין!</strong><br>
                        אין תשלומים, אין מנויים, אין עלויות נסתרות.<br>
                        פשוט ממתינים לאישור המנהל ואתה בתוך המערכת!
                    </p>
                </div>
                
                <div class="welcome-features">
                    <div class="welcome-feature">
                        <span>פרופיל מקצועי מלא</span>
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="welcome-feature">
                        <span>חשיפה למשתמשים</span>
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="welcome-feature">
                        <span>ניהול ביקורות ודירוגים</span>
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="welcome-feature">
                        <span>כל זה חינם - ללא תשלום!</span>
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                
                <div class="welcome-actions">
                    <a href="{{ route('trainers.index') }}" class="btn btn-primary">
                        <i class="fas fa-dumbbell"></i>
                        צפה במאמנים אחרים
                    </a>
                    <a href="{{ route('welcome') }}" class="btn btn-secondary">
                        <i class="fas fa-home"></i>
                        חזור לדף הבית
                    </a>
                </div>
            </div>
        </div>
    </main>

    @include('partials.footer')
    @include('partials.cookie-consent')
    @include('partials.accessibility-panel')

    <script src="/site/script.js"></script>
    <script>
        initTheme && initTheme();
        initNavbarToggle && initNavbarToggle();
    </script>
</body>
</html>

