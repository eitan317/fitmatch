<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>תנאי שימוש - FitMatch</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/site/style.css">
    @include('partials.schema-ld')
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        <h1 class="page-title">תנאי שימוש</h1>
        
        <div class="form-container">
            <div style="padding: 2rem;">
                <p style="color: var(--text-muted); margin-bottom: 2rem; text-align: center;">
                    עודכן לאחרונה: {{ date('d/m/Y') }}
                </p>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.8rem;">1. הסכמה לתנאים</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        על ידי גישה ושימוש בפלטפורמה FitMatch, אתה מסכים להיות כפוף לתנאי השימוש הללו. 
                        אם אינך מסכים עם כל התנאים, אנא אל תשתמש בפלטפורמה.
                    </p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.8rem;">2. שימוש בפלטפורמה</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        אתה רשאי להשתמש בפלטפורמה למטרות חוקיות בלבד. אתה מתחייב:
                    </p>
                    <ul style="color: var(--text-main); line-height: 1.8; margin-right: 2rem; margin-bottom: 1rem;">
                        <li>לא להשתמש בפלטפורמה למטרות בלתי חוקיות או לא מורשות</li>
                        <li>לא להפר זכויות יוצרים, סימני מסחר או זכויות קניין רוחני אחרות</li>
                        <li>לא להעלות תוכן מזיק, פוגעני או בלתי חוקי</li>
                        <li>לא לנסות לגשת ללא הרשאה למערכות או למידע של משתמשים אחרים</li>
                        <li>לא להשתמש ב-bots, scripts או כלים אוטומטיים אחרים</li>
                    </ul>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.8rem;">3. הרשמה וחשבון</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        כדי להשתמש בחלק מהשירותים, תצטרך ליצור חשבון. אתה מתחייב:
                    </p>
                    <ul style="color: var(--text-main); line-height: 1.8; margin-right: 2rem; margin-bottom: 1rem;">
                        <li>לספק מידע מדויק, מעודכן ומלא</li>
                        <li>לשמור על סודיות הסיסמה שלך</li>
                        <li>להיות אחראי לכל הפעילות שמתבצעת בחשבון שלך</li>
                        <li>להודיע לנו מיד על כל שימוש לא מורשה בחשבון שלך</li>
                    </ul>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.8rem;">4. תוכן משתמשים</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        אתה שומר על כל הזכויות בתוכן שאתה מעלה לפלטפורמה. עם זאת, על ידי העלאת תוכן, 
                        אתה מעניק לנו רישיון לא בלעדי, חינמי, עולמי ובלתי ניתן לביטול להשתמש, לשכפל, 
                        לשנות ולהציג את התוכן שלך בפלטפורמה.
                    </p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.8rem;">5. אחריות</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        הפלטפורמה מסופקת "כפי שהיא" ללא כל אחריות מפורשת או משתמעת. אנו לא מתחייבים כי:
                    </p>
                    <ul style="color: var(--text-main); line-height: 1.8; margin-right: 2rem; margin-bottom: 1rem;">
                        <li>הפלטפורמה תהיה זמינה ללא הפרעות או שגיאות</li>
                        <li>הפלטפורמה תהיה בטוחה מפני וירוסים או קודים מזיקים</li>
                        <li>התוצאות המתקבלות מהשימוש בפלטפורמה יהיו מדויקות או אמינות</li>
                    </ul>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        אנו לא נהיה אחראים לכל נזק ישיר, עקיף, מקרי או תוצאתי הנובע מהשימוש או אי-השימוש בפלטפורמה.
                    </p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.8rem;">6. זכויות יוצרים</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        כל התוכן בפלטפורמה, כולל טקסטים, גרפיקה, לוגואים, תמונות וקוד, הוא רכוש של FitMatch 
                        או של בעלי הרישיון שלו ומוגן על ידי חוקי זכויות יוצרים. אסור לשכפל, להפיץ או להשתמש בתוכן 
                        ללא רשות מפורשת בכתב.
                    </p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.8rem;">7. קישורים לאתרים חיצוניים</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        הפלטפורמה עשויה להכיל קישורים לאתרים חיצוניים. אנו לא אחראים לתוכן, מדיניות הפרטיות 
                        או הפרקטיקות של אתרים חיצוניים אלה. אנו ממליצים לך לקרוא את תנאי השימוש ומדיניות הפרטיות 
                        של כל אתר חיצוני שאתה מבקר בו.
                    </p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.8rem;">8. שינויים בתנאים</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        אנו שומרים לעצמנו את הזכות לעדכן או לשנות את תנאי השימוש בכל עת. שינויים ייכנסו לתוקף 
                        מיד לאחר פרסומם באתר. המשך השימוש בפלטפורמה לאחר שינויים מהווה הסכמה לתנאים המעודכנים.
                    </p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.8rem;">9. ביטול חשבון</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        אתה רשאי לבטל את חשבונך בכל עת. אנו שומרים לעצמנו את הזכות להשעות או לבטל חשבונות 
                        שמפרים את תנאי השימוש ללא הודעה מוקדמת.
                    </p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.8rem;">10. חוק שולט</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        תנאי שימוש אלה נשלטים על ידי חוקי מדינת ישראל. כל מחלוקת הנובעת מתנאים אלה תיפתר 
                        בבתי המשפט המוסמכים בישראל.
                    </p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.8rem;">11. יצירת קשר</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 1rem;">
                        אם יש לך שאלות לגבי תנאי השימוש, אנא צור קשר איתנו:
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
    @include('partials.accessibility-panel')

    <script src="/site/script.js"></script>
    <script>
        if (typeof initTheme === 'function') initTheme();
        if (typeof initNavbarToggle === 'function') initNavbarToggle();
    </script>
</body>
</html>

