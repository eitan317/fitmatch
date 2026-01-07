#!/bin/bash
# Railway-specific sitemap check script
# This can be run in Railway console to diagnose issues

echo "Checking sitemap setup on Railway..."
echo ""

# Check if static file exists
if [ -f "public/sitemap.xml" ]; then
    echo "❌ WARNING: Static sitemap.xml file exists!"
    echo "   This will block the route. Removing it..."
    rm -f public/sitemap.xml
    echo "   ✅ Removed"
else
    echo "✅ No static sitemap.xml file"
fi

# Check route registration
echo ""
echo "Checking route registration..."
php artisan route:list | grep sitemap

# Test controller
echo ""
echo "Testing controller..."
php artisan tinker --execute="try { \$c = app(\App\Http\Controllers\SitemapController::class); \$r = \$c->main(); echo 'Status: ' . \$r->getStatusCode(); } catch (\Exception \$e) { echo 'Error: ' . \$e->getMessage(); }"

echo ""
echo "Done!"

