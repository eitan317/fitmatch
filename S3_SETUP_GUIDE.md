# מדריך הגדרת S3 ל-FitMatch

## למה צריך S3?

ב-Railway, כל פעם שאתה עושה Redeploy, ה-container נבנה מחדש וכל הקבצים ב-`storage/` נמחקים. לכן התמונות של המאמנים נעלמות.

**הפתרון:** שמירת התמונות ב-AWS S3 (cloud storage) במקום ב-container.

---

## שלב 1: יצירת S3 Bucket ב-AWS

1. היכנס ל-[AWS Console](https://console.aws.amazon.com/)
2. לך ל-**S3** → **Create bucket**
3. הגדרות:
   - **Bucket name**: `fitmatch-trainer-images` (או שם אחר)
   - **Region**: בחר קרוב (למשל `us-east-1`)
   - **Block Public Access**: **בטל את הסימון** (או הגדר Public Read)
   - לחץ **Create bucket**

---

## שלב 2: יצירת IAM User עם הרשאות

1. לך ל-**IAM** → **Users** → **Create user**
2. שם: `fitmatch-s3-user`
3. **Attach policies**: בחר `AmazonS3FullAccess` (או רק Read/Write ל-bucket ספציפי)
4. לחץ **Create user**
5. לך ל-**Security credentials** → **Create access key**
6. בחר **Application running outside AWS**
7. **שמור את ה-Access Key ID וה-Secret Access Key** - תצטרך אותם!

---

## שלב 3: הגדרת Bucket Policy (למתן גישה ציבורית)

1. לך ל-S3 → בחר את ה-Bucket שלך
2. לך ל-**Permissions** → **Bucket Policy**
3. הוסף את ה-Policy הבא (החלף `fitmatch-trainer-images` בשם ה-Bucket שלך):

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "PublicReadGetObject",
            "Effect": "Allow",
            "Principal": "*",
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::fitmatch-trainer-images/*"
        }
    ]
}
```

4. לחץ **Save**

---

## שלב 4: התקנת החבילה (אם לא הותקנה)

הרץ בפרויקט:

```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
```

---

## שלב 5: הוספת משתנים ב-Railway

הוסף ב-Railway (Service → Variables):

```
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_access_key_id_here
AWS_SECRET_ACCESS_KEY=your_secret_access_key_here
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=fitmatch-trainer-images
AWS_USE_PATH_STYLE_ENDPOINT=false
```

**חשוב:**
- החלף `your_access_key_id_here` ב-Access Key ID האמיתי
- החלף `your_secret_access_key_here` ב-Secret Access Key האמיתי
- החלף `us-east-1` ב-Region שבחרת
- החלף `fitmatch-trainer-images` בשם ה-Bucket שלך

---

## שלב 6: Redeploy

לאחר הוספת המשתנים, Railway יבצע Redeploy אוטומטית. אם לא, לחץ **Deploy** ידנית.

---

## בדיקה

1. גש לאתר: `https://www.fitmatch.org.il`
2. נסה להעלות תמונה של מאמן
3. בדוק שהתמונה נשמרת ונצגת
4. עשה Redeploy
5. בדוק שהתמונה עדיין קיימת (לא נמחקה!)

---

## הערות חשובות

- **תמונות קיימות**: התמונות שכבר נשמרו ב-local storage לא יעברו אוטומטית ל-S3. תצטרך להעלות אותן מחדש.
- **עלויות**: AWS S3 Free Tier כולל 5GB למשך שנה. אחרי זה, העלות היא כ-$0.023 ל-GB לחודש.
- **ביצועים**: התמונות יטענו מהר יותר מ-S3 (CDN של AWS).

---

## פתרון בעיות

### התמונות לא נשמרות
- בדוק שה-Access Keys נכונים
- בדוק שה-Bucket Policy נכון
- בדוק את ה-Logs ב-Railway

### שגיאת "Access Denied"
- ודא שה-IAM User יש לו הרשאות ל-S3
- ודא שה-Bucket Policy מאפשר Public Read

### התמונות לא נטענות
- בדוק שה-Bucket Policy נכון
- בדוק שה-URL של התמונה נכון (אמור להתחיל ב-`https://`)

---

## חזרה ל-Local Storage (אם צריך)

אם תרצה לחזור ל-local storage, פשוט שנה ב-Railway:

```
FILESYSTEM_DISK=local
```

והסר את שאר המשתנים של AWS.

---

## סיכום

לאחר הגדרת S3:
- ✅ התמונות נשמרות ב-cloud (לא נמחקות ב-Redeploy)
- ✅ התמונות נטענות מהר יותר
- ✅ אין בעיות עם symlinks
- ✅ אמין וזמין תמיד

**הכל מוכן!** 🎉

