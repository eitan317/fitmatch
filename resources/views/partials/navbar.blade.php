<header class="site-header">
    <div class="nav-inner">
        <div class="nav-logo">
            <i class="fas fa-dumbbell"></i>
            <span class="logo-text">FitMatch</span>
        </div>
        <button class="nav-toggle" id="navToggle" aria-label="פתיחת תפריט">☰</button>
        <nav class="nav-links" id="navLinks">
            <a href="/">דף הבית</a>
            @auth
                <a href="/trainers">מצא מאמן</a>
                <a href="/register-trainer">הרשמה כמאמן</a>
                @if(Auth::user()->isAdmin())
                    <a href="/admin/trainers" id="admin-link">פאנל מנהל</a>
                @endif
                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary);">
                    @else
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.9rem;">
                            {{ mb_substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                    <span style="color: var(--text-main); font-size: 0.9rem;">{{ Auth::user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="nav-btn">התנתק</button>
                    </form>
                </div>
            @else
                <a href="/login">התחברות</a>
                <a href="{{ route('register') }}">הרשמה</a>
            @endauth
        </nav>
    </div>
</header>
