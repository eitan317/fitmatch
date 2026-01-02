web: (php artisan storage:link || true) && php artisan migrate --force && (php -r "if(file_exists('public/sitemap.xml')) unlink('public/sitemap.xml');" || true) && php artisan serve --host 0.0.0.0 --port $PORT

