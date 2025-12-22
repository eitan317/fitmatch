<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>בחירת תכנית מנוי - FitMatch</title>
    <link rel="stylesheet" href="/site/style.css">
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        <h1>בחר תכנית מנוי</h1>
        <p>בחר את התכנית המתאימה לך והתחל להציג את הפרופיל שלך</p>

        @if(session('error'))
            <div class="form-message error">{{ session('error') }}</div>
        @endif

        <div class="subscription-plans" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 2rem;">
            @foreach($plans as $plan)
                <div class="subscription-plan-card" style="background: var(--bg-card); border-radius: 1rem; padding: 2rem; border: 2px solid {{ $plan->priority === 3 ? 'var(--primary)' : 'rgba(148,163,184,0.3)' }}; position: relative;">
                    @if($plan->badge_text)
                        <div style="position: absolute; top: -12px; right: 20px; background: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 999px; font-size: 0.85rem; font-weight: 600;">
                            {{ $plan->badge_text }}
                        </div>
                    @endif
                    
                    <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem;">{{ $plan->name }}</h2>
                    <div style="font-size: 2.5rem; font-weight: bold; color: var(--primary); margin: 1rem 0;">
                        ₪{{ number_format($plan->price, 0) }}
                        <span style="font-size: 1rem; color: var(--text-muted);">/חודש</span>
                    </div>
                    
                    <ul style="list-style: none; padding: 0; margin: 1.5rem 0;">
                        @foreach($plan->features as $feature)
                            <li style="padding: 0.5rem 0; display: flex; align-items: center;">
                                <span style="color: var(--primary); margin-left: 0.5rem;">✓</span>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>

                    <form action="{{ route('subscriptions.subscribe') }}" method="POST" style="margin-top: 2rem;">
                        @csrf
                        <input type="hidden" name="subscription_plan_id" value="{{ $plan->id }}">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            בחר תכנית זו
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </main>

    <script src="/site/script.js"></script>
    <script>
        if (typeof initTheme === 'function') initTheme();
        if (typeof initNavbarToggle === 'function') initNavbarToggle();
    </script>
</body>
</html>

