@echo off
echo Updating .env file...
php fix-env-domain.php
if %ERRORLEVEL% EQU 0 (
    echo.
    echo Running cache clear commands...
    php artisan config:clear
    php artisan cache:clear
    php artisan route:clear
    php artisan view:clear
    echo.
    echo Done! Please verify with: php artisan tinker
    echo Then run: config('app.url') and config('services.google.redirect')
) else (
    echo Error updating .env file
    pause
)

