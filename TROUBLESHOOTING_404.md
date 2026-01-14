# Troubleshooting 404 on /sitemap.xml

## 🚨 Problem: Getting 404 on /sitemap.xml

If you're seeing "Failed to load resource: the server responded with a status of 404", here's how to fix it:

---

## ✅ Step 1: Verify Changes Are Committed

**Check if changes are committed:**
```bash
git status
```

**If you see modified files, commit them:**
```bash
git add public/router.php Procfile app/Http/Middleware/RedirectToWww.php bootstrap/app.php env.example.template
git commit -m "Fix sitemap.xml routing: Use router.php, add www redirect"
git push
```

---

## ✅ Step 2: Verify Procfile Uses router.php

**Check Procfile content:**
```bash
cat Procfile
```

**Should be:**
```
web: php artisan storage:link || true; php artisan migrate --force || true; php artisan config:clear; php artisan route:clear; php artisan cache:clear; sh -c "php -S 0.0.0.0:\$PORT -t public public/router.php"
```

**Must include:** `public/router.php` at the end

---

## ✅ Step 3: Verify router.php Exists

**Check if router.php exists:**
```bash
ls -la public/router.php
```

**Should exist and contain:**
- Check for `/sitemap.xml` FIRST
- Route to Laravel: `require __DIR__ . '/index.php'`
- Exit after sitemap routing

---

## ✅ Step 4: Wait for Railway Deployment

After pushing to git:
1. Railway will automatically deploy (2-3 minutes)
2. Check Railway Dashboard → Deployments
3. Wait for deployment to complete

---

## ✅ Step 5: Check Railway Logs

After deployment, check Railway logs:
1. Railway Dashboard → Service → Logs
2. Look for: `[404]: GET /sitemap.xml - No such file or directory`
3. If you see this → router.php is NOT being used
4. If you DON'T see this → router.php IS working, check Laravel logs

---

## ✅ Step 6: Test the URL

**Test with curl:**
```bash
curl -I https://www.fitmatch.org.il/sitemap.xml
```

**Expected:**
- `HTTP/2 200` (not 404)
- `Content-Type: application/xml; charset=utf-8`

**Test content:**
```bash
curl https://www.fitmatch.org.il/sitemap.xml
```

**Expected:** Valid XML with `<urlset>`

---

## 🚨 Common Issues

### Issue 1: Changes Not Deployed
**Symptom:** Still getting 404 after making changes
**Solution:** 
- Commit and push changes
- Wait for Railway deployment
- Check Railway logs

### Issue 2: Procfile Not Using router.php
**Symptom:** Railway logs show "No such file or directory"
**Solution:**
- Check Procfile uses `public/router.php`
- Update Procfile if needed
- Redeploy

### Issue 3: router.php Not Found
**Symptom:** Deployment fails or router not working
**Solution:**
- Ensure `public/router.php` exists in repository
- Check file is committed: `git ls-files public/router.php`
- If not, add it: `git add public/router.php && git commit -m "Add router.php" && git push`

### Issue 4: Route Not Registered
**Symptom:** Laravel returns 404 (not PHP server)
**Solution:**
- Check route exists: `php artisan route:list | findstr sitemap`
- Check `routes/web.php` has sitemap route
- Clear route cache: `php artisan route:clear`

---

## 🔍 Debugging Steps

### 1. Check Railway Logs
```
Railway Dashboard → Service → Logs
```
Look for:
- `[404]: GET /sitemap.xml - No such file or directory` → Router not working
- `SitemapController::index() called` → Router working, Laravel handling request
- PHP errors → Check code

### 2. Check Route Registration
If you can SSH into Railway (if supported):
```bash
php artisan route:list | grep sitemap
```

Should show:
```
GET|HEAD  /sitemap.xml ................................ sitemap.xml
```

### 3. Test router.php Locally
```bash
cd public
php -S 127.0.0.1:8000 router.php
```

Then visit: `http://127.0.0.1:8000/sitemap.xml`

Should work if router.php is correct.

---

## ✅ Verification Checklist

After deployment, verify:

- [ ] Changes committed and pushed
- [ ] Railway deployment completed
- [ ] Procfile uses `public/router.php`
- [ ] `public/router.php` exists in repository
- [ ] Railway logs don't show "No such file or directory"
- [ ] `curl -I https://www.fitmatch.org.il/sitemap.xml` returns 200
- [ ] `curl https://www.fitmatch.org.il/sitemap.xml` returns XML

---

## 📝 Summary

**If getting 404:**

1. ✅ Commit and push changes
2. ✅ Wait for Railway deployment
3. ✅ Check Railway logs
4. ✅ Test with curl
5. ✅ Verify route is registered

**Most common cause:** Changes not deployed (forgot to commit/push)
