# Railway Domain Configuration - Complete Guide

## Problem
Getting Railway's edge 404 ("The train has not arrived at the station") means the domain is NOT properly attached to your Railway service. The request never reaches your Laravel app.

## ✅ Step 1: Verify Procfile (Already Correct)

**Current Procfile:**
```
php -S 0.0.0.0:${PORT} -t public public/index.php
```

✅ **CORRECT** - Uses `${PORT}` (dynamic, not hardcoded)
✅ **CORRECT** - Binds to `0.0.0.0` (all interfaces)

## 🔧 Step 2: Railway Domain Configuration

### 2.1 Check Domain Attachment

1. **Go to Railway Dashboard:**
   - https://railway.app
   - Select your **Project**
   - Click on your **Service** (the one running Laravel)

2. **Navigate to Settings → Domains:**
   - Click **"Settings"** tab
   - Click **"Domains"** section
   - Check if `fitmatch.org.il` is listed

3. **If domain is NOT listed:**
   - Click **"Add Domain"** or **"Custom Domain"**
   - Enter: `fitmatch.org.il`
   - Railway will provide DNS records to configure

4. **If domain IS listed:**
   - Check status: Should be **"Active"** or **"Verified"**
   - If status is **"Pending"** or **"Failed"**: DNS not configured correctly
   - Click on domain to see DNS requirements

### 2.2 Common Issues

❌ **Domain attached to wrong service:**
- Domain must be attached to the SERVICE running Laravel
- Not the project, not a different service

❌ **Domain not provisioned:**
- Domain must be added in Railway first
- Railway needs to provision the domain before DNS works

❌ **Service not deployed:**
- Service must be deployed and running
- Check "Deployments" tab - should show successful deployment

## 📋 Step 3: DNS Records Configuration

### For Apex Domain (fitmatch.org.il)

Railway typically provides one of these options:

#### Option A: A Record (Most Common)
```
Type: A
Name: @ (or leave blank, or "fitmatch.org.il")
Value: [Railway's IP address - provided in dashboard]
TTL: 300 (or default)
```

**Example:**
```
Type: A
Name: @
Value: 35.123.45.67
TTL: 300
```

#### Option B: ALIAS/ANAME Record (If supported)
Some DNS providers (e.g., Cloudflare, DNSimple) support ALIAS/ANAME:
```
Type: ALIAS (or ANAME)
Name: @
Value: [Railway's domain, e.g., your-app.up.railway.app]
TTL: 300
```

### For WWW Subdomain (www.fitmatch.org.il)

#### Option A: CNAME Record (Recommended)
```
Type: CNAME
Name: www
Value: [Railway's domain, e.g., your-app.up.railway.app]
TTL: 300
```

**Example:**
```
Type: CNAME
Name: www
Value: fitmatch-production.up.railway.app
TTL: 300
```

#### Option B: A Record (If CNAME not supported)
```
Type: A
Name: www
Value: [Same IP as apex domain]
TTL: 300
```

### ⚠️ Important DNS Rules

1. **No Conflicts:**
   - Don't have both A and CNAME for same name
   - Don't have multiple A records pointing to different IPs
   - Apex domain (@) can only use A record (not CNAME)

2. **Wait for Propagation:**
   - DNS changes take 5-30 minutes (up to 48 hours)
   - Use `nslookup` or `dig` to verify DNS resolution

3. **Check DNS Provider:**
   - Some providers have special requirements
   - Cloudflare: May need to disable proxy (gray cloud) for A records
   - GoDaddy: May need to use @ for apex domain

## 🔍 Step 4: Verification Steps

### 4.1 Verify DNS Resolution

```bash
# Check apex domain
nslookup fitmatch.org.il
dig fitmatch.org.il

# Check www subdomain
nslookup www.fitmatch.org.il
dig www.fitmatch.org.il
```

**Expected:**
- Should resolve to Railway's IP or domain
- If it doesn't resolve, DNS not configured or not propagated

### 4.2 Verify Railway Domain Status

In Railway Dashboard → Service → Settings → Domains:
- Status should be **"Active"** or **"Verified"**
- If **"Pending"**: DNS not configured correctly
- If **"Failed"**: Check DNS records match Railway's requirements

### 4.3 Test Domain Connection

1. **First test:** `https://fitmatch.org.il`
   - Should load your Laravel app (not Railway 404)
   - If Railway 404: Domain not attached or DNS not working

2. **Then test:** `https://fitmatch.org.il/sitemap.xml`
   - Should return HTTP 200 with XML
   - If still 404: Domain issue (not app issue)

## 🛠️ Step 5: Troubleshooting

### Issue: Domain shows "Pending" in Railway

**Solution:**
1. Check DNS records match Railway's requirements exactly
2. Wait 5-30 minutes for DNS propagation
3. Verify DNS resolution with `nslookup` or `dig`
4. Check for conflicting DNS records

### Issue: Domain shows "Failed" in Railway

**Solution:**
1. Remove domain from Railway
2. Re-add domain
3. Configure DNS records exactly as Railway specifies
4. Wait for verification

### Issue: Domain loads but shows Railway 404

**Solution:**
1. Verify domain is attached to CORRECT service
2. Check service is deployed and running
3. Check service logs for errors
4. Verify Procfile is correct (already verified ✅)

### Issue: DNS resolves but domain doesn't load

**Solution:**
1. Check Railway service is running
2. Check Railway logs for errors
3. Verify environment variables are set
4. Check service health status

## 📝 Step 6: Exact DNS Records (Get from Railway)

**Important:** Railway provides the EXACT DNS records needed. Follow these steps:

1. **In Railway Dashboard:**
   - Service → Settings → Domains
   - Click "Add Domain" or click on existing domain
   - Railway will show EXACT DNS records needed

2. **Copy the records exactly:**
   - Type (A, CNAME, etc.)
   - Name (@, www, etc.)
   - Value (IP or domain)
   - TTL

3. **Configure in your DNS provider:**
   - Go to your DNS provider (Cloudflare, GoDaddy, etc.)
   - Add the records exactly as Railway specifies
   - Save changes

4. **Wait for propagation:**
   - 5-30 minutes typically
   - Up to 48 hours in rare cases

## ✅ Verification Checklist

After configuring DNS:

- [ ] DNS records added exactly as Railway specifies
- [ ] DNS resolution works (`nslookup fitmatch.org.il` resolves)
- [ ] Railway domain status shows "Active" or "Verified"
- [ ] Domain attached to CORRECT service (the one running Laravel)
- [ ] Service is deployed and running
- [ ] `https://fitmatch.org.il` loads app (not Railway 404)
- [ ] `https://fitmatch.org.il/sitemap.xml` returns HTTP 200

## 🚀 Next Steps

1. **Configure DNS in Railway Dashboard:**
   - Add domain if not added
   - Copy DNS records Railway provides

2. **Configure DNS in your DNS provider:**
   - Add records exactly as Railway specifies
   - Wait for propagation

3. **Verify:**
   - Check Railway domain status
   - Test domain loads app
   - Test sitemap returns 200

## Status: ⚠️ DOMAIN CONFIGURATION REQUIRED

The app code is correct. You need to configure the domain in Railway and DNS.

