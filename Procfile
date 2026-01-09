web: php artisan storage:link || true; php artisan migrate --force || true; php artisan config:clear; php artisan route:clear; php artisan cache:clear; php generate-sitemap-static.php || true; sh -c "php -S 0.0.0.0:\$PORT -t public public/router.php"

