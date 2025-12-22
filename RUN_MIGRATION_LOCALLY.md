# 🔧 איך להריץ Migration בלוקאלי

## ⚠️ שים לב:
הפקודה `web: php artisan migrate...` היא חלק מה-`Procfile` ל-Railway, לא פקודה להרצה ישירה!

---

## ✅ איך להריץ Migration בלוקאלי:

### Windows (PowerShell / CMD):
```bash
php artisan migrate
```

או אם צריך force:
```bash
php artisan migrate --force
```

---

## 🚀 מה קורה:

1. **בלוקאלי**: אתה מריץ `php artisan migrate` ידנית
2. **בפרודקשן (Railway)**: ה-Procfile מריץ את זה אוטומטית בסטארט

---

## 📋 צעדים:

1. פתח Terminal/PowerShell בתיקייה: `C:\laragon\www\fitmatch`
2. הרץ:
   ```bash
   php artisan migrate
   ```
3. זה יוסיף את העמודה `owner_email` לטבלה

---

## ✅ אחרי שהרצת:
הטופס "הרשמה כמאמן" יעבוד בלוקאלי גם!

