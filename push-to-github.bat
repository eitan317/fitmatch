@echo off
cd /d "%~dp0"

echo ========================================
echo Pushing to GitHub
echo ========================================
echo.
echo This will push your committed changes to: https://github.com/eitan317/fitmatch
echo.
echo You will be prompted for your GitHub credentials.
echo For GitHub, you can use:
echo   - Username: your GitHub username
echo   - Password: a Personal Access Token (not your password)
echo.
echo To create a Personal Access Token:
echo   1. Go to https://github.com/settings/tokens
echo   2. Generate new token (classic)
echo   3. Select 'repo' scope
echo   4. Copy the token and use it as password
echo.
pause

git push -u origin main

if %errorlevel% equ 0 (
    echo.
    echo ========================================
    echo SUCCESS! Changes pushed to GitHub!
    echo ========================================
    echo.
    echo Railway should automatically deploy your changes.
    echo After deployment, run the migration on Railway:
    echo   railway run php artisan migrate --path=database/migrations/2025_12_17_120000_add_status_to_trainers_table.php --force
) else (
    echo.
    echo ========================================
    echo Push failed. Possible reasons:
    echo   1. Authentication failed
    echo   2. No write access to repository
    echo   3. Network issues
    echo ========================================
)

pause

