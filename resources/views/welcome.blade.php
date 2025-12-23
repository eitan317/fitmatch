<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>מציאת מאמני כושר - דף הבית</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/site/style.css?v={{ file_exists(public_path('site/style.css')) ? filemtime(public_path('site/style.css')) : time() }}">
    @include('partials.schema-ld')
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="particle particle-1"></div>
        <div class="particle particle-2"></div>
        <div class="particle particle-3"></div>
        <div class="wave wave-top"></div>
        
        <div class="hero card">
            <div class="hero-content">
                <h1>מצא את מאמן הכושר המושלם עבורך</h1>
                <p>הפלטפורמה המקשרת בין מאמני כושר מקצועיים לבין מתאמנים חדשים. התחל את המסע שלך לכושר טוב יותר היום!</p>
                <div class="hero-buttons">
                    @auth
                        <a href="/trainers" class="btn">מצא מאמן</a>
                        <a href="/register-trainer" class="btn btn-success">הירשם כמאמן</a>
                    @else
                        <a href="/trainers" class="btn">מצא מאמן</a>
                        <a href="/login" class="btn">התחבר כדי להתחיל</a>
                        <a href="{{ route('register') }}" class="btn btn-success">הירשם</a>
                    @endauth
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-graphic">
                    <div class="hero-circle hero-circle-main"></div>
                    <div class="hero-circle hero-circle-secondary"></div>
                    <div class="hero-dumbbell">💪</div>
                    <div class="hero-stat-card">
                        <div class="hero-stat-label">מאמנים פעילים</div>
                        <div class="hero-stat-number">+{{ $stats['active_trainers'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Choose Us Section -->
        <section class="why-choose-us">
            <h2 class="section-title">למה לבחור בנו?</h2>
            <div class="features-grid">
                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>מהירות</h3>
                    <p>מצא מאמן תוך דקות. חיפוש פשוט ומהיר עם תוצאות מיידיות.</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>איכות מוכחת</h3>
                    <p>כל המאמנים שלנו מאומתים ומקצועיים עם ניסיון מוכח.</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-th-large"></i>
                    </div>
                    <h3>מגוון רחב</h3>
                    <p>מאות מאמנים מקצועיים בכל סוגי האימונים והתמחויות.</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>תמיכה 24/7</h3>
                    <p>צוות תמיכה מקצועי זמין בכל שעה לעזור ולסייע.</p>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats-section">
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number" data-target="{{ $stats['active_trainers'] }}">0</div>
                    <div class="stat-label">מאמנים פעילים</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-number" data-target="{{ $stats['satisfied_trainees'] }}">0</div>
                    <div class="stat-label">מתאמנים מרוצים</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-number" data-target="{{ number_format($stats['average_rating'], 1) }}">0</div>
                    <div class="stat-label">דירוג ממוצע</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stat-number" data-target="{{ $stats['total_reviews'] }}">0</div>
                    <div class="stat-label">ביקורות</div>
                </div>
            </div>
        </section>

        <section class="how-it-works">
            <h2 class="section-title">איך זה עובד?</h2>
            <div class="cards">
                <div class="card step-card fade-in">
                    <div class="step-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>1. חיפוש מאמן</h3>
                    <p>מתאמנים מחפשים מאמן לפי אזור והתמחות. ניתן לסנן לפי עיר, סוג אימון ומחיר.</p>
                </div>
                <div class="card step-card fade-in">
                    <div class="step-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3>2. הרשמה למאמנים</h3>
                    <p>מאמנים נרשמים וממלאים פרופיל מפורט עם פרטי התמחות, ניסיון ומחירים.</p>
                </div>
                <div class="card step-card fade-in">
                    <div class="step-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>3. אישור איכותי</h3>
                    <p>המנהל מאשר מאמנים איכותיים בלבד, כך שתוכלו להיות בטוחים שאתם מקבלים שירות מקצועי.</p>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="testimonials-section">
            <h2 class="section-title">מה אומרים עלינו</h2>
            <div class="testimonials-grid">
                <div class="testimonial-card fade-in">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="testimonial-info">
                            <h4>דני כהן</h4>
                            <p>מתאמן</p>
                        </div>
                    </div>
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"מצאתי מאמן מעולה תוך יום! התהליך היה פשוט ומהיר, והמאמן מקצועי מאוד. ממליץ בחום!"</p>
                </div>
                <div class="testimonial-card fade-in">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="testimonial-info">
                            <h4>שרה לוי</h4>
                            <p>מתאמנת</p>
                        </div>
                    </div>
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"האתר עזר לי למצוא מאמנת מושלמת! המגוון רחב והמחירים הוגנים. אני מאוד מרוצה מהשירות."</p>
                </div>
                <div class="testimonial-card fade-in">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="testimonial-info">
                            <h4>מיכאל דוד</h4>
                            <p>מאמן</p>
                        </div>
                    </div>
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"כמאמן, הפלטפורמה הזו עזרה לי להגיע ללקוחות חדשים בקלות. ממליץ לכל מאמן!"</p>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="cta-content">
                <h2>מוכן להתחיל את המסע שלך?</h2>
                <p>הצטרף לאלפי מתאמנים ומאמנים שכבר משתמשים בפלטפורמה שלנו</p>
                <div class="cta-buttons">
                    @auth
                        <a href="/trainers" class="btn btn-large">מצא מאמן עכשיו</a>
                        <a href="/register-trainer" class="btn btn-large btn-outline-white">הירשם כמאמן</a>
                    @else
                        <a href="/trainers" class="btn btn-large">מצא מאמן עכשיו</a>
                        <a href="/login" class="btn btn-large">התחבר כדי להתחיל</a>
                        <a href="{{ route('register') }}" class="btn btn-large btn-outline-white">הירשם עכשיו</a>
                    @endauth
                </div>
            </div>
        </section>
    </main>

    @include('partials.footer')

    <script src="/site/script.js?v={{ file_exists(public_path('site/script.js')) ? filemtime(public_path('site/script.js')) : time() }}"></script>
    <script>
        initTheme && initTheme();
        initNavbarToggle && initNavbarToggle();
    </script>
</body>
</html>
