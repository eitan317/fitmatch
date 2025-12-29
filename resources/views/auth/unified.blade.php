<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>התחברות והרשמה - FitMatch</title>
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

                <!-- Register Form - Multi Step -->
                <div id="register-container">
                    <!-- Step 1: Email Verification -->
                    <div id="register-step-1" class="register-step">
                        <h3 style="margin-bottom: 1rem; color: var(--text-main);">אימות אימייל</h3>
                        <div class="form-group">
                            <label for="register-email">דוא"ל:</label>
                            <input type="email" id="register-email" name="email" class="form-control" value="{{ old('email') }}" required>
                            <small class="form-text" style="color: var(--text-muted); margin-top: 0.5rem; display: block;">
                                נשלח קוד אימות לאימייל שלך
                            </small>
                            <div id="email-error" class="form-error" style="display: none; margin-top: 0.5rem;"></div>
                        </div>
                        <button type="button" id="send-code-btn" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                            <span id="send-code-text">שלח קוד אימות</span>
                            <span id="send-code-loading" style="display: none;">שולח...</span>
                        </button>
                    </div>

                    <!-- Step 2: Code Verification -->
                    <div id="register-step-2" class="register-step" style="display: none;">
                        <h3 style="margin-bottom: 1rem; color: var(--text-main);">הזן קוד אימות</h3>
                        <div class="form-group">
                            <label for="verification-code">קוד אימות (6 ספרות):</label>
                            <input type="text" id="verification-code" name="code" class="form-control" maxlength="6" pattern="[0-9]{6}" required style="text-align: center; font-size: 1.5rem; letter-spacing: 0.5rem; font-family: monospace;">
                            <small class="form-text" style="color: var(--text-muted); margin-top: 0.5rem; display: block;">
                                הקוד נשלח ל: <span id="verified-email-display"></span>
                            </small>
                            <div id="code-error" class="form-error" style="display: none; margin-top: 0.5rem;"></div>
                        </div>
                        <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                            <button type="button" id="verify-code-btn" class="btn btn-primary" style="flex: 1;">
                                <span id="verify-code-text">אמת קוד</span>
                                <span id="verify-code-loading" style="display: none;">בודק...</span>
                            </button>
                            <button type="button" id="resend-code-btn" class="btn btn-secondary" style="flex: 1;">
                                שלח קוד חדש
                            </button>
                        </div>
                        <button type="button" id="back-to-email-btn" class="btn btn-link" style="width: 100%; margin-top: 0.5rem; color: var(--text-muted);">
                            חזור לשלב הקודם
                        </button>
                    </div>

                    <!-- Step 3: Registration Form -->
                    <div id="register-step-3" class="register-step" style="display: none;">
                        <h3 style="margin-bottom: 1rem; color: var(--text-main);">השלם את ההרשמה</h3>
                        <form method="POST" action="{{ route('register') }}" id="register-form">
                            @csrf
                            <input type="hidden" id="register-email-final" name="email" value="">

                            <div class="form-group">
                                <label for="register-name">שם מלא:</label>
                                <input type="text" id="register-name" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
                                @error('name')
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
                    </div>
                </div>
                
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

        // Email Verification Flow
        (function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                             document.querySelector('input[name="_token"]')?.value;
            
            let currentEmail = '';

            // Step 1: Send Code
            const sendCodeBtn = document.getElementById('send-code-btn');
            const registerEmailInput = document.getElementById('register-email');
            const emailError = document.getElementById('email-error');

            if (sendCodeBtn && registerEmailInput) {
                sendCodeBtn.addEventListener('click', async function() {
                    const email = registerEmailInput.value.trim().toLowerCase();
                    
                    if (!email || !email.includes('@')) {
                        emailError.textContent = 'אנא הזן אימייל תקין';
                        emailError.style.display = 'block';
                        return;
                    }

                    emailError.style.display = 'none';
                    sendCodeBtn.disabled = true;
                    document.getElementById('send-code-text').style.display = 'none';
                    document.getElementById('send-code-loading').style.display = 'inline';

                    try {
                        const response = await fetch('/verify-email/check', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ email })
                        });

                        const data = await response.json();

                        if (data.success) {
                            currentEmail = email;
                            document.getElementById('register-step-1').style.display = 'none';
                            document.getElementById('register-step-2').style.display = 'block';
                            document.getElementById('verified-email-display').textContent = email;
                            document.getElementById('verification-code').focus();
                        } else {
                            emailError.textContent = data.message || 'שגיאה בשליחת הקוד';
                            emailError.style.display = 'block';
                        }
                    } catch (error) {
                        emailError.textContent = 'שגיאה בחיבור לשרת';
                        emailError.style.display = 'block';
                    } finally {
                        sendCodeBtn.disabled = false;
                        document.getElementById('send-code-text').style.display = 'inline';
                        document.getElementById('send-code-loading').style.display = 'none';
                    }
                });
            }

            // Step 2: Verify Code
            const verifyCodeBtn = document.getElementById('verify-code-btn');
            const verificationCodeInput = document.getElementById('verification-code');
            const codeError = document.getElementById('code-error');
            const resendCodeBtn = document.getElementById('resend-code-btn');
            const backToEmailBtn = document.getElementById('back-to-email-btn');

            if (verificationCodeInput) {
                // Auto-format code input (numbers only)
                verificationCodeInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
                });
            }

            if (verifyCodeBtn && verificationCodeInput) {
                verifyCodeBtn.addEventListener('click', async function() {
                    const code = verificationCodeInput.value.trim();
                    
                    if (code.length !== 6) {
                        codeError.textContent = 'אנא הזן קוד של 6 ספרות';
                        codeError.style.display = 'block';
                        return;
                    }

                    codeError.style.display = 'none';
                    verifyCodeBtn.disabled = true;
                    document.getElementById('verify-code-text').style.display = 'none';
                    document.getElementById('verify-code-loading').style.display = 'inline';

                    try {
                        const response = await fetch('/verify-email/verify', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ 
                                email: currentEmail,
                                code: code
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            document.getElementById('register-step-2').style.display = 'none';
                            document.getElementById('register-step-3').style.display = 'block';
                            document.getElementById('register-email-final').value = currentEmail;
                            document.getElementById('register-name').focus();
                        } else {
                            codeError.textContent = data.message || 'קוד שגוי';
                            codeError.style.display = 'block';
                        }
                    } catch (error) {
                        codeError.textContent = 'שגיאה בחיבור לשרת';
                        codeError.style.display = 'block';
                    } finally {
                        verifyCodeBtn.disabled = false;
                        document.getElementById('verify-code-text').style.display = 'inline';
                        document.getElementById('verify-code-loading').style.display = 'none';
                    }
                });
            }

            // Resend Code
            if (resendCodeBtn) {
                resendCodeBtn.addEventListener('click', async function() {
                    if (!currentEmail) return;

                    resendCodeBtn.disabled = true;
                    resendCodeBtn.textContent = 'שולח...';

                    try {
                        const response = await fetch('/verify-email/resend', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ email: currentEmail })
                        });

                        const data = await response.json();

                        if (data.success) {
                            alert('קוד חדש נשלח לאימייל שלך');
                            verificationCodeInput.value = '';
                            verificationCodeInput.focus();
                        } else {
                            alert(data.message || 'שגיאה בשליחת הקוד');
                        }
                    } catch (error) {
                        alert('שגיאה בחיבור לשרת');
                    } finally {
                        resendCodeBtn.disabled = false;
                        resendCodeBtn.textContent = 'שלח קוד חדש';
                    }
                });
            }

            // Back to Email Step
            if (backToEmailBtn) {
                backToEmailBtn.addEventListener('click', function() {
                    document.getElementById('register-step-2').style.display = 'none';
                    document.getElementById('register-step-1').style.display = 'block';
                    verificationCodeInput.value = '';
                    codeError.style.display = 'none';
                });
            }
        })();

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
