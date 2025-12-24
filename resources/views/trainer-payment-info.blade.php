<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ממתין לאישור תשלום - FitMatch</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/site/style.css">
    @include('partials.schema-ld')
    <style>
        /* Mobile Responsive Styles for Payment Info Page */
        @media (max-width: 768px) {
            /* Grid layouts - single column */
            .payment-info-grid {
                grid-template-columns: 1fr !important;
            }
            
            .trainer-info-grid {
                grid-template-columns: 1fr !important;
            }
            
            /* Payment card padding */
            .payment-card {
                padding: 1.5rem !important;
            }
            
            /* Payment details padding */
            .payment-details-card {
                padding: 1.5rem !important;
            }
            
            /* Large icon at top */
            .payment-header-icon {
                font-size: 3rem !important;
            }
            
            /* Price size */
            .payment-price {
                font-size: 2rem !important;
            }
            
            /* Icons in payment details */
            .payment-icon {
                font-size: 1.5rem !important;
            }
            
            /* Phone number */
            .payment-phone {
                font-size: 1.1rem !important;
            }
            
            /* Section titles */
            .payment-section-title {
                font-size: 1.25rem !important;
            }
            
            /* Step numbers padding */
            .payment-steps li {
                padding-right: 2rem !important;
            }
        }
        
        @media (max-width: 480px) {
            /* Payment cards padding - even smaller */
            .payment-card,
            .payment-details-card {
                padding: 1rem !important;
            }
            
            /* Trainer info padding */
            .trainer-info-card {
                padding: 1rem !important;
            }
            
            /* Large icon at top */
            .payment-header-icon {
                font-size: 2.5rem !important;
            }
            
            /* Price size */
            .payment-price {
                font-size: 1.75rem !important;
            }
            
            /* Icons in payment details */
            .payment-icon {
                font-size: 1.25rem !important;
            }
            
            /* Phone number */
            .payment-phone {
                font-size: 1rem !important;
            }
            
            /* Section titles */
            .payment-section-title {
                font-size: 1.1rem !important;
            }
            
            /* Page title */
            .page-title {
                font-size: 1.5rem !important;
            }
            
            /* Description text */
            .form-container p[style*="font-size: 1.1rem"] {
                font-size: 1rem !important;
            }
            
            /* Step numbers padding */
            .payment-steps li {
                padding-right: 2rem !important;
            }
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <main class="page-container">
        @if(session('success'))
            <div class="form-message success" style="margin-bottom: 2rem;">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="form-container">
            <div class="form-card">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div class="payment-header-icon" style="font-size: 4rem; margin-bottom: 1rem; color: #ffc107;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h1 class="page-title" style="margin-bottom: 0.5rem;">
                        ממתין לאישור תשלום
                    </h1>
                    <p style="font-size: 1.1rem; color: var(--text-muted);">
                        הפרופיל שלך יוצג באתר לאחר אישור התשלום על ידי מנהל המערכת
                    </p>
                </div>

                <!-- Trainer Info -->
                <div class="trainer-info-card" style="background: linear-gradient(135deg, rgba(0, 217, 255, 0.1), rgba(74, 158, 255, 0.1)); padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem; border: 2px solid rgba(0, 217, 255, 0.3);">
                    <h2 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.2rem;">
                        <i class="fas fa-user"></i>
                        פרטי המאמן
                    </h2>
                    <div class="trainer-info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        <div>
                            <strong>שם מלא:</strong> {{ $trainer->full_name }}
                        </div>
                        @if($trainer->phone)
                            <div>
                                <strong>מספר טלפון:</strong> {{ $trainer->phone }}
                            </div>
                        @endif
                        @if($trainer->city)
                            <div>
                                <strong>עיר:</strong> {{ $trainer->city }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="payment-card" style="background: rgba(255, 193, 7, 0.1); padding: 2rem; border-radius: 16px; margin-bottom: 2rem; border: 2px solid rgba(255, 193, 7, 0.3);">
                    <h2 class="payment-section-title" style="color: #ffc107; margin-bottom: 1.5rem; font-size: 1.5rem; text-align: center;">
                        <i class="fas fa-money-bill-wave"></i>
                        פרטי תשלום
                    </h2>
                    
                    <div class="payment-details-card" style="background: rgba(255, 255, 255, 0.05); padding: 2rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1); margin-bottom: 1.5rem;">
                        <div style="text-align: center; margin-bottom: 1.5rem;">
                            <div class="payment-price" style="font-size: 3rem; font-weight: bold; color: #ffc107; margin-bottom: 0.5rem;">
                                20 ₪
                            </div>
                            <p style="color: var(--text-muted); font-size: 0.9rem;">
                                סכום התשלום לחודש
                            </p>
                        </div>

                        <div class="payment-info-grid" style="display: grid; gap: 1.5rem;">
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: rgba(255, 255, 255, 0.05); border-radius: 8px;">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div class="payment-icon" style="font-size: 2rem; color: var(--primary);">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; margin-bottom: 0.25rem;">אמצעי תשלום</div>
                                        <div style="color: var(--text-muted); font-size: 0.9rem;">Bit בלבד</div>
                                    </div>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: rgba(255, 255, 255, 0.05); border-radius: 8px;">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div class="payment-icon" style="font-size: 2rem; color: var(--primary);">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; margin-bottom: 0.25rem;">מספר Bit</div>
                                        <div class="payment-phone" style="color: var(--primary); font-size: 1.3rem; font-weight: bold;">0527020113</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div style="background: rgba(148, 163, 184, 0.1); padding: 2rem; border-radius: 16px; margin-bottom: 2rem; border: 2px solid rgba(148, 163, 184, 0.3);">
                    <h3 style="margin-bottom: 1rem; font-size: 1.2rem;">
                        <i class="fas fa-info-circle" style="color: var(--primary); margin-left: 0.5rem;"></i>
                        מה קורה עכשיו?
                    </h3>
                    <ol style="list-style: none; padding: 0; margin: 0;">
                        <li style="padding: 0.75rem 0; position: relative; padding-right: 2.5rem;">
                            <span style="position: absolute; right: 0; background: var(--primary); color: white; width: 1.5rem; height: 1.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.85rem;">
                                1
                            </span>
                            שלח 20₪ דרך Bit למספר 0527020113
                        </li>
                        <li style="padding: 0.75rem 0; position: relative; padding-right: 2.5rem;">
                            <span style="position: absolute; right: 0; background: var(--primary); color: white; width: 1.5rem; height: 1.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.85rem;">
                                2
                            </span>
                            מנהל המערכת יאשר את התשלום
                        </li>
                        <li style="padding: 0.75rem 0; position: relative; padding-right: 2.5rem;">
                            <span style="position: absolute; right: 0; background: var(--primary); color: white; width: 1.5rem; height: 1.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.85rem;">
                                3
                            </span>
                            הפרופיל שלך יוצג באתר באופן מיידי
                        </li>
                    </ol>
                </div>

                <!-- Status Info -->
                <div class="payment-card" style="background: rgba(255, 193, 7, 0.15); padding: 1.5rem; border-radius: 12px; border-right: 4px solid #ffc107; margin-bottom: 2rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div class="payment-icon" style="font-size: 2rem; color: #ffc107;">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; margin-bottom: 0.25rem;">סטטוס נוכחי</div>
                            <div style="color: var(--text-muted);">ממתין לאישור תשלום על ידי מנהל המערכת</div>
                        </div>
                    </div>
                </div>

                <div style="text-align: center;">
                    <a href="{{ route('trainers.index') }}" class="btn btn-secondary" style="margin: 0.5rem;">
                        <i class="fas fa-home"></i>
                        חזור לדף הבית
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script src="/site/script.js"></script>
    <script>
        if (typeof initTheme === 'function') initTheme();
        if (typeof initNavbarToggle === 'function') initNavbarToggle();
    </script>
</body>
</html>

