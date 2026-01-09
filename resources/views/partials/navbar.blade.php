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
        
        <!-- Desktop Navigation Links -->
        <nav class="nav-links desktop-nav">
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
            @endauth
        </nav>
    </div>
    
    <!-- Mobile Hamburger Menu -->
    <button id="navToggle" class="hamburger-menu" aria-label="תפריט" aria-expanded="false">
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
    </button>
    
    <!-- Mobile Menu Panel -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>
    <nav class="mobile-menu-panel" id="mobileMenuPanel">
        <button class="mobile-menu-close" id="mobileMenuClose" aria-label="סגור תפריט">
            <i class="fas fa-times"></i>
        </button>
        <div class="mobile-menu-content">
            @php
                $isTrainersPage = request()->routeIs('trainers.index*') || 
                                  request()->routeIs('trainers.show*') ||
                                  request()->is('trainers') || 
                                  request()->is('trainers/*') ||
                                  (request()->is('*/trainers') && !request()->is('admin/*'));
            @endphp
            @if($isTrainersPage)
                {{-- User is on trainers page - show "Back to Home" --}}
                <a href="/" class="mobile-menu-item">
                    <i class="fas fa-home"></i>
                    <span>חזור למסך הבית</span>
                </a>
            @else
                {{-- User is on other pages - show "Find Trainer" --}}
                <a href="/trainers" class="mobile-menu-item">
                    <i class="fas fa-search"></i>
                    <span>מציאת מאמן</span>
                </a>
            @endif
            @guest
                {{-- User not logged in: Always show "Register as Trainer" --}}
                <a href="{{ route('trainers.create') }}" class="mobile-menu-item">
                    <i class="fas fa-user-plus"></i>
                    <span>הרשמה כמאמן</span>
                </a>
            @else
                {{-- User logged in: Only show "Back to Main Profile" if NOT on trainers page (avoid duplication) --}}
                @if(!$isTrainersPage)
                    <a href="/" class="mobile-menu-item">
                        <i class="fas fa-home"></i>
                        <span>חזרה לפרופיל הראשי</span>
                    </a>
                @endif
                
                {{-- Admin Panel: Only show if user is admin --}}
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.trainers.index') }}" class="mobile-menu-item">
                        <i class="fas fa-cog"></i>
                        <span>{{ __('messages.admin_panel') }}</span>
                    </a>
                @endif
            @endguest
            <div class="mobile-menu-divider"></div>
            @auth
                <form action="{{ route('logout') }}" method="POST" class="mobile-menu-logout-form">
                    @csrf
                    <button type="submit" class="mobile-menu-item mobile-menu-logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>התנתקות</span>
                    </button>
                </form>
            @else
                <a href="/login" class="mobile-menu-item">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>התחברות</span>
                </a>
            @endauth
        </div>
    </nav>
    </div>
</header>
