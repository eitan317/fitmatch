<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-content">
            <div class="footer-section">
                <h3 class="footer-title">
                    <i class="fas fa-dumbbell"></i> FitMatch
                </h3>
                <p class="footer-description">
                    הפלטפורמה המובילה לחיבור בין מאמני כושר מקצועיים למתאמנים. התחל את המסע שלך לכושר טוב יותר היום!
                </p>
                <div class="footer-social">
                    <a href="https://www.instagram.com/fitmatch.co.il/" target="_blank" class="social-icon" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.tiktok.com/@fitmatch912" target="_blank" class="social-icon" aria-label="TikTok">
                        <i class="fab fa-tiktok"></i>
                    </a>
                </div>
            </div>

            <div class="footer-section">
                <h4 class="footer-heading">קישורים מהירים</h4>
                <ul class="footer-links">
                    <li><a href="/"><i class="fas fa-home"></i> דף הבית</a></li>
                    @auth
                        <li><a href="{{ route('trainers.index') }}"><i class="fas fa-search"></i> מצא מאמן</a></li>
                        <li><a href="{{ route('trainers.create') }}"><i class="fas fa-user-plus"></i> הרשמה כמאמן</a></li>
                    @else
                        <li><a href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> התחברות</a></li>
                        <li><a href="{{ route('register') }}"><i class="fas fa-user-plus"></i> הרשמה</a></li>
                    @endauth
                </ul>
            </div>

            <div class="footer-section">
                <h4 class="footer-heading">מידע</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('about') }}"><i class="fas fa-info-circle"></i> אודותינו</a></li>
                    <li><a href="{{ route('faq') }}"><i class="fas fa-question-circle"></i> שאלות נפוצות</a></li>
                    <li><a href="{{ route('contact') }}"><i class="fas fa-envelope"></i> צור קשר</a></li>
                    <li><a href="{{ route('privacy') }}"><i class="fas fa-shield-alt"></i> מדיניות פרטיות</a></li>
                    <li><a href="{{ route('terms') }}"><i class="fas fa-file-contract"></i> תנאי שימוש</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4 class="footer-heading">צור קשר</h4>
                <ul class="footer-contact">
                    <li>
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:fitmatchcoil@gmail.com?subject=פנייה%20מ-FitMatch&body=שלום,%0D%0A%0D%0A"><span>fitmatchcoil@gmail.com</span></a>
                    </li>
                    <li>
                        <i class="fas fa-phone"></i>
                        <div>
                            <a href="tel:0527020113"><span>0527020113</span></a><br>
                            <a href="tel:0528381463"><span>0528381463</span></a>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>ישראל</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-copyright">
                <p>&copy; {{ date('Y') }} FitMatch. כל הזכויות שמורות.</p>
            </div>
            <div class="footer-made-with">
                <p>נבנה עם <i class="fas fa-heart" style="color: var(--primary);"></i> בישראל</p>
            </div>
        </div>
    </div>
</footer>

