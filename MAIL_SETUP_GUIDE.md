# מדריך הגדרת Mail לשליחת קודי OTP

## הבעיה
אם קודי OTP לא מגיעים לאימייל, זה כנראה בגלל שהגדרות Mail לא מוגדרות נכון.

## פתרון מהיר - שימוש ב-Log Driver (לבדיקה)

לבדיקה מהירה, אפשר לראות את הקודים ב-log:

1. פתח את `.env` והוסף/עדכן:
```env
MAIL_MAILER=log
```

2. הרץ:
```bash
php artisan config:clear
```

3. נסה לשלוח קוד - הקוד יופיע ב-`storage/logs/laravel.log`

## פתרון קבוע - הגדרת Gmail SMTP

### שלב 1: יצירת App Password ב-Gmail

1. היכנס לחשבון Google: `fitmatchcoil@gmail.com`
2. עבור ל-[Google Account Security](https://myaccount.google.com/security)
3. הפעל "2-Step Verification" (אם לא מופעל)
4. עבור ל-[App Passwords](https://myaccount.google.com/apppasswords)
5. צור App Password חדש:
   - בחר "Mail" ו-"Other (Custom name)"
   - שם: "FitMatch Website"
   - העתק את הסיסמה שנוצרה (16 תווים, עם רווחים)

### שלב 2: הוספת הגדרות SMTP ל-.env

פתח את קובץ `.env` והוסף/עדכן את השורות הבאות:

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

### שלב 3: ניקוי Cache

הרץ את הפקודות הבאות:

```bash
php artisan config:clear
php artisan cache:clear
```

### שלב 4: בדיקה

1. נסה לשלוח קוד אימות דרך האתר
2. בדוק את `storage/logs/laravel.log` לראות אם יש שגיאות
3. אם יש שגיאה, בדוק את ההודעה המדויקת

## פתרון חלופי - Mailtrap (לפיתוח)

לפיתוח מקומי, אפשר להשתמש ב-Mailtrap (חינמי):

1. הירשם ב-[Mailtrap](https://mailtrap.io)
2. קבל את ה-credentials מה-Inbox שלך
3. הוסף ל-.env:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@fitmatch.org.il
MAIL_FROM_NAME="FitMatch"
```

## בדיקת הגדרות

הרץ את הפקודה הבאה כדי לבדוק את ההגדרות:

```bash
php artisan mail:test your-email@example.com
```

זה ישלח אימייל בדיקה ויראה לך את כל ההגדרות.

## פתרון בעיות

### שגיאת "Connection refused"
- בדוק שה-`MAIL_HOST` נכון
- בדוק שה-`MAIL_PORT` נכון
- בדוק חיבור לאינטרנט

### שגיאת "Authentication failed"
- בדוק שה-`MAIL_USERNAME` נכון
- בדוק שה-`MAIL_PASSWORD` הוא App Password (לא סיסמה רגילה)
- ודא ש-2-Step Verification מופעל ב-Gmail

### אימייל לא מגיע
- בדוק תיקיית Spam
- בדוק את `storage/logs/laravel.log` לראות אם יש שגיאות
- ודא שהקוד נשלח (בדוק ב-log)

### Mail driver is "log"
אם ה-Mail driver מוגדר ל-"log", האימיילים נשמרים ב-log ולא נשלחים. שנה ל-"smtp" ב-.env.

## בדיקת Logs

לבדוק מה קורה, פתח את:
```
storage/logs/laravel.log
```

חפש:
- "Sending verification email" - ניסיון לשלוח
- "Verification email sent successfully" - הצלחה
- "Error sending verification email" - שגיאה

## הערות חשובות

1. **ב-Railway**: ודא שהגדרות Mail ב-Railway Environment Variables מוגדרות נכון
2. **ב-Production**: אל תשתמש ב-"log" driver - זה רק לבדיקה
3. **App Password**: תמיד השתמש ב-App Password, לא בסיסמה הרגילה

