<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ניהול מנוי - FitMatch</title>
    <link rel="stylesheet" href="{{ asset('site/style.css') }}">
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        <h1>ניהול מנוי</h1>

        @if(session('success'))
            <div class="form-message success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="form-message error">{{ session('error') }}</div>
        @endif

        @if($subscription)
            <div class="form-card" style="max-width: 800px; margin: 2rem auto;">
                <h2>מנוי נוכחי</h2>
                <div style="background: rgba(34,197,94,0.1); padding: 1.5rem; border-radius: 0.5rem; margin: 1rem 0;">
                    <p><strong>תכנית:</strong> {{ $subscription->plan->name }}</p>
                    <p><strong>מחיר:</strong> ₪{{ number_format($subscription->plan->price, 0) }} לחודש</p>
                    <p><strong>סטטוס:</strong> 
                        <span style="color: {{ $subscription->status === 'active' ? 'green' : 'orange' }};">
                            {{ $subscription->status === 'active' ? 'פעיל' : 'פג תוקף' }}
                        </span>
                    </p>
                    <p><strong>תוקף עד:</strong> {{ $subscription->expires_at->format('d/m/Y H:i') }}</p>
                    <p><strong>חידוש אוטומטי:</strong> {{ $subscription->auto_renew ? 'כן' : 'לא' }}</p>
                </div>

                @if($subscription->status === 'active')
                    <form action="{{ route('subscriptions.cancel') }}" method="POST" style="margin-top: 1rem;">
                        @csrf
                        <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">
                        <button type="submit" class="btn btn-outline danger" onclick="return confirm('האם אתה בטוח שברצונך לבטל את המנוי?')">
                            בטל מנוי
                        </button>
                    </form>
                @else
                    <form action="{{ route('subscriptions.renew') }}" method="POST" style="margin-top: 1rem;">
                        @csrf
                        <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">
                        <button type="submit" class="btn btn-primary">
                            חדש מנוי
                        </button>
                    </form>
                @endif
            </div>
        @else
            <div class="form-card" style="max-width: 600px; margin: 2rem auto; text-align: center;">
                <p>אין לך מנוי פעיל כרגע.</p>
                <a href="{{ route('subscriptions.choose') }}" class="btn btn-primary">בחר תכנית מנוי</a>
            </div>
        @endif

        @if($allSubscriptions->count() > 0)
            <div class="form-card" style="max-width: 800px; margin: 2rem auto;">
                <h2>היסטוריית מנויים</h2>
                <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid rgba(148,163,184,0.3);">
                            <th style="padding: 0.75rem; text-align: right;">תכנית</th>
                            <th style="padding: 0.75rem; text-align: right;">סטטוס</th>
                            <th style="padding: 0.75rem; text-align: right;">תאריך התחלה</th>
                            <th style="padding: 0.75rem; text-align: right;">תאריך סיום</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allSubscriptions as $sub)
                            <tr style="border-bottom: 1px solid rgba(148,163,184,0.2);">
                                <td style="padding: 0.75rem;">{{ $sub->plan->name }}</td>
                                <td style="padding: 0.75rem;">{{ $sub->status }}</td>
                                <td style="padding: 0.75rem;">{{ $sub->starts_at ? $sub->starts_at->format('d/m/Y') : '-' }}</td>
                                <td style="padding: 0.75rem;">{{ $sub->expires_at ? $sub->expires_at->format('d/m/Y') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </main>

    <script src="{{ asset('site/script.js') }}"></script>
    <script>
        if (typeof initTheme === 'function') initTheme();
        if (typeof initNavbarToggle === 'function') initNavbarToggle();
    </script>
</body>
</html>

