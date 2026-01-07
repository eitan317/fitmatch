# Railway Domain Quick Fix Checklist

## ✅ App Configuration (Already Correct)

- ✅ Procfile uses `${PORT}` (dynamic port)
- ✅ Binds to `0.0.0.0` (all interfaces)
- ✅ Uses `public/index.php` as router
- ✅ Sitemap routes are stateless

## 🔧 Railway Domain Setup (Action Required)

### Step 1: Add Domain in Railway

1. Go to: **Railway Dashboard** → **Your Project** → **Your Service**
2. Click: **Settings** → **Domains**
3. Click: **"Add Domain"** or **"Custom Domain"**
4. Enter: `fitmatch.org.il`
5. Railway will show DNS records needed

### Step 2: Configure DNS Records

**Get EXACT records from Railway dashboard**, then add to your DNS provider:

#### Typical Configuration:

**For apex (fitmatch.org.il):**
```
Type: A
Name: @ (or blank)
Value: [Railway's IP - shown in dashboard]
TTL: 300
```

**For www (www.fitmatch.org.il):**
```
Type: CNAME
Name: www
Value: [Railway's domain - shown in dashboard]
TTL: 300
```

### Step 3: Wait & Verify

1. **Wait 5-30 minutes** for DNS propagation
2. **Check Railway:** Domain status should be "Active"
3. **Test:** `https://fitmatch.org.il` should load app
4. **Test:** `https://fitmatch.org.il/sitemap.xml` should return 200

## ⚠️ Common Mistakes

- ❌ Domain attached to wrong service
- ❌ DNS records don't match Railway's requirements
- ❌ Apex domain using CNAME (must use A record)
- ❌ Multiple conflicting DNS records
- ❌ DNS not propagated yet (wait longer)

## 🎯 What to Check in Railway

1. **Service → Settings → Domains:**
   - Is `fitmatch.org.il` listed?
   - Status: "Active" or "Verified"?
   - If "Pending": DNS not configured correctly

2. **Service → Deployments:**
   - Is service deployed and running?
   - Any deployment errors?

3. **Service → Logs:**
   - Is app starting correctly?
   - Any errors in logs?

## 📞 If Still Not Working

1. **Remove domain from Railway**
2. **Re-add domain** (Railway will show DNS records again)
3. **Configure DNS exactly as Railway specifies**
4. **Wait for propagation** (can take up to 48 hours)

## Status: ⚠️ DOMAIN CONFIGURATION NEEDED

The app is ready. Configure the domain in Railway and DNS.

