<?php
/**
 * Comprehensive verification of sitemap fix
 */

echo "🔍 Comprehensive Sitemap Fix Verification\n";
echo str_repeat("=", 70) . "\n\n";

$allGood = true;

// Test 1: Router file exists and has correct logic
echo "1. Checking router.php file...\n";
if (!file_exists('public/router.php')) {
    echo "   ❌ router.php NOT FOUND\n";
    $allGood = false;
} else {
    echo "   ✅ router.php exists\n";
    $routerContent = file_get_contents('public/router.php');
    
    // Check for key components
    $checks = [
        'sitemap.*\.xml' => 'Handles sitemap.xml requests',
        'exit' => 'Uses exit() to stop execution',
        'require __DIR__ . \'/index.php\'' => 'Routes to Laravel index.php',
        'SCRIPT_NAME' => 'Sets SCRIPT_NAME correctly',
    ];
    
    foreach ($checks as $pattern => $description) {
        if (strpos($routerContent, $pattern) !== false) {
            echo "   ✅ $description\n";
        } else {
            echo "   ❌ Missing: $description\n";
            $allGood = false;
        }
    }
    
    // Check that it ALWAYS routes sitemap (not conditional)
    if (strpos($routerContent, 'if (!file_exists($file)') !== false && 
        strpos($routerContent, 'sitemap.*\.xml') !== false) {
        // Check if there's a check for file existence before routing
        $lines = explode("\n", $routerContent);
        $inSitemapBlock = false;
        $hasFileCheck = false;
        foreach ($lines as $line) {
            if (preg_match('#sitemap.*\.xml#', $line)) {
                $inSitemapBlock = true;
            }
            if ($inSitemapBlock && strpos($line, 'file_exists') !== false) {
                $hasFileCheck = true;
            }
            if ($inSitemapBlock && strpos($line, 'exit') !== false) {
                break;
            }
        }
        if ($hasFileCheck) {
            echo "   ⚠️  Router checks for file existence (should always route)\n";
        } else {
            echo "   ✅ Router always routes sitemap.xml (no file check)\n";
        }
    }
}

// Test 2: Routes are configured correctly
echo "\n2. Checking route configuration...\n";
$webContent = file_get_contents('routes/web.php');
if (strpos($webContent, 'withoutMiddleware') !== false && 
    strpos($webContent, 'StartSession') !== false) {
    echo "   ✅ Session middleware excluded from sitemap routes\n";
} else {
    echo "   ❌ Session middleware exclusion not found\n";
    $allGood = false;
}

if (strpos($webContent, 'sitemap.xml') !== false) {
    echo "   ✅ Sitemap route is defined\n";
} else {
    echo "   ❌ Sitemap route not found\n";
    $allGood = false;
}

// Test 3: Procfile uses router
echo "\n3. Checking Procfile...\n";
if (file_exists('Procfile')) {
    $procfile = file_get_contents('Procfile');
    if (strpos($procfile, 'php -S') !== false && strpos($procfile, 'router.php') !== false) {
        echo "   ✅ Procfile uses php -S with router.php\n";
    } else {
        echo "   ⚠️  Procfile may not use router.php correctly\n";
        echo "   Current: " . trim($procfile) . "\n";
    }
} else {
    echo "   ⚠️  Procfile not found (may not be needed for local testing)\n";
}

// Test 4: Route registration
echo "\n4. Checking route registration...\n";
exec('php artisan route:list --path=sitemap 2>&1', $output, $return);
if ($return === 0 && !empty($output)) {
    echo "   ✅ Sitemap routes are registered:\n";
    foreach ($output as $line) {
        if (strpos($line, 'sitemap') !== false) {
            echo "      " . trim($line) . "\n";
        }
    }
} else {
    echo "   ⚠️  Could not verify route registration\n";
}

// Summary
echo "\n" . str_repeat("=", 70) . "\n";
if ($allGood) {
    echo "✅ All checks passed!\n";
    echo "\n📋 Next Steps:\n";
    echo "1. Start server: cd public && php -S 127.0.0.1:8000 router.php\n";
    echo "2. Test in browser: http://127.0.0.1:8000/sitemap.xml\n";
    echo "3. Should see XML content (not 404)\n";
    echo "4. Deploy to Railway when confirmed working\n";
} else {
    echo "❌ Some checks failed. Please review the issues above.\n";
}

