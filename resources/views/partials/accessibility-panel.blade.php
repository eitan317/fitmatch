<!-- Accessibility Panel -->
<div id="accessibility-panel" class="accessibility-panel" aria-label="פאנל נגישות">
    <!-- Toggle Button -->
    <button 
        id="accessibility-toggle" 
        class="accessibility-toggle"
        aria-label="פתיחת פאנל נגישות"
        aria-expanded="false"
        aria-controls="accessibility-menu">
        <i class="fas fa-universal-access"></i>
        <span class="accessibility-toggle-text">נגישות</span>
    </button>

    <!-- Panel Menu -->
    <div id="accessibility-menu" class="accessibility-menu" role="menu" aria-label="תפריט נגישות">
        <div class="accessibility-header">
            <h3>
                <i class="fas fa-universal-access"></i>
                נגישות
            </h3>
            <button 
                id="accessibility-close" 
                class="accessibility-close"
                aria-label="סגירת פאנל נגישות">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="accessibility-content">
            <!-- Font Size Controls -->
            <div class="accessibility-section">
                <label class="accessibility-label">
                    <i class="fas fa-text-height"></i>
                    גודל טקסט
                </label>
                <div class="accessibility-controls">
                    <button 
                        id="font-decrease" 
                        class="accessibility-btn"
                        aria-label="הקטנת גודל הטקסט">
                        <i class="fas fa-minus"></i>
                        <span>קטן</span>
                    </button>
                    <button 
                        id="font-reset" 
                        class="accessibility-btn"
                        aria-label="איפוס גודל הטקסט">
                        <i class="fas fa-undo"></i>
                        <span>רגיל</span>
                    </button>
                    <button 
                        id="font-increase" 
                        class="accessibility-btn"
                        aria-label="הגדלת גודל הטקסט">
                        <i class="fas fa-plus"></i>
                        <span>גדול</span>
                    </button>
                </div>
            </div>

            <!-- High Contrast Toggle -->
            <div class="accessibility-section">
                <label class="accessibility-label">
                    <i class="fas fa-adjust"></i>
                    ניגודיות גבוהה
                </label>
                <div class="accessibility-toggle-switch">
                    <input 
                        type="checkbox" 
                        id="high-contrast-toggle"
                        aria-label="הפעלת ניגודיות גבוהה">
                    <label for="high-contrast-toggle" class="toggle-label">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            <!-- Keyboard Navigation Indicator -->
            <div class="accessibility-section">
                <label class="accessibility-label">
                    <i class="fas fa-keyboard"></i>
                    הדגשת פוקוס
                </label>
                <div class="accessibility-toggle-switch">
                    <input 
                        type="checkbox" 
                        id="focus-indicator-toggle"
                        aria-label="הפעלת הדגשת פוקוס">
                    <label for="focus-indicator-toggle" class="toggle-label">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            <!-- Screen Reader Announcements -->
            <div id="accessibility-announce" class="sr-only" aria-live="polite" aria-atomic="true"></div>
        </div>
    </div>
</div>

<style>
/* Accessibility Panel Styles */
.accessibility-panel {
    position: fixed;
    bottom: 20px;
    left: 20px;
    z-index: 9999;
    font-family: "Inter", "Poppins", system-ui, sans-serif;
}

