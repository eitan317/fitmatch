<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>תשלום מנוי - FitMatch</title>
    <link rel="stylesheet" href="{{ asset('site/style.css') }}">
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        <h1>תשלום מנוי</h1>

        @if(session('error'))
            <div class="form-message error">{{ session('error') }}</div>
        @endif

        <div class="form-card" style="max-width: 600px; margin: 2rem auto;">
            <h2>פרטי התכנית</h2>
            <p><strong>תכנית:</strong> {{ $subscription->plan->name }}</p>
            <p><strong>מחיר:</strong> ₪{{ number_format($subscription->plan->price, 0) }} לחודש</p>
            <p><strong>תוקף:</strong> עד {{ $subscription->expires_at->format('d/m/Y') }}</p>

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

    <script src="{{ asset('site/script.js') }}"></script>
    <script>
        if (typeof initTheme === 'function') initTheme();
        if (typeof initNavbarToggle === 'function') initNavbarToggle();
    </script>
</body>
</html>

