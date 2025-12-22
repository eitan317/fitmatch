# 🚀 איך להריץ Migration דרך Railway Dashboard (פאנל הניהול)

## 📋 הוראות מפורטות:

### שלב 1: התחבר ל-Railway
1. לך ל: **https://railway.app**
2. התחבר לחשבון שלך

### שלב 2: פתח את הפרויקט
1. לחץ על הפרויקט שלך: **FitMatch**
2. אתה תראה את ה-services שלך

### שלב 3: פתח Console/Terminal
1. לחץ על ה-service הראשי שלך (זה שיש לו את האפליקציה)
2. למעלה יש לשוניות: **"Deployments"**, **"Logs"**, **"Variables"** וכו'
3. חפש לשונית בשם: **"Shell"** או **"Console"** או **"Terminal"** או **"CLI"**
4. לחץ עליה

### שלב 4: הרץ את הפקודה
1. בתוך ה-Console, תראה שורת פקודה (כמו terminal)
2. הקלד:
   ```bash
   php artisan migrate --force
   ```
3. לחץ Enter

### שלב 5: בדוק שהצליח
1. אתה תראה הודעות על migrations שרצים
2. אם הכל בסדר, תראה משהו כמו: "Migrating..." ואז "Migrated successfully"
3. אם יש שגיאה - תראה אותה

---

## 🎯 אם אין לך לשונית Console:

### אופציה 1: דרך Logs
1. לחץ על **"Logs"**
2. חפש כפתור **"Open Shell"** או **"Terminal"**

### אופציה 2: דרך Deployments
1. לחץ על **"Deployments"**
2. לחץ על ה-deployment האחרון
3. חפש **"View Logs"** או **"Shell"**

### אופציה 3: דרך Settings
1. לחץ על **"Settings"**
2. חפש **"Shell"** או **"CLI"** או **"Terminal"**

---

## ✅ אחרי שהרצת:

הטופס "הרשמה כמאמן" יעבוד בפרודקשן! 🎉

---

## 💡 טיפ:
אם אתה לא מוצא את ה-Console, נסה לבדוק:
- האם אתה במסך הנכון (service ולא project)
- האם יש לך הרשאות (admin)
- נסה לרענן את הדף

