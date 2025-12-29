# תיקון מהיר לבעיית שליחת אימיילים

## הבעיה
קודי OTP לא מגיעים לאימייל.

## הפתרון המהיר (לבדיקה)

כרגע הקוד מוגדר לעבוד גם עם `log` driver. זה אומר:

1. **אם Mail driver הוא `log`**:
   - הקוד יופיע ישירות במסך (בצהוב)
   - הקוד יישמר ב-`storage/logs/laravel.log`
   - זה לבדיקה בלבד!

2. **כדי לשלוח אימיילים אמיתיים**:
   - שנה ב-`.env`: `MAIL_MAILER=smtp`
   - הוסף: `MAIL_PASSWORD=your_gmail_app_password`
   - הרץ: `php artisan config:clear`

## איך לבדוק עכשיו:

1. גש ל-`/register`
2. הזן אימייל ולחץ "שלח קוד"
3. **אם Mail driver הוא `log`** - הקוד יופיע ישירות במסך!
4. **אם Mail driver הוא `smtp`** - הקוד יישלח לאימייל

## הגדרת Gmail SMTP (לשליחה אמיתית):

1. קבל App Password מ-Gmail: https://myaccount.google.com/apppasswords
2. עדכן את `.env`:
   ```
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=fitmatchcoil@gmail.com
   MAIL_PASSWORD=xxxx xxxx xxxx xxxx
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=fitmatchcoil@gmail.com
   MAIL_FROM_NAME="FitMatch"
   ```
3. הרץ: `php artisan config:clear`

## בדיקת Logs

תמיד אפשר לבדוק את `storage/logs/laravel.log` - שם תראה:
- את הקוד (אם Mail driver הוא `log`)
- שגיאות (אם יש בעיה עם SMTP)

