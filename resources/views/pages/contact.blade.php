<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>צור קשר - FitMatch</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('site/style.css') }}">
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        <h1 class="page-title">צור קשר</h1>
        
        @if(session('success'))
            <div class="form-message success" style="margin-bottom: 20px; text-align: center;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="form-message error" style="margin-bottom: 20px;">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
            <div class="form-container">
                <h2 style="color: var(--primary); margin-bottom: 1.5rem; text-align: center;">שלח לנו הודעה</h2>
                <form method="POST" action="{{ route('contact.store') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label for="name">שם מלא:</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                        @error('name')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">דוא"ל:</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="subject">נושא:</label>
                        <input type="text" id="subject" name="subject" class="form-control" value="{{ old('subject') }}" required>
                        @error('subject')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="message">הודעה:</label>
                        <textarea id="message" name="message" class="form-control" rows="6" required>{{ old('message') }}</textarea>
                        @error('message')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">שלח הודעה</button>
                </form>
            </div>

            <div class="form-container">
                <h2 style="color: var(--primary); margin-bottom: 1.5rem; text-align: center;">פרטי יצירת קשר</h2>
                <div style="padding: 1rem;">
                    <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 2rem; padding: 1rem; background: rgba(220, 38, 38, 0.1); border-radius: 12px;">
                        <i class="fas fa-envelope" style="color: var(--primary); font-size: 1.5rem; margin-top: 0.25rem;"></i>
                        <div>
                            <h3 style="color: var(--text-main); margin-bottom: 0.5rem;">אימייל</h3>
                            <a href="mailto:fitmatchcoil@gmail.com?subject=פנייה%20מ-FitMatch&body=שלום,%0D%0A%0D%0A" style="color: var(--primary); text-decoration: none;">fitmatchcoil@gmail.com</a>
                        </div>
                    </div>

                    <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 2rem; padding: 1rem; background: rgba(220, 38, 38, 0.1); border-radius: 12px;">
                        <i class="fas fa-phone" style="color: var(--primary); font-size: 1.5rem; margin-top: 0.25rem;"></i>
                        <div>
                            <h3 style="color: var(--text-main); margin-bottom: 0.5rem;">טלפון</h3>
                            <div>
                                <a href="tel:0527020113" style="color: var(--primary); text-decoration: none; display: block; margin-bottom: 0.5rem;">0527020113</a>
                                <a href="tel:0528381463" style="color: var(--primary); text-decoration: none; display: block;">0528381463</a>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 2rem; padding: 1rem; background: rgba(220, 38, 38, 0.1); border-radius: 12px;">
                        <i class="fas fa-map-marker-alt" style="color: var(--primary); font-size: 1.5rem; margin-top: 0.25rem;"></i>
                        <div>
                            <h3 style="color: var(--text-main); margin-bottom: 0.5rem;">מיקום</h3>
                            <p style="color: var(--text-muted); margin: 0;">ישראל</p>
                        </div>
                    </div>

                    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-soft);">
                        <h3 style="color: var(--text-main); margin-bottom: 1rem;">שעות פעילות</h3>
                        <p style="color: var(--text-muted); line-height: 1.8;">
                            אנו זמינים לענות על שאלותיכם בימים א'-ה' בין השעות 9:00-18:00.<br>
                            נשתדל לענות על כל פניה תוך 24 שעות.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('partials.footer')

    <script src="{{ asset('site/script.js') }}"></script>
    <script>
        if (typeof initTheme === 'function') initTheme();
        if (typeof initNavbarToggle === 'function') initNavbarToggle();
    </script>
</body>
</html>

