# 🚀 איך להריץ Migration ב-Railway (ידנית)

## ⚠️ הבעיה:
ה-migration לא רץ אוטומטית, צריך להריץ אותו ידנית.

---

## 📋 איך להריץ Migration ב-Railway:

### דרך 1: דרך Railway Console (הכי קל!)

1. לך ל-Railway Dashboard: https://railway.app
2. בחר את הפרויקט שלך (FitMatch)
3. לחץ על השירות (service) שלך
4. לחץ על הלשונית **"Deployments"** או **"Logs"**
5. לחץ על **"View Logs"** או **"Open Shell"** / **"Console"**
6. בתוך ה-Console, הרץ:
   ```bash
   php artisan migrate --force
   ```

### דרך 2: דרך Railway CLI (אם מותקן)

אם יש לך Railway CLI מותקן:
```bash
railway run php artisan migrate --force
```

---

## ✅ אחרי שהרצת:
הטופס "הרשמה כמאמן" אמור לעבוד!

---

## 🔍 איך לבדוק שהעמודה נוספה:

אחרי שהרצת את ה-migration, תוכל לבדוק עם:
```bash
php artisan tinker
```
ואז:
```php
Schema::hasColumn('trainers', 'owner_email')
```
אם זה מחזיר `true` - העמודה קיימת! ✅

---

## 💡 למה זה לא רץ אוטומטית?

יכול להיות שה-Procfile לא רץ migrations כמו שצריך, או שה-migration לא היה קיים בזמן הדפלוי.

להבא, ה-migration ירוץ אוטומטית.

