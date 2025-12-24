<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>תשלום נכשל - FitMatch</title>
    <link rel="stylesheet" href="/site/style.css">
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        <div class="form-card" style="max-width: 600px; margin: 2rem auto; text-align: center;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">❌</div>
            <h1>התשלום נכשל</h1>
            <p>אירעה שגיאה בעת ביצוע התשלום. אנא נסה שוב.</p>

            <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem;">
                <a href="{{ route('subscriptions.payment', $subscription) }}" class="btn btn-primary">נסה שוב</a>
                <a href="{{ route('subscriptions.choose') }}" class="btn btn-outline">בחר תכנית אחרת</a>
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

