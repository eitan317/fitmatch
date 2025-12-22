# 🔧 תיקון שגיאת owner_email

## הבעיה:
העמודה `owner_email` לא קיימת בטבלת `trainers` בבסיס הנתונים בפרודקשן.

## הפתרון:
יצרתי migration חדש שיוסיף את העמודה אם היא לא קיימת.

---

## 📋 קבצים שנוצרו/שונו:

### 1. `database/migrations/2025_12_22_185000_add_owner_email_to_trainers_table.php` (חדש)
- **תפקיד**: מוסיף את העמודה `owner_email` לטבלת `trainers` אם היא לא קיימת
- **מה הוא עושה**: בודק אם הטבלה קיימת, ואז בודק אם העמודה קיימת, ואם לא - מוסיף אותה

---

## 🚀 איך להריץ בפרודקשן:

### דרך 1: דרך Railway Console
1. לך ל-Railway Dashboard
2. פתח את ה-Console/Terminal של השרת
3. הרץ:
   ```bash
   php artisan migrate
   ```

### דרך 2: דרך SSH (אם יש גישה)
```bash
php artisan migrate
```

---

## ⚠️ חשוב:
אחרי שהרצת את ה-migration, הטופס "הרשמה כמאמן" אמור לעבוד.

---

## ✅ סיכום:
1. ה-migration מוכן
2. צריך להריץ `php artisan migrate` בפרודקשן
3. אחרי זה הכל יעבוד!

