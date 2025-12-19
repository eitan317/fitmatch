# הוראות הגדרת SMTP לשליחת אימייל

## שלב 1: יצירת App Password ב-Gmail

1. היכנס לחשבון Google: `fitmatchcoil@gmail.com`
2. עבור ל-[Google Account Security](https://myaccount.google.com/security)
3. הפעל "2-Step Verification" (אם לא מופעל)
4. עבור ל-[App Passwords](https://myaccount.google.com/apppasswords)
5. צור App Password חדש:
   - בחר "Mail" ו-"Other (Custom name)"
   - שם: "FitMatch Website"
   - העתק את הסיסמה שנוצרה (16 תווים, עם רווחים)

## שלב 2: הוספת הגדרות SMTP ל-.env

פתח את קובץ `.env` והוסף את השורות הבאות בסוף הקובץ:

```env
# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=fitmatchcoil@gmail.com
MAIL_PASSWORD=xxxx xxxx xxxx xxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=fitmatchcoil@gmail.com
MAIL_FROM_NAME="FitMatch"
```

**חשוב**: החלף את `xxxx xxxx xxxx xxxx` ב-App Password שיצרת בשלב 1.

## שלב 3: ניקוי Cache

הרץ את הפקודה הבאה בטרמינל:

```bash
php artisan config:clear
```

## שלב 4: בדיקה

1. פתח את האתר ונסה לשלוח הודעה מטופס יצירת קשר
2. בדוק שהאימייל הגיע ל-`fitmatchcoil@gmail.com`
3. אם יש שגיאה, בדוק את `storage/logs/laravel.log`

## פתרון בעיות

- **שגיאת authentication**: בדוק שה-App Password נכון (16 תווים עם רווחים)
- **שגיאת connection**: בדוק חיבור לאינטרנט
- **אימייל לא מגיע**: בדוק תיקיית Spam
- **שגיאת port**: נסה port 465 עם `MAIL_ENCRYPTION=ssl`

