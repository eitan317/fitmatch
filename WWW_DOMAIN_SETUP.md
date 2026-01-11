# WWW Domain Setup - Make www.fitmatch.org.il the Canonical Domain

## Problem
- `https://www.fitmatch.org.il` works (HTTP 200) ✅
- `https://fitmatch.org.il` returns: `{"status":"error","code":404,"message":"Application not found"}` ❌

The apex domain (fitmatch.org.il) is NOT connected to Railway service, while the www subdomain IS.

## Solution Implemented

### 1. Code Changes ✅

- ✅ Created `RedirectToWww` middleware to redirect apex → www (301)
- ✅ Added middleware to bootstrap/app.php (prepended to web group)
- ✅ Updated `env.example.template` to use `APP_URL=https://www.fitmatch.org.il`
- ✅ All code already uses `config('app.url')` - will use www once APP_URL is updated

### 2. Required Actions in Railway Dashboard

**Update APP_URL environment variable:**
1. Go to Railway Dashboard → Your Project → Your Service
2. Go to: **Variables** tab
3. Find: `APP_URL`
4. Update to: `https://www.fitmatch.org.il`
5. Save (this will trigger a redeploy)

### 3. Connect Apex Domain to Railway (Optional but Recommended)

**Option A: Connect apex to Railway (Recommended)**
1. Go to Railway Dashboard → Your Service → Settings → Domains
2. Click: **"Add Domain"**
3. Enter: `fitmatch.org.il`
4. Railway will provide DNS records
5. Add DNS records in your DNS provider
6. Once connected, the `RedirectToWww` middleware will redirect apex → www (301)

**Option B: DNS-level redirect (Alternative)**
- Some DNS providers (Cloudflare, etc.) support URL forwarding/redirects
- Set up redirect at DNS level: `fitmatch.org.il` → `www.fitmatch.org.il` (301)

## Files Changed

1. **app/Http/Middleware/RedirectToWww.php** (NEW)
   - Redirects `fitmatch.org.il` → `www.fitmatch.org.il` (301)
   - Only redirects apex domain, ignores www/localhost/Railway subdomains

2. **bootstrap/app.php**
   - Added `RedirectToWww` middleware (prepended to web group)

3. **env.example.template**
   - Updated `APP_URL=https://www.fitmatch.org.il`

4. **All existing code** (No changes needed)
   - Already uses `config('app.url')` in:
     - `SitemapController` → `config('app.url')`
     - `routes/web.php` (robots.txt) → `config('app.url')`
     - `resources/views/partials/seo-meta.blade.php` → `config('app.url')`
     - `resources/views/partials/schema-ld.blade.php` → `config('app.url')`
   - Will automatically use www once `APP_URL` is updated

## Verification Steps

### After updating APP_URL in Railway:

1. **Test www domain:**
   ```bash
   curl -I https://www.fitmatch.org.il/sitemap.xml
   ```
   Expected: `HTTP/2 200` + `Content-Type: application/xml`

2. **Test sitemap content:**
   ```bash
   curl https://www.fitmatch.org.il/sitemap.xml
   ```
   Expected: XML with URLs starting with `https://www.fitmatch.org.il`

3. **Check Railway logs:**
   - Should see: `SitemapController::index() called`
   - URLs in sitemap should use `www.fitmatch.org.il`

### After connecting apex domain to Railway:

4. **Test apex domain redirect:**
   ```bash
   curl -I https://fitmatch.org.il
   ```
   Expected: `HTTP/2 301` + `Location: https://www.fitmatch.org.il`

5. **Test apex domain with path:**
   ```bash
   curl -I https://fitmatch.org.il/trainers
   ```
   Expected: `HTTP/2 301` + `Location: https://www.fitmatch.org.il/trainers`

## Important Notes

⚠️ **The redirect middleware will ONLY work if apex domain is connected to Railway.**

If apex domain is NOT connected to Railway, requests to `fitmatch.org.il` will return Railway's 404 page BEFORE reaching Laravel code.

**To make redirect work:**
- Connect apex domain to Railway (see "Connect Apex Domain" above)
- OR use DNS-level redirect (see Option B above)

## Current Status

✅ Code is ready - all URLs will use www once APP_URL is updated
✅ Redirect middleware is in place (will work once apex is connected)
⏳ **ACTION REQUIRED**: Update `APP_URL` in Railway Dashboard
⏳ **ACTION REQUIRED (Optional)**: Connect apex domain to Railway

## Summary

1. ✅ Code changes complete
2. ⏳ Update `APP_URL=https://www.fitmatch.org.il` in Railway
3. ⏳ (Optional) Connect `fitmatch.org.il` to Railway for redirect to work
4. ✅ Verify: `https://www.fitmatch.org.il/sitemap.xml` returns 200

After these steps, www will be the canonical domain and all traffic will use www.
