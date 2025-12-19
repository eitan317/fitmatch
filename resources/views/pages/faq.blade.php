<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>שאלות נפוצות - FitMatch</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('site/style.css') }}">
    <style>
        .faq-item {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            margin-bottom: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .faq-question {
            padding: 1.5rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-main);
            font-weight: 600;
            font-size: 1.1rem;
        }
        .faq-question:hover {
            background: rgba(220, 38, 38, 0.1);
        }
        .faq-answer {
            padding: 0 1.5rem;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            color: var(--text-muted);
            line-height: 1.8;
        }
        .faq-item.active .faq-answer {
            max-height: 500px;
            padding: 0 1.5rem 1.5rem 1.5rem;
        }
        .faq-icon {
            transition: transform 0.3s ease;
        }
        .faq-item.active .faq-icon {
            transform: rotate(180deg);
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        <h1 class="page-title">שאלות נפוצות</h1>
        
        <div class="form-container">
            <div style="padding: 2rem;">
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>איך אני מוצא מאמן כושר?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>לאחר הרשמה לאתר, תוכל לגלוש בין מאמני הכושר הזמינים. תוכל לסנן לפי מיקום, סוג אימון, מחיר ועוד. 
                        כל מאמן כולל פרופיל מפורט עם ניסיון, התמחויות, ביקורות ומחירים.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>איך אני נרשם כמאמן כושר?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>לאחר הרשמה לאתר, תוכל למלא טופס הרשמה כמאמן. הטופס כולל פרטים אישיים, ניסיון, התמחויות, 
                        תמחור ופרטים נוספים. לאחר מילוי הטופס, הבקשה תישלח לאישור מנהל המערכת.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>כמה עולה שירות מאמן כושר?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>המחירים משתנים בין מאמנים שונים ותלויים בניסיון, סוג האימון ומיקום. 
                        כל מאמן מציג את המחירים שלו בפרופיל. תוכל לסנן מאמנים לפי טווח מחירים שמתאים לך.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>איך אני יוצר קשר עם מאמן?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>לאחר מציאת מאמן שמעניין אותך, תוכל לראות את פרטי הקשר שלו בפרופיל (טלפון, אימייל, 
                        רשתות חברתיות). תוכל ליצור קשר ישירות עם המאמן כדי לתאם אימון.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>האם המאמנים מאומתים?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>כן, כל המאמנים בפלטפורמה עוברים תהליך אימות מקצועי. אנו בודקים את ההכשרות, 
                        הניסיון והפרטים המקצועיים של כל מאמן לפני אישורו בפלטפורמה.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>איך אני יכול להשאיר ביקורת על מאמן?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>לאחר אימון עם מאמן, תוכל להשאיר ביקורת ודירוג בפרופיל המאמן. 
                        הביקורות עוזרות למתאמנים אחרים לבחור את המאמן המתאים להם.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>האם יש דמי הרשמה?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>ההרשמה לאתר היא חינמית. אין דמי הרשמה למתאמנים. 
                        מאמנים יכולים להירשם ולפרסם את השירותים שלהם ללא עלות.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>איך אני יכול לבטל או לשנות אימון?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>ביטול או שינוי אימון מתבצע ישירות עם המאמן. 
                        אנו ממליצים לתאם עם המאמן מראש על מדיניות הביטולים והשינויים.</p>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border-soft);">
                    <p style="color: var(--text-muted); margin-bottom: 1rem;">לא מצאת את התשובה שחיפשת?</p>
                    <a href="{{ route('contact') }}" class="btn btn-primary">צור קשר</a>
                </div>
            </div>
        </div>
    </main>

    @include('partials.footer')

    <script src="{{ asset('site/script.js') }}"></script>
    <script>
        if (typeof initTheme === 'function') initTheme();
        if (typeof initNavbarToggle === 'function') initNavbarToggle();

        function toggleFaq(element) {
            const faqItem = element.closest('.faq-item');
            const isActive = faqItem.classList.contains('active');
            
            // Close all FAQ items
            document.querySelectorAll('.faq-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Open clicked item if it wasn't active
            if (!isActive) {
                faqItem.classList.add('active');
            }
        }
    </script>
</body>
</html>

