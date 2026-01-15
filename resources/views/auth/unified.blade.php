<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>התחברות והרשמה - FitMatch</title>
    @include('partials.adsense-verification')
    @include('partials.adsense')
    <link rel="stylesheet" href="/site/style.css?v={{ file_exists(public_path('site/style.css')) ? filemtime(public_path('site/style.css')) : time() }}">
    @include('partials.schema-ld')
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        <h1 class="page-title">התחברות והרשמה</h1>
        
        @if(session('error'))
            <div class="form-message error" style="margin-bottom: 20px;">
                {{ session('error') }}
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
        
        <div class="form-container">
            <!-- Tabs Container -->
            <div class="auth-tabs">
                <button class="auth-tab-button {{ $activeTab === 'login' ? 'active' : '' }}" data-tab="login" id="tab-login">
                    התחברות
                </button>
                <button class="auth-tab-button {{ $activeTab === 'register' ? 'active' : '' }}" data-tab="register" id="tab-register">
                    הרשמה
                </button>
            </div>

            <!-- Login Tab Content -->
            <div class="auth-tab-content {{ $activeTab === 'login' ? 'active' : '' }}" id="content-login">
                <!-- Google Login Button -->
                <div style="margin-bottom: 30px; text-align: center;">
                    <a href="{{ route('google.login') }}" class="btn btn-primary google-auth-button" style="display: inline-flex; align-items: center; gap: 10px; padding: 0.75rem 1.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" style="fill: white;">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        התחבר עם Google
                    </a>
                </div>

                <div class="auth-divider">
                    <span>או</span>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" id="login-form">
                    @csrf

                    <div class="form-group">
                        <label for="login-email">דוא"ל:</label>
                        <input type="email" id="login-email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="login-password">סיסמה:</label>
                        <input type="password" id="login-password" name="password" class="form-control" required>
                        @error('password')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">זכור אותי</label>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">התחברות</button>
                </form>
                
                <div class="auth-links">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">שכחת סיסמה?</a>
                    @endif
                </div>
            </div>

            <!-- Register Tab Content -->
            <div class="auth-tab-content {{ $activeTab === 'register' ? 'active' : '' }}" id="content-register">
                <!-- Google Register Button -->
                <div style="margin-bottom: 30px; text-align: center;">
                    <a href="{{ route('google.login') }}" class="btn btn-primary google-auth-button" style="display: inline-flex; align-items: center; gap: 10px; padding: 0.75rem 1.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" style="fill: white;">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        הרשמה עם Google
                    </a>
                </div>

                <div class="auth-divider">
                    <span>או</span>
                </div>

                <!-- Register Form -->
                <form method="POST" action="{{ route('register') }}" id="register-form">
                    @csrf

                    <div class="form-group">
                        <label for="register-name">שם מלא:</label>
                        <input type="text" id="register-name" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="register-email">דוא"ל:</label>
                        <input type="email" id="register-email" name="email" class="form-control" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="register-password">סיסמה:</label>
                        <input type="password" id="register-password" name="password" class="form-control" required>
                        @error('password')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">אימות סיסמה:</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">הרשמה</button>
                </form>
                
                <div class="auth-links">
                    <a href="{{ route('trainers.create') }}">הרשמה כמאמן</a>
                </div>
            </div>
        </div>
    </main>

    <script src="/site/script.js?v={{ file_exists(public_path('site/script.js')) ? filemtime(public_path('site/script.js')) : time() }}"></script>
    <script>
        // Initialize theme and navbar
        if (typeof initTheme === 'function') initTheme();
        if (typeof initNavbarToggle === 'function') initNavbarToggle();

        // Tab Management
        (function() {
            const tabButtons = document.querySelectorAll('.auth-tab-button');
            const tabContents = document.querySelectorAll('.auth-tab-content');
            const activeTab = '{{ $activeTab ?? "login" }}';

            // Function to switch tabs
            function switchTab(tabName) {
                // Update buttons
                tabButtons.forEach(btn => {
                    if (btn.dataset.tab === tabName) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });

                // Update content
                tabContents.forEach(content => {
                    if (content.id === `content-${tabName}`) {
                        content.classList.add('active');
                    } else {
                        content.classList.remove('active');
                    }
                });

                // Update URL hash without scrolling
                if (history.pushState) {
                    history.pushState(null, null, `#${tabName}`);
                }

                // Store in sessionStorage
                sessionStorage.setItem('authActiveTab', tabName);
            }

            // Add click handlers to tab buttons
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabName = this.dataset.tab;
                    switchTab(tabName);
                });
            });

            // Check URL hash on load
            if (window.location.hash) {
                const hash = window.location.hash.substring(1);
                if (hash === 'login' || hash === 'register') {
                    switchTab(hash);
                }
            } else if (sessionStorage.getItem('authActiveTab')) {
                const savedTab = sessionStorage.getItem('authActiveTab');
                if (savedTab === 'login' || savedTab === 'register') {
                    switchTab(savedTab);
                }
            }

            // Handle form errors - switch to appropriate tab
            @if($errors->any())
                @if($errors->has('email') || $errors->has('password') || (old('_token') && !old('name')))
                    // Login form errors
                    switchTab('login');
                @elseif($errors->has('name') || $errors->has('email') || $errors->has('password'))
                    // Register form errors
                    switchTab('register');
                @endif
            @endif
        })();
    </script>
    @include('partials.cookie-consent')
    @include('partials.accessibility-panel')
</body>
</html>
