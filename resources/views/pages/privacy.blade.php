<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>מדיניות פרטיות - FitMatch</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/site/style.css">
    @include('partials.schema-ld')
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        <h1 class="page-title">מדיניות פרטיות</h1>
        
        <div class="form-container">
            <div style="padding: 2rem;">
                <p style="color: var(--text-muted); margin-bottom: 2rem; text-align: center;">
                    עודכן לאחרונה: {{ date('d/m/Y') }}
                </p>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.8rem;">1. מבוא</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        FitMatch ("אנו", "שלנו", "האתר") מחויבת להגנה על הפרטיות שלך. מדיניות פרטיות זו מסבירה כיצד אנו אוספים, 
                        משתמשים, מגנים ומחשים את המידע האישי שלך בעת השימוש בפלטפורמה שלנו.
                    </p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.8rem;">2. איסוף מידע</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        אנו אוספים מידע שאתה מספק לנו ישירות בעת:
                    </p>
                    <ul style="color: var(--text-main); line-height: 1.8; margin-right: 2rem; margin-bottom: 1rem;">
                        <li>הרשמה לחשבון</li>
                        <li>מילוי טופס הרשמה כמאמן</li>
                        <li>יצירת קשר עם מאמנים</li>
                        <li>השארת ביקורות</li>
                        <li>יצירת קשר עם התמיכה</li>
                    </ul>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        המידע שאנו אוספים כולל: שם מלא, כתובת אימייל, מספר טלפון, מיקום, וכל מידע נוסף שאתה בוחר לשתף.
                    </p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.8rem;">3. שימוש במידע</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        אנו משתמשים במידע שאנו אוספים כדי:
                    </p>
                    <ul style="color: var(--text-main); line-height: 1.8; margin-right: 2rem; margin-bottom: 1rem;">
                        <li>לספק ולשפר את השירותים שלנו</li>
                        <li>לאפשר תקשורת בין מאמנים למתאמנים</li>
                        <li>לשלוח עדכונים והודעות חשובות</li>
                        <li>לשפר את חוויית המשתמש</li>
                        <li>לאכוף את תנאי השימוש שלנו</li>
                    </ul>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.8rem;">4. הגנה על מידע</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        אנו משתמשים באמצעי אבטחה טכנולוגיים וארגוניים מתקדמים כדי להגן על המידע האישי שלך מפני גישה לא מורשית, 
                        שימוש לרעה, חשיפה, שינוי או הרס. עם זאת, אין שיטה של העברה דרך האינטרנט או אחסון אלקטרוני שהיא מאובטחת 100%, 
                        ולכן אנו לא יכולים להבטיח אבטחה מוחלטת.
                    </p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.8rem;">5. שיתוף מידע</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        אנו לא מוכרים, משכירים או מעבירים את המידע האישי שלך לצדדים שלישיים ללא הסכמתך, למעט המקרים הבאים:
                    </p>
                    <ul style="color: var(--text-main); line-height: 1.8; margin-right: 2rem; margin-bottom: 1rem;">
                        <li>כאשר הדבר נדרש על פי חוק או צו שיפוטי</li>
                        <li>כדי להגן על זכויותינו, רכושנו או בטיחות המשתמשים</li>
                        <li>עם ספקי שירותים מהימנים שעוזרים לנו להפעיל את הפלטפורמה (תחת הסכמי סודיות)</li>
                    </ul>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.8rem;">6. זכויותיך</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        יש לך זכות:
                    </p>
                    <ul style="color: var(--text-main); line-height: 1.8; margin-right: 2rem; margin-bottom: 1rem;">
                        <li>לגשת למידע האישי שלך</li>
                        <li>לתקן מידע שגוי או לא מעודכן</li>
                        <li>למחוק את החשבון שלך ואת המידע הקשור אליו</li>
                        <li>להתנגד לעיבוד המידע שלך</li>
                        <li>לבקש העברת המידע שלך</li>
                    </ul>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        כדי לממש את זכויותיך, אנא צור קשר איתנו בכתובת: fitmatchcoil@gmail.com
                    </p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.8rem;">7. עוגיות (Cookies)</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        אנו משתמשים בעוגיות כדי לשפר את חוויית השימוש באתר. אתה יכול להגדיר את הדפדפן שלך לסרב לעוגיות, 
                        אך זה עלול להשפיע על תפקוד האתר.
                    </p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.8rem;">8. שינויים במדיניות</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        אנו עשויים לעדכן את מדיניות הפרטיות מעת לעת. כל שינוי יפורסם בדף זה עם תאריך העדכון. 
                        אנו ממליצים לך לבדוק את מדיניות הפרטיות מדי פעם.
                    </p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.8rem;">9. יצירת קשר</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        אם יש לך שאלות או חששות לגבי מדיניות הפרטיות שלנו, אנא צור קשר איתנו:
                    </p>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        <strong>אימייל:</strong> <a href="mailto:fitmatchcoil@gmail.com" style="color: var(--primary);">fitmatchcoil@gmail.com</a><br>
                        <strong>טלפון:</strong> <a href="tel:0527020113" style="color: var(--primary);">0527020113</a> | <a href="tel:0528381463" style="color: var(--primary);">0528381463</a>
                    </p>
                </div>
            </div>
        </div>
    </main>

    @include('partials.footer')
    @include('partials.cookie-consent')

    <script src="/site/script.js"></script>
    <script>
        if (typeof initTheme === 'function') initTheme();
        if (typeof initNavbarToggle === 'function') initNavbarToggle();
    </script>
</body>
</html>

