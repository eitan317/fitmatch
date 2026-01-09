<header class="site-header">
    <div class="nav-inner">
        <!-- Logo -->
        <div class="nav-logo">
            <i class="fas fa-dumbbell"></i>
            <span class="logo-text">FitMatch</span>
        </div>
        
        <!-- User Status (if logged in) - Desktop only -->
        @auth
        <div class="nav-user-status">
            @if(Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="nav-user-avatar-small">
            @else
                <div class="nav-user-avatar-small-placeholder">
                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                </div>
            @endif
            <span class="nav-user-email">{{ Auth::user()->email }}</span>
        </div>
        @endauth
        
        <!-- Language Selector -->
        <div class="language-selector">
            <button class="language-btn" id="languageToggle" aria-label="{{ __('messages.select_language') }}">
                <i class="fas fa-globe"></i>
                <span class="current-lang">{{ strtoupper(session('locale', 'he')) }}</span>
            </button>
            <div class="language-menu" id="languageMenu">
                <a href="{{ route('language.switch', 'he') }}" class="language-option {{ session('locale', 'he') == 'he' ? 'active' : '' }}">
                    <span>עברית</span>
                </a>
                <a href="{{ route('language.switch', 'ar') }}" class="language-option {{ session('locale', 'he') == 'ar' ? 'active' : '' }}">
                    <span>العربية</span>
                </a>
                <a href="{{ route('language.switch', 'ru') }}" class="language-option {{ session('locale', 'he') == 'ru' ? 'active' : '' }}">
                    <span>Русский</span>
                </a>
                <a href="{{ route('language.switch', 'en') }}" class="language-option {{ session('locale', 'he') == 'en' ? 'active' : '' }}">
                    <span>English</span>
                </a>
            </div>
        </div>
        
        <!-- Hamburger Menu Button -->
        <button id="navToggle" class="hamburger-menu" aria-label="תפריט">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
        
        <!-- Navigation Menu -->
        <nav class="nav-links" id="navLinks">
            <a href="/" class="nav-link-item">
                <i class="fas fa-home"></i>
                <span>{{ __('messages.home') }}</span>
            </a>
            <a href="/trainers" class="nav-link-item">
                <i class="fas fa-search"></i>
                <span>{{ __('messages.find_trainer') }}</span>
            </a>
            @auth
                <a href="/register-trainer" class="nav-link-item">
                    <i class="fas fa-user-plus"></i>
                    <span>{{ __('messages.register_as_trainer') }}</span>
                </a>
                @if(Auth::user()->isAdmin())
                    <a href="/admin/trainers" class="nav-link-item" id="admin-link">
                        <i class="fas fa-cog"></i>
                        <span>{{ __('messages.admin_panel') }}</span>
                    </a>
                @endif
                <div class="nav-user-section-mobile">
                    <div class="nav-user-info">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="nav-avatar">
                        @else
                            <div class="nav-avatar-placeholder">
                                {{ mb_substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="nav-user-details">
                            <span class="nav-user-name">{{ Auth::user()->name }}</span>
                            <span class="nav-user-email-mobile">{{ Auth::user()->email }}</span>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="nav-logout-form">
                        @csrf
                        <button type="submit" class="nav-btn-logout">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>{{ __('messages.logout') }}</span>
                        </button>
                    </form>
                </div>
            @else
                <div class="nav-auth-section">
                    <a href="/login" class="nav-auth-btn nav-login-btn">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>{{ __('messages.login') }}</span>
                    </a>
                    <a href="{{ route('register') }}" class="nav-auth-btn nav-register-btn">
                        <i class="fas fa-user-plus"></i>
                        <span>{{ __('messages.register') }}</span>
                    </a>
                </div>
            @endauth
        </nav>
    </div>
</header>
