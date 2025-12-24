<header class="site-header">
    <div class="nav-inner">
        <div class="nav-logo">
            <i class="fas fa-dumbbell"></i>
            <span class="logo-text">FitMatch</span>
        </div>
        
        <!-- Language Selector -->
        <div class="language-selector">
            <button class="language-btn" id="languageToggle" aria-label="{{ __('messages.select_language') }}">
                <i class="fas fa-globe"></i>
                <span class="current-lang">{{ strtoupper(session('locale', 'he')) }}</span>
            </button>
            <div class="language-menu" id="languageMenu">
                <a href="{{ route('language.switch', 'he') }}" class="language-option {{ session('locale', 'he') == 'he' ? 'active' : '' }}">
                    <span class="lang-flag">🇮🇱</span>
                    <span>עברית</span>
                </a>
                <a href="{{ route('language.switch', 'ar') }}" class="language-option {{ session('locale', 'he') == 'ar' ? 'active' : '' }}">
                    <span class="lang-flag">🇵🇸</span>
                    <span>العربية</span>
                </a>
                <a href="{{ route('language.switch', 'ru') }}" class="language-option {{ session('locale', 'he') == 'ru' ? 'active' : '' }}">
                    <span class="lang-flag">🇷🇺</span>
                    <span>Русский</span>
                </a>
                <a href="{{ route('language.switch', 'en') }}" class="language-option {{ session('locale', 'he') == 'en' ? 'active' : '' }}">
                    <span class="lang-flag">🇬🇧</span>
                    <span>English</span>
                </a>
            </div>
        </div>
        
        <button class="nav-toggle" id="navToggle" aria-label="פתיחת תפריט">☰</button>
        <nav class="nav-links" id="navLinks">
            <a href="/">{{ __('messages.home') }}</a>
            <a href="/trainers">{{ __('messages.find_trainer') }}</a>
            @auth
                <a href="/register-trainer">{{ __('messages.register_as_trainer') }}</a>
                @if(Auth::user()->isAdmin())
                    <a href="/admin/trainers" id="admin-link">{{ __('messages.admin_panel') }}</a>
                @endif
                <div class="nav-user-section">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="nav-avatar">
                    @else
                        <div class="nav-avatar-placeholder">
                            {{ mb_substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                    <span class="nav-user-name">{{ Auth::user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="nav-logout-form">
                        @csrf
                        <button type="submit" class="nav-btn">{{ __('messages.logout') }}</button>
                    </form>
                </div>
            @else
                <div class="nav-auth-section">
                    <a href="/login" class="nav-auth-btn nav-login-btn">
                        <i class="fas fa-sign-in-alt"></i>
                        {{ __('messages.login') }}
                    </a>
                    <a href="{{ route('register') }}" class="nav-auth-btn nav-register-btn">
                        <i class="fas fa-user-plus"></i>
                        {{ __('messages.register') }}
                    </a>
                </div>
            @endauth
        </nav>
    </div>
</header>
