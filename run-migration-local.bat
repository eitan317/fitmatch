@echo off
echo === Running Database Migrations ===
echo.

php artisan migrate

if errorlevel 1 (
    echo.
    echo Migration failed. Trying with --force...
    php artisan migrate --force
)

echo.
echo === Done ===
pause

