# איך לבדוק אם ה-Sitemap עובד

## 🎯 בדיקות מהירות

### 1. בדיקה בדפדפן (הכי פשוט)
פתח בדפדפן:
```
https://www.fitmatch.org.il/sitemap.xml
```

**מה לראות:**
- ✅ HTTP 200 (לא 404, לא 500)
- ✅ XML תקין (מתחיל ב-`<?xml version="1.0" encoding="UTF-8"?>`)
- ✅ כולל `<urlset>` עם URLs
- ✅ URLs מתחילים ב-`https://www.fitmatch.org.il`

---

### 2. בדיקה עם PowerShell Script
הרץ:
```powershell
.\check-sitemap.ps1
```

או עם URL מותאם:
```powershell
.\check-sitemap.ps1 https://www.fitmatch.org.il
```

**מה זה בודק:**
- ✅ HTTP Status (200)
- ✅ Content-Type (application/xml)
- ✅ XML תקין
- ✅ יש `<urlset>`
- ✅ מספר URLs
- ✅ URLs משתמשים ב-www

---

### 3. בדיקה עם curl
```bash
# בדיקת headers
curl -I https://www.fitmatch.org.il/sitemap.xml

# בדיקת תוכן
curl https://www.fitmatch.org.il/sitemap.xml
```

**צפוי:**
```
HTTP/2 200
Content-Type: application/xml; charset=utf-8

<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  ...
</urlset>
```

---

### 4. בדיקה עם PowerShell (ידנית)
```powershell
# בדיקת Status
$response = Invoke-WebRequest -Uri "https://www.fitmatch.org.il/sitemap.xml" -Method Head -UseBasicParsing
Write-Host "Status: $($response.StatusCode)"
Write-Host "Content-Type: $($response.Headers['Content-Type'])"

# בדיקת תוכן
$content = (Invoke-WebRequest -Uri "https://www.fitmatch.org.il/sitemap.xml" -UseBasicParsing).Content
Write-Host "Length: $($content.Length) bytes"
Write-Host "Has XML: $(if ($content -match '^\s*<\?xml') { 'Yes' } else { 'No' })"
Write-Host "Has urlset: $(if ($content -match '<urlset') { 'Yes' } else { 'No' })"
```

---

## 🔍 מה לבדוק בתוכן

### תוכן תקין:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
  <url>
    <loc>https://www.fitmatch.org.il/he/</loc>
    <lastmod>2024-01-11T18:06:26+00:00</lastmod>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
    <xhtml:link rel="alternate" hreflang="he" href="https://www.fitmatch.org.il/he/" />
    <xhtml:link rel="alternate" hreflang="en" href="https://www.fitmatch.org.il/en/" />
    ...
  </url>
  ...
</urlset>
```

### מה צריך להיות:
- ✅ מתחיל ב-`<?xml version="1.0" encoding="UTF-8"?>`
- ✅ כולל `<urlset xmlns="...">`
- ✅ יש `<url>` entries
- ✅ כל `<url>` כולל `<loc>`, `<lastmod>`, `<changefreq>`, `<priority>`
- ✅ URLs מתחילים ב-`https://www.fitmatch.org.il`
- ✅ יש hreflang tags לכל שפה (he, en, ru, ar)

---

## 📋 Checklist

### בדיקה בסיסית:
- [ ] HTTP Status: 200 (לא 404, לא 500)
- [ ] Content-Type: application/xml (או application/xml; charset=utf-8)
- [ ] XML תקין (מתחיל ב-`<?xml`)

### בדיקת תוכן:
- [ ] יש `<urlset>` tag
- [ ] יש לפחות כמה `<url>` entries
- [ ] כל `<url>` כולל `<loc>` עם URL תקין
- [ ] URLs מתחילים ב-`https://www.fitmatch.org.il`

### בדיקת URLs:
- [ ] יש דפים סטטיים: `/`, `/trainers`, `/about`, `/faq`, `/contact`
- [ ] יש פרופילי מאמנים: `/trainers/{id}`
- [ ] יש hreflang tags לכל שפה

---

## 🚨 מה לעשות אם זה לא עובד

### אם מקבל 404:
1. **בדוק שה-route רשום:**
   ```bash
   php artisan route:list | findstr sitemap
   ```

2. **בדוק Railway Logs:**
   - Railway Dashboard → Service → Logs
   - חפש: "SitemapController::index() called"
   - אם לא רואה → הבקשה לא מגיעה ל-Laravel

3. **בדוק שה-deployment הושלם:**
   - Railway Dashboard → Service → Deployments
   - בדוק שהדפלוימנט האחרון הצליח

4. **בדוק שה-Procfile משתמש ב-router.php:**
   - `web: ... php -S 0.0.0.0:\$PORT -t public public/router.php`

### אם מקבל 500:
1. **בדוק Railway Logs:**
   - Railway Dashboard → Service → Logs
   - חפש שגיאות PHP

2. **בדוק שה-DB זמין:**
   - (אבל זה לא אמור לעצור - יש try-catch)

### אם התוכן לא תקין:
1. **בדוק Railway Logs:**
   - חפש: "SitemapController::index() called"
   - בדוק אם יש שגיאות

2. **בדוק שה-APP_URL עודכן:**
   - Railway Dashboard → Service → Variables
   - `APP_URL=https://www.fitmatch.org.il`

---

## 🎯 בדיקה מקומית (לא מומלץ)

**הערה:** השרת המקומי לא עובד עם router.php (צריך להריץ עם router.php), אז עדיף לבדוק ב-production.

אבל אם רוצים לבדוק מקומית:
```bash
# צריך להריץ עם router.php
cd public
php -S 127.0.0.1:8000 router.php

# אז לבדוק
curl http://127.0.0.1:8000/sitemap.xml
```

---

## ✅ סיכום

**הדרך הכי פשוטה:**
1. פתח בדפדפן: `https://www.fitmatch.org.il/sitemap.xml`
2. אם רואה XML תקין → עובד ✅
3. אם רואה 404/500 → לא עובד ❌

**הדרך הכי מפורטת:**
1. הרץ: `.\check-sitemap.ps1`
2. הסקריפט יבדוק הכל ויגיד לך מה מצב
