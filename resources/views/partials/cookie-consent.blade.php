<!-- Cookie Consent Banner -->
<div id="cookie-consent-banner" style="display: none; position: fixed; bottom: 0; left: 0; right: 0; background: rgba(0, 0, 0, 0.95); backdrop-filter: blur(10px); padding: 1rem 1.5rem; z-index: 10000; box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.3); border-top: 1px solid rgba(74, 158, 255, 0.3);">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 200px; color: var(--text-main, #ffffff); font-size: 0.9rem; line-height: 1.5;">
            <i class="fas fa-cookie-bite" style="color: var(--primary, #4a9eff); margin-left: 0.5rem;"></i>
            האתר משתמש בעוגיות לצורך התחברות, ניהול סשן ואבטחה. המשך השימוש באתר מהווה הסכמה לשימוש בעוגיות.
        </div>
        <button id="cookie-consent-accept" style="background: var(--primary, #4a9eff); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; white-space: nowrap; box-shadow: 0 2px 10px rgba(74, 158, 255, 0.3);">
            אישור
        </button>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        #cookie-consent-banner {
            padding: 1rem;
        }
        #cookie-consent-banner > div {
            flex-direction: column;
            align-items: stretch;
        }
        #cookie-consent-banner > div > div {
            font-size: 0.85rem;
            margin-bottom: 0.75rem;
        }
        #cookie-consent-accept {
            width: 100%;
            padding: 0.875rem 1.5rem;
        }
    }
    @media (max-width: 480px) {
        #cookie-consent-banner {
            padding: 0.875rem;
        }
        #cookie-consent-banner > div > div {
            font-size: 0.8rem;
        }
        #cookie-consent-accept {
            font-size: 0.9rem;
            padding: 0.75rem 1.25rem;
        }
    }
    #cookie-consent-accept:hover {
        background: var(--primary-hover, #3a8eef) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(74, 158, 255, 0.4);
    }
    #cookie-consent-accept:active {
        transform: translateY(0);
    }
</style>

<script>
(function() {
    // Check if consent was already given
    if (localStorage.getItem('cookiesAccepted') === 'true') {
        return; // Don't show the banner
    }
    
    // Show the banner
    var banner = document.getElementById('cookie-consent-banner');
    if (banner) {
        banner.style.display = 'block';
        
        // Handle accept button click
        var acceptButton = document.getElementById('cookie-consent-accept');
        if (acceptButton) {
            acceptButton.addEventListener('click', function() {
                localStorage.setItem('cookiesAccepted', 'true');
                banner.style.display = 'none';
            });
        }
    }
})();
</script>