/* Toggle Button */
.accessibility-toggle {
    background: var(--primary, #4A9EFF);
    color: white;
    border: none;
    border-radius: 50px;
    padding: 1rem 1.5rem;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(74, 158, 255, 0.4);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    position: relative;
    z-index: 10001;
}

.accessibility-toggle:hover {
    background: var(--primary-dark, #3A8EEF);
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(74, 158, 255, 0.5);
}

.accessibility-toggle:focus {
    outline: 3px solid var(--primary-light, #8FC5FF);
    outline-offset: 2px;
}

.accessibility-toggle i {
    font-size: 1.2rem;
}

/* Panel Menu */
.accessibility-menu {
    position: absolute;
    bottom: 70px;
    left: 0;
    background: var(--bg-card, #0d2329);
    border: 2px solid var(--border-soft, rgba(74, 158, 255, 0.2));
    border-radius: 16px;
    padding: 1.5rem;
    min-width: 320px;
    max-width: 400px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(10px);
    display: none;
    animation: slideUp 0.3s ease;
}

.accessibility-menu.active {
    display: block;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.accessibility-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-soft, rgba(74, 158, 255, 0.2));
}

.accessibility-header h3 {
    color: var(--text-main, #ffffff);
    font-size: 1.25rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
}

.accessibility-close {
    background: transparent;
    border: none;
    color: var(--text-muted, #a0d4d9);
    font-size: 1.25rem;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 8px;
    transition: all 0.2s ease;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.accessibility-close:hover {
    background: rgba(74, 158, 255, 0.1);
    color: var(--text-main, #ffffff);
}

.accessibility-close:focus {
    outline: 2px solid var(--primary, #4A9EFF);
    outline-offset: 2px;
}

/* Content */
.accessibility-content {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.accessibility-section {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.accessibility-label {
    color: var(--text-main, #ffffff);
    font-size: 1rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.accessibility-controls {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.accessibility-btn {
    flex: 1;
    min-width: 80px;
    background: rgba(74, 158, 255, 0.1);
    border: 1px solid var(--border-soft, rgba(74, 158, 255, 0.2));
    color: var(--text-main, #ffffff);
    padding: 0.75rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
}

.accessibility-btn:hover {
    background: rgba(74, 158, 255, 0.2);
    border-color: var(--primary, #4A9EFF);
    transform: translateY(-1px);
}

.accessibility-btn:focus {
    outline: 2px solid var(--primary, #4A9EFF);
    outline-offset: 2px;
}

.accessibility-btn:active {
    transform: translateY(0);
}

/* Toggle Switch */
.accessibility-toggle-switch {
    display: flex;
    align-items: center;
}

.toggle-label {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 30px;
    cursor: pointer;
}

.toggle-label input[type="checkbox"] {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(74, 158, 255, 0.2);
    border-radius: 30px;
    transition: 0.3s;
    border: 1px solid var(--border-soft, rgba(74, 158, 255, 0.2));
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    border-radius: 50%;
    transition: 0.3s;
}

input:checked + .toggle-label .toggle-slider {
    background-color: var(--primary, #4A9EFF);
}

input:checked + .toggle-label .toggle-slider:before {
    transform: translateX(30px);
}

input:focus + .toggle-label .toggle-slider {
    box-shadow: 0 0 0 3px rgba(74, 158, 255, 0.3);
}

/* High Contrast Mode */
body.high-contrast {
    --bg-body: #000000;
    --bg-card: #1a1a1a;
    --bg-navbar: #000000;
    --text-main: #ffffff;
    --text-muted: #cccccc;
    --primary: #00ffff;
    --primary-dark: #00cccc;
    --border-soft: rgba(0, 255, 255, 0.5);
}

body.high-contrast * {
    border-color: var(--border-soft) !important;
}

/* Enhanced Focus Indicators */
body.enhanced-focus *:focus {
    outline: 3px solid var(--primary, #4A9EFF) !important;
    outline-offset: 3px !important;
    box-shadow: 0 0 0 2px var(--bg-body), 0 0 0 5px var(--primary) !important;
}

body.high-contrast.enhanced-focus *:focus {
    outline: 4px solid #00ffff !important;
    outline-offset: 4px !important;
    box-shadow: 0 0 0 3px #000000, 0 0 0 7px #00ffff !important;
}

/* Font Size Adjustments */
body.font-small {
    font-size: 14px;
}

body.font-normal {
    font-size: 16px;
}

body.font-large {
    font-size: 18px;
}

body.font-xlarge {
    font-size: 20px;
}

body.font-xxlarge {
    font-size: 22px;
}

/* Screen Reader Only */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .accessibility-panel {
        bottom: 15px;
        left: 15px;
    }

    .accessibility-toggle {
        padding: 0.875rem 1.25rem;
        font-size: 0.9rem;
    }

    .accessibility-toggle-text {
        display: none;
    }

    .accessibility-menu {
        min-width: 280px;
        max-width: calc(100vw - 30px);
        bottom: 60px;
        padding: 1.25rem;
    }

    .accessibility-controls {
        flex-direction: column;
    }

    .accessibility-btn {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .accessibility-panel {
        bottom: 10px;
        left: 10px;
    }

    .accessibility-toggle {
        padding: 0.75rem;
        width: 50px;
        height: 50px;
        justify-content: center;
    }

    .accessibility-menu {
        min-width: calc(100vw - 20px);
        left: 10px;
        right: 10px;
        bottom: 65px;
    }
}
</style>

<script>
(function() {
    'use strict';

    // Initialize accessibility panel
    const panel = document.getElementById('accessibility-panel');
    const toggle = document.getElementById('accessibility-toggle');
    const menu = document.getElementById('accessibility-menu');
    const closeBtn = document.getElementById('accessibility-close');
    const announce = document.getElementById('accessibility-announce');

    // Font size controls
    const fontDecrease = document.getElementById('font-decrease');
    const fontReset = document.getElementById('font-reset');
    const fontIncrease = document.getElementById('font-increase');

    // Toggles
    const highContrastToggle = document.getElementById('high-contrast-toggle');
    const focusIndicatorToggle = document.getElementById('focus-indicator-toggle');

    // Announce to screen readers
    function announceToScreenReader(message) {
        if (announce) {
            announce.textContent = message;
            setTimeout(() => {
                announce.textContent = '';
            }, 1000);
        }
    }

    // Load saved preferences
    function loadPreferences() {
        // Font size
        const fontSize = localStorage.getItem('accessibility-font-size') || 'normal';
        document.body.className = document.body.className.replace(/font-\w+/g, '');
        document.body.classList.add(`font-${fontSize}`);
        announceToScreenReader(`גודל טקסט: ${fontSize === 'small' ? 'קטן' : fontSize === 'large' ? 'גדול' : fontSize === 'xlarge' ? 'גדול מאוד' : fontSize === 'xxlarge' ? 'גדול במיוחד' : 'רגיל'}`);

        // High contrast
        const highContrast = localStorage.getItem('accessibility-high-contrast') === 'true';
        if (highContrast) {
            document.body.classList.add('high-contrast');
            highContrastToggle.checked = true;
        }

        // Focus indicator
        const focusIndicator = localStorage.getItem('accessibility-focus-indicator') === 'true';
        if (focusIndicator) {
            document.body.classList.add('enhanced-focus');
            focusIndicatorToggle.checked = true;
        }
    }

    // Toggle menu
    function toggleMenu() {
        const isOpen = menu.classList.contains('active');
        menu.classList.toggle('active');
        toggle.setAttribute('aria-expanded', !isOpen);
        
        if (!isOpen) {
            announceToScreenReader('פאנל נגישות נפתח');
        } else {
            announceToScreenReader('פאנל נגישות נסגר');
        }
    }

    // Font size functions
    const fontSizes = ['small', 'normal', 'large', 'xlarge', 'xxlarge'];
    let currentFontIndex = fontSizes.indexOf(localStorage.getItem('accessibility-font-size') || 'normal');

    function setFontSize(size) {
        document.body.className = document.body.className.replace(/font-\w+/g, '');
        document.body.classList.add(`font-${size}`);
        localStorage.setItem('accessibility-font-size', size);
        currentFontIndex = fontSizes.indexOf(size);
        
        const sizeLabels = {
            'small': 'קטן',
            'normal': 'רגיל',
            'large': 'גדול',
            'xlarge': 'גדול מאוד',
            'xxlarge': 'גדול במיוחד'
        };
        announceToScreenReader(`גודל טקסט שונה ל-${sizeLabels[size]}`);
    }

    // Event listeners
    if (toggle) {
        toggle.addEventListener('click', toggleMenu);
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', toggleMenu);
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (menu && menu.classList.contains('active')) {
            if (!panel.contains(e.target)) {
                menu.classList.remove('active');
                toggle.setAttribute('aria-expanded', 'false');
            }
        }
    });

    // Font size controls
    if (fontDecrease) {
        fontDecrease.addEventListener('click', function() {
            if (currentFontIndex > 0) {
                setFontSize(fontSizes[currentFontIndex - 1]);
            }
        });
    }

    if (fontReset) {
        fontReset.addEventListener('click', function() {
            setFontSize('normal');
        });
    }

    if (fontIncrease) {
        fontIncrease.addEventListener('click', function() {
            if (currentFontIndex < fontSizes.length - 1) {
                setFontSize(fontSizes[currentFontIndex + 1]);
            }
        });
    }

    // High contrast toggle
    if (highContrastToggle) {
        highContrastToggle.addEventListener('change', function() {
            if (this.checked) {
                document.body.classList.add('high-contrast');
                localStorage.setItem('accessibility-high-contrast', 'true');
                announceToScreenReader('ניגודיות גבוהה הופעלה');
            } else {
                document.body.classList.remove('high-contrast');
                localStorage.setItem('accessibility-high-contrast', 'false');
                announceToScreenReader('ניגודיות גבוהה בוטלה');
            }
        });
    }

    // Focus indicator toggle
    if (focusIndicatorToggle) {
        focusIndicatorToggle.addEventListener('change', function() {
            if (this.checked) {
                document.body.classList.add('enhanced-focus');
                localStorage.setItem('accessibility-focus-indicator', 'true');
                announceToScreenReader('הדגשת פוקוס הופעלה');
            } else {
                document.body.classList.remove('enhanced-focus');
                localStorage.setItem('accessibility-focus-indicator', 'false');
                announceToScreenReader('הדגשת פוקוס בוטלה');
            }
        });
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        // Close menu with Escape
        if (e.key === 'Escape' && menu && menu.classList.contains('active')) {
            menu.classList.remove('active');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.focus();
            announceToScreenReader('פאנל נגישות נסגר');
        }
    });

    // Load preferences on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadPreferences);
    } else {
        loadPreferences();
    }
})();
</script>

