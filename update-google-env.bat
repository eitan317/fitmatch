@echo off
REM Batch script to update .env file with Google OAuth credentials
REM Run this from C:\laragon\www\fitmatch directory

cd /d C:\laragon\www\fitmatch

echo Updating .env file with Google OAuth credentials...

REM Check if .env exists
if not exist .env (
    echo ERROR: .env file not found!
    echo Please make sure you're in the correct directory.
    pause
    exit /b 1
)

REM Add Google credentials (will append even if they exist - you can manually remove duplicates)
echo. >> .env
echo # Google OAuth Configuration >> .env
echo GOOGLE_CLIENT_ID=1022366565072-8a9nrblkv480k4hl4f3140e1dqjsjec1.apps.googleusercontent.com >> .env
echo GOOGLE_CLIENT_SECRET=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 >> .env
echo GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback >> .env

echo.
echo Successfully added Google OAuth credentials to .env
echo.
echo Next steps:
echo 1. Run: php artisan config:clear
echo 2. Run: php artisan cache:clear
echo 3. Restart your Laravel server
echo.
pause

