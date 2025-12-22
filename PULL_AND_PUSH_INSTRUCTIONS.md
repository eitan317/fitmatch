# 🔄 הוראות: Pull ואז Push

## הבעיה:
ה-remote repository (GitHub) מכיל שינויים שלא קיימים אצלך מקומית.
צריך לעדכן את הקוד המקומי לפני push.

---

## ✅ פתרון פשוט:

### שיטה 1: עם הסקריפט (מומלץ!)

1. לחץ לחיצה כפולה על: **`pull-then-push.bat`**
2. הסקריפט יעשה:
   - Pull (יעדכן את הקוד המקומי)
   - Push (ישלח את השינויים שלך)
3. אם יש conflict - תצטרך לפתור אותו ידנית

---

### שיטה 2: ידנית (אם הסקריפט לא עובד)

פתח **Git Bash** או **Terminal** והרץ:

```bash
# שלב 1: עדכן את הקוד המקומי
git pull origin main

# שלב 2: שלח את השינויים שלך
git push origin main
```

אם תתבקש להכניס credentials:
- **Username**: `eitan317`
- **Password**: `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`

---

## ⚠️ אם יש Conflicts (התנגשויות):

אם אחרי `git pull` יש conflicts:

1. Git יראה לך אילו קבצים יש בהם conflicts
2. פתח את הקבצים האלה
3. חפש את הסימנים: `<<<<<<<`, `=======`, `>>>>>>>`
4. תבחר איזה קוד לשמור
5. שמור את הקובץ
6. הרץ:
   ```bash
   git add .
   git commit -m "Resolve merge conflicts"
   git push origin main
   ```

---

## 🎯 סיכום:

**עכשיו הרץ**: `pull-then-push.bat`

זה יעדכן את הקוד וישלח את השינויים שלך!

