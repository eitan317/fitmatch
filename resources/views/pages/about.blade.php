<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>אודותינו - FitMatch</title>
    @include('partials.adsense-verification')
    @include('partials.adsense')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/site/style.css">
    @include('partials.schema-ld')
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        <h1 class="page-title">אודותינו</h1>
        
        <div class="form-container">
            <div style="padding: 2rem;">
                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 2rem;">מי אנחנו?</h2>
                    <p style="color: var(--text-main); line-height: 1.8; font-size: 1.1rem; margin-bottom: 1.5rem;">
                        FitMatch היא הפלטפורמה המובילה בישראל לחיבור בין מאמני כושר מקצועיים למתאמנים שמחפשים את המאמן המושלם עבורם. 
                        אנו מאמינים שכל אחד יכול להגיע לכושר גופני מעולה עם המאמן הנכון, ולכן יצרנו פלטפורמה שמקלה על מציאת המאמן המתאים ביותר.
                    </p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 2rem;">המטרה שלנו</h2>
                    <p style="color: var(--text-main); line-height: 1.8; font-size: 1.1rem; margin-bottom: 1.5rem;">
                        המטרה שלנו היא להפוך את מציאת מאמן הכושר לחוויה פשוטה, נוחה ואמינה. 
                        אנו מספקים פלטפורמה שבה מאמנים מקצועיים יכולים להציג את השירותים שלהם, 
                        ומתאמנים יכולים למצוא את המאמן המתאים ביותר לצרכים שלהם, התקציב שלהם והמיקום שלהם.
                    </p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 2rem;">למה לבחור בנו?</h2>
                    <div class="features-slider-container" id="aboutFeaturesSlider">
                        <div class="features-slider-track">
                            <div class="feature-card" style="background: rgba(220, 38, 38, 0.1); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-soft);">
                                <div style="font-size: 2.5rem; margin-bottom: 1rem;">🎯</div>
                                <h3 style="color: var(--primary); margin-bottom: 0.5rem;">מאמנים מאומתים</h3>
                                <p style="color: var(--text-muted); line-height: 1.6;">
                                    כל המאמנים בפלטפורמה שלנו עוברים תהליך אימות מקצועי כדי להבטיח איכות ושירות מעולה.
                                </p>
                            </div>
                            <div class="feature-card" style="background: rgba(220, 38, 38, 0.1); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-soft);">
                                <div style="font-size: 2.5rem; margin-bottom: 1rem;">💪</div>
                                <h3 style="color: var(--primary); margin-bottom: 0.5rem;">מגוון רחב</h3>
                                <p style="color: var(--text-muted); line-height: 1.6;">
                                    מאות מאמנים מקצועיים במגוון תחומים: כוח, אירובי, יוגה, פילאטיס ועוד.
                                </p>
                            </div>
                            <div class="feature-card" style="background: rgba(220, 38, 38, 0.1); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-soft);">
                                <div style="font-size: 2.5rem; margin-bottom: 1rem;">⭐</div>
                                <h3 style="color: var(--primary); margin-bottom: 0.5rem;">ביקורות אמיתיות</h3>
                                <p style="color: var(--text-muted); line-height: 1.6;">
                                    קראו ביקורות אמיתיות ממתאמנים אחרים כדי לבחור את המאמן המתאים לכם.
                                </p>
                            </div>
                            <div class="feature-card" style="background: rgba(220, 38, 38, 0.1); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-soft);">
                                <div style="font-size: 2.5rem; margin-bottom: 1rem;">🔒</div>
                                <h3 style="color: var(--primary); margin-bottom: 0.5rem;">אבטחה ואמינות</h3>
                                <p style="color: var(--text-muted); line-height: 1.6;">
                                    הפלטפורמה שלנו מאובטחת ומאומתת, כך שתוכלו להתאמן בביטחון מלא.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 2rem;">החזון שלנו</h2>
                    <p style="color: var(--text-main); line-height: 1.8; font-size: 1.1rem;">
                        החזון שלנו הוא ליצור קהילה חזקה של מאמנים ומתאמנים בישראל, 
                        שבה כל אחד יכול למצוא את המאמן המושלם עבורו ולהגיע ליעדים הכושר שלו. 
                        אנו שואפים להיות הפלטפורמה המובילה בישראל לחיבור בין מאמני כושר למתאמנים.
                    </p>
                </div>

                <div style="text-align: center; margin-top: 3rem;">
                    <a href="{{ route('register') }}" class="btn btn-primary" style="margin: 0.5rem;">הצטרף אלינו</a>
                    <a href="{{ route('contact') }}" class="btn btn-outline" style="margin: 0.5rem;">צור קשר</a>
                </div>
            </div>
        </div>
    </main>

    @include('partials.footer')
    @include('partials.cookie-consent')
    @include('partials.accessibility-panel')

    <script src="/site/script.js"></script>
    <script>
        if (typeof initTheme === 'function') initTheme();
        if (typeof initNavbarToggle === 'function') initNavbarToggle();
        
        // Initialize about page slider
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof initMobileSlider === 'function') {
                initMobileSlider('#aboutFeaturesSlider', { cardsPerView: 1 });
            }
        });
    </script>
</body>
</html>

