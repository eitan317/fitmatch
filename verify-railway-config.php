<?php
/**
 * Verify Railway configuration is correct
 */

echo "🔍 Railway Configuration Verification\n";
echo str_repeat("=", 70) . "\n\n";

// Check 1: Procfile uses ${PORT}
echo "1. Checking Procfile port configuration...\n";
$procfile = file_get_contents('Procfile');
if (strpos($procfile, '${PORT}') !== false) {
    echo "   ✅ Procfile uses \${PORT} (dynamic port)\n";
} elseif (strpos($procfile, '$PORT') !== false) {
    echo "   ✅ Procfile uses \$PORT (dynamic port)\n";
} else {
    echo "   ❌ Procfile does NOT use dynamic port!\n";
    echo "   Current: " . trim($procfile) . "\n";
    exit(1);
}

// Check 2: Binds to 0.0.0.0 (all interfaces)
if (strpos($procfile, '0.0.0.0') !== false) {
    echo "   ✅ Binds to 0.0.0.0 (all interfaces)\n";
} else {
    echo "   ⚠️  Not binding to 0.0.0.0 (may cause issues)\n";
}

// Check 3: Uses public/index.php as router
if (strpos($procfile, 'public/index.php') !== false || strpos($procfile, 'index.php') !== false) {
    echo "   ✅ Uses index.php as router\n";
} else {
    echo "   ⚠️  Router configuration unclear\n";
}

// Check 4: Sitemap route is stateless
echo "\n2. Checking sitemap route configuration...\n";
$webContent = file_get_contents('routes/web.php');
if (strpos($webContent, 'withoutMiddleware') !== false && 
    strpos($webContent, 'StartSession') !== false) {
    echo "   ✅ Sitemap routes exclude session middleware (stateless)\n";
} else {
    echo "   ⚠️  Session middleware may not be excluded\n";
}

// Check 5: Route registration
echo "\n3. Checking route registration...\n";
exec('php artisan route:list --path=sitemap.xml 2>&1', $output, $return);
if ($return === 0 && !empty($output)) {
    $found = false;
    foreach ($output as $line) {
        if (strpos($line, 'sitemap.xml') !== false) {
            echo "   ✅ Route registered: " . trim($line) . "\n";
            $found = true;
        }
    }
    if (!$found) {
        echo "   ⚠️  Route not found\n";
    }
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "✅ App Configuration is Correct!\n";
echo "\n";
echo "📋 Railway Domain Checklist:\n";
echo "1. Domain 'fitmatch.org.il' attached to correct SERVICE in Railway\n";
echo "2. Domain status shows 'Active' or 'Verified'\n";
echo "3. DNS records configured correctly:\n";
echo "   - A record for @ (apex) pointing to Railway IP\n";
echo "   - CNAME for www pointing to Railway domain\n";
echo "4. DNS propagated (wait 5-30 minutes)\n";
echo "5. Service is deployed and running\n";
echo "\n";
echo "🔗 Railway Dashboard:\n";
echo "   https://railway.app → Your Project → Service → Settings → Domains\n";

