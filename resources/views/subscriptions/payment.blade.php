<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>תשלום השתתפות חודשית - FitMatch</title>
    <link rel="stylesheet" href="/site/style.css">
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        <h1>תשלום השתתפות חודשית</h1>

        @if(session('error'))
            <div class="form-message error">{{ session('error') }}</div>
        @endif

        <div class="form-card" style="max-width: 600px; margin: 2rem auto;">
            <h2>פרטי התכנית</h2>
            <p><strong>תכנית:</strong> {{ $subscription->plan->name }}</p>
            <p><strong>מחיר:</strong> ₪{{ number_format($subscription->plan->price, 0) }} לחודש</p>
            <p><strong>תוקף:</strong> עד {{ $subscription->expires_at->format('d/m/Y') }}</p>

            <!-- הודעה חשובה על תנאי ההשתתפות -->
            <div style="background: rgba(34, 197, 94, 0.1); padding: 1.5rem; border-radius: 0.5rem; margin: 1.5rem 0; border: 2px solid rgba(34, 197, 94, 0.3);">
                <h3 style="margin-top: 0; margin-bottom: 1rem; color: var(--text-main); font-size: 1.1rem;">
                    תנאי השתתפות חודשית בפלטפורמה
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0; color: var(--text-main);">
                    <li style="padding: 0.5rem 0; display: flex; align-items: flex-start;">
                        <span style="color: rgba(34, 197, 94, 1); margin-left: 0.5rem; font-weight: bold; font-size: 1.2rem;">✓</span>
                        <span><strong>אין התחייבות:</strong> ההשתתפות החודשית אינה כרוכה בהתחייבות לטווח ארוך</span>
                    </li>
                    <li style="padding: 0.5rem 0; display: flex; align-items: flex-start;">
                        <span style="color: rgba(34, 197, 94, 1); margin-left: 0.5rem; font-weight: bold; font-size: 1.2rem;">✓</span>
                        <span><strong>תשלום מרצון:</strong> התשלום מתבצע מרצונך החופשי בלבד</span>
                    </li>
                    <li style="padding: 0.5rem 0; display: flex; align-items: flex-start;">
                        <span style="color: rgba(34, 197, 94, 1); margin-left: 0.5rem; font-weight: bold; font-size: 1.2rem;">✓</span>
                        <span><strong>אין חיוב אוטומטי:</strong> לא יבוצע חיוב אוטומטי - כל תשלום דורש אישור שלך</span>
                    </li>
                    <li style="padding: 0.5rem 0; display: flex; align-items: flex-start;">
                        <span style="color: rgba(34, 197, 94, 1); margin-left: 0.5rem; font-weight: bold; font-size: 1.2rem;">✓</span>
                        <span><strong>הפסקה חופשית:</strong> ניתן להפסיק את ההשתתפות החודשית בכל עת ללא כל עמלה או תנאי</span>
                    </li>
                </ul>
            </div>

            <form action="{{ route('subscriptions.payment.process', $subscription) }}" method="POST" style="margin-top: 2rem;">
                @csrf
                
                <div class="form-group">
                    <label>אמצעי תשלום</label>
                    <select name="payment_method" required style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid rgba(148,163,184,0.3); background: var(--bg-card); color: var(--text-main);">
                        <option value="credit_card">כרטיס אשראי</option>
                        <option value="paypal">PayPal</option>
                        <option value="bit">ביט</option>
                    </select>
                </div>

                <div style="background: rgba(220,38,38,0.1); padding: 1rem; border-radius: 0.5rem; margin: 1rem 0; border: 1px solid rgba(220,38,38,0.3);">
                    <p style="margin: 0; font-size: 0.9rem; color: var(--text-muted);">
                        <strong>הערה:</strong> זהו תשלום סימולציה. התשלום לא יבוצע בפועל.
                    </p>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    אישור תשלום
                </button>
            </form>
        </div>
    </main>

    <script src="/site/script.js"></script>
    <script>
        if (typeof initTheme === 'function') initTheme();
        if (typeof initNavbarToggle === 'function') initNavbarToggle();
    </script>
</body>
</html>

