@echo off
cd /d "%~dp0"

echo ========================================
echo FINAL STEP: Push to GitHub
echo ========================================
echo.
echo Your changes are committed locally.
echo.
echo To push to GitHub, you need to authenticate.
echo.
echo OPTION 1: Use GitHub Personal Access Token
echo   1. Go to: https://github.com/settings/tokens
echo   2. Click "Generate new token (classic)"
echo   3. Name it "FitMatch Push"
echo   4. Select scope: "repo"
echo   5. Click "Generate token"
echo   6. Copy the token
echo   7. When prompted for password, paste the token
echo.
echo OPTION 2: Run this command with your token:
echo   git push https://YOUR_TOKEN@github.com/eitan317/fitmatch.git main
echo.
echo Press any key to attempt push (will prompt for credentials)...
pause

git push -u origin main

if %errorlevel% equ 0 (
    echo.
    echo ========================================
    echo SUCCESS! Pushed to GitHub!
    echo ========================================
    echo.
    echo Next steps:
    echo   1. Railway should auto-deploy
    echo   2. Run migration on Railway:
    echo      railway run php artisan migrate --path=database/migrations/2025_12_17_120000_add_status_to_trainers_table.php --force
) else (
    echo.
    echo Push requires authentication.
    echo See instructions above for Personal Access Token.
)

pause

