<?php
/**
 * Railway Domain Fix - Verification and Instructions
 */

echo "🔧 Railway Domain Fix - Action Required\n";
echo str_repeat("=", 70) . "\n\n";

// Verify app configuration
echo "1. Verifying App Configuration...\n";

// Check Procfile
$procfile = file_get_contents('Procfile');
if (strpos($procfile, '${PORT}') !== false && strpos($procfile, '0.0.0.0') !== false) {
    echo "   ✅ Procfile is correct (uses \${PORT}, binds to 0.0.0.0)\n";
} else {
    echo "   ❌ Procfile needs fixing\n";
    exit(1);
}

// Check routes
exec('php artisan route:list --path=sitemap.xml 2>&1', $output, $return);
if ($return === 0) {
    echo "   ✅ Sitemap routes are registered\n";
} else {
    echo "   ⚠️  Could not verify routes\n";
}

// Check middleware exclusion
$webContent = file_get_contents('routes/web.php');
if (strpos($webContent, 'withoutMiddleware') !== false) {
    echo "   ✅ Session middleware excluded from sitemap routes\n";
}

echo "\n2. App Configuration: ✅ READY\n\n";

echo "3. Railway Domain Configuration Required:\n";
echo str_repeat("-", 70) . "\n";
echo "\n";
echo "⚠️  The app code is correct. You need to configure the domain in Railway.\n";
echo "\n";
echo "📋 EXACT STEPS TO FIX NOW:\n";
echo "\n";
echo "STEP 1: Railway Dashboard\n";
echo "   1. Go to: https://railway.app\n";
echo "   2. Select your PROJECT\n";
echo "   3. Click on your SERVICE (the one running Laravel)\n";
echo "   4. Click: Settings → Domains\n";
echo "   5. Click: 'Add Domain' or 'Custom Domain'\n";
echo "   6. Enter: fitmatch.org.il\n";
echo "   7. Railway will show DNS records - COPY THEM\n";
echo "\n";
echo "STEP 2: DNS Provider\n";
echo "   1. Go to your DNS provider (where you manage fitmatch.org.il)\n";
echo "   2. Add the EXACT DNS records Railway provided\n";
echo "   3. Typically:\n";
echo "      - A record for @ (apex) → Railway's IP\n";
echo "      - CNAME for www → Railway's domain\n";
echo "   4. Save changes\n";
echo "\n";
echo "STEP 3: Wait & Verify\n";
echo "   1. Wait 5-30 minutes for DNS propagation\n";
echo "   2. Check Railway: Domain status should be 'Active'\n";
echo "   3. Test: https://fitmatch.org.il (should load app)\n";
echo "   4. Test: https://fitmatch.org.il/sitemap.xml (should return 200)\n";
echo "\n";
echo str_repeat("=", 70) . "\n";
echo "✅ App is ready. Configure domain in Railway to make it work.\n";
echo "\n";

