<!DOCTYPE html>
<html lang="{{ session('locale', 'he') }}" dir="{{ in_array(session('locale', 'he'), ['he', 'ar']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>פנייה חדשה מ-FitMatch</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            direction: rtl;
            text-align: right;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #dc2626, #991b1b);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 30px 20px;
        }
        .info-section {
            background-color: #f9fafb;
            border-right: 4px solid #dc2626;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .info-row {
            margin-bottom: 15px;
        }
        .info-label {
            font-weight: bold;
            color: #374151;
            margin-bottom: 5px;
        }
        .info-value {
            color: #6b7280;
        }
        .message-section {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            padding: 20px;
            border-radius: 4px;
            margin-top: 20px;
        }
        .message-content {
            color: #374151;
            line-height: 1.8;
            white-space: pre-wrap;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>פנייה חדשה מ-FitMatch</h1>
        </div>
        
        <div class="email-body">
            <div class="info-section">
                <div class="info-row">
                    <div class="info-label">שם:</div>
                    <div class="info-value">{{ $name }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">דוא"ל:</div>
                    <div class="info-value">
                        <a href="mailto:{{ $email }}" style="color: #dc2626; text-decoration: none;">{{ $email }}</a>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">נושא:</div>
                    <div class="info-value">{{ $subject }}</div>
                </div>
            </div>
            
            <div class="message-section">
                <div class="info-label" style="margin-bottom: 10px;">הודעה:</div>
                <div class="message-content">{{ $message }}</div>
            </div>
        </div>
        
        <div class="email-footer">
            <p>הודעה זו נשלחה מטופס יצירת קשר באתר FitMatch</p>
            <p style="margin: 5px 0;">תאריך: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>

