# 🖥️ מדריך להרצת Migration דרך Railway Console

## 📍 איפה למצוא את ה-Console:

### דרך 1: דרך Service View
```
Railway Dashboard
  └── Project: FitMatch
      └── Service: [שם השירות שלך]
          └── לשונית "Shell" / "Console" / "Terminal"
```

### דרך 2: דרך Logs
```
Service
  └── Logs
      └── כפתור "Open Shell" / "Terminal" (בפינה הימנית העליונה)
```

### דרך 3: דרך Deployments
```
Service
  └── Deployments
      └── Deployment אחרון
          └── "View Logs" → "Shell"
```

---

## ⌨️ מה להקלד ב-Console:

```bash
php artisan migrate --force
```

---

## ✅ מה אמור לקרות:

אם הכל עובד, תראה משהו כמו:
```
Migrating: 2025_12_22_185000_add_owner_email_to_trainers_table
Migrated:  2025_12_22_185000_add_owner_email_to_trainers_table (XX.XXms)
```

---

## ❌ אם יש שגיאה:

תראה את השגיאה. בדרך כלל:
- אם העמודה כבר קיימת - זה בסדר (Nothing to migrate)
- אם יש שגיאה אחרת - תעתיק אותה ותשלח לי

