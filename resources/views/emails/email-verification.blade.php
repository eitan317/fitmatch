<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>קוד אימות אימייל</title>
</head>
<body style="font-family: Arial, sans-serif; direction: rtl; text-align: right; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #4a9eff; margin: 0;">FitMatch</h1>
        </div>
        
        <h2 style="color: #333; margin-top: 0;">שלום!</h2>
        
        <p style="color: #666; font-size: 16px; line-height: 1.6;">
            תודה שנרשמת ל-FitMatch! כדי להשלים את ההרשמה, אנא השתמש בקוד האימות הבא:
        </p>
        
        <div style="background-color: #f8f9fa; border: 2px dashed #4a9eff; border-radius: 8px; padding: 20px; text-align: center; margin: 30px 0;">
            <div style="font-size: 36px; font-weight: bold; color: #4a9eff; letter-spacing: 8px; font-family: 'Courier New', monospace;">
                {{ $code }}
            </div>
        </div>
        
        <p style="color: #666; font-size: 14px; line-height: 1.6;">
            <strong>חשוב:</strong> הקוד תקף ל-10 דקות בלבד. אם לא ביקשת קוד זה, תוכל להתעלם מהאימייל הזה.
        </p>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center;">
            <p style="color: #999; font-size: 12px; margin: 0;">
                זהו אימייל אוטומטי, אנא אל תשיב עליו.
            </p>
        </div>
    </div>
</body>
</html>

