<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>תשלום בוצע בהצלחה - FitMatch</title>
    <link rel="stylesheet" href="/site/style.css">
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        <div class="form-card" style="max-width: 600px; margin: 2rem auto; text-align: center;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">✅</div>
            <h1>התשלום בוצע בהצלחה!</h1>
            <p>המנוי שלך פעיל כעת</p>

            <div style="background: rgba(34,197,94,0.1); padding: 1.5rem; border-radius: 0.5rem; margin: 2rem 0; text-align: right;">
                <p><strong>תכנית:</strong> {{ $subscription->plan->name }}</p>
                <p><strong>תוקף עד:</strong> {{ $subscription->expires_at->format('d/m/Y') }}</p>
                <p><strong>מספר הזמנה:</strong> {{ $subscription->payment_id }}</p>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem;">
                <a href="{{ route('trainers.index') }}" class="btn btn-primary">חזור לרשימת המאמנים</a>
                <a href="{{ route('subscriptions.my-subscription') }}" class="btn btn-outline">ניהול מנוי</a>
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

