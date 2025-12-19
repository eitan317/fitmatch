# Script to add Google OAuth credentials to .env file
# Make sure you run this from C:\laragon\www\fitmatch directory

$envFile = "C:\laragon\www\fitmatch\.env"
$clientId = "GOOGLE_CLIENT_ID=1022366565072-8a9nrblkv480k4hl4f3140e1dqjsjec1.apps.googleusercontent.com"
$clientSecret = "GOOGLE_CLIENT_SECRET=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"
$redirectUri = "GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback"

Write-Host "Updating .env file at: $envFile" -ForegroundColor Yellow

if (-not (Test-Path $envFile)) {
    Write-Host "ERROR: .env file not found at $envFile" -ForegroundColor Red
    Write-Host "Please make sure you're running this from the correct directory." -ForegroundColor Red
    exit 1
}

# Read current content
$content = Get-Content $envFile -ErrorAction SilentlyContinue

# Remove existing Google entries if they exist
$newContent = $content | Where-Object { 
    $_ -notmatch "^GOOGLE_CLIENT_ID=" -and 
    $_ -notmatch "^GOOGLE_CLIENT_SECRET=" -and 
    $_ -notmatch "^GOOGLE_REDIRECT_URI=" 
}

# Add new entries
$newContent += ""
$newContent += "# Google OAuth Configuration"
$newContent += $clientId
$newContent += $clientSecret
$newContent += $redirectUri

# Write back to file
$newContent | Set-Content $envFile -Encoding UTF8

Write-Host ""
Write-Host "✅ Successfully updated .env file!" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Run: cd C:\laragon\www\fitmatch" -ForegroundColor Cyan
Write-Host "2. Run: php artisan config:clear" -ForegroundColor Cyan
Write-Host "3. Run: php artisan cache:clear" -ForegroundColor Cyan
Write-Host "4. Restart your Laravel server (php artisan serve)" -ForegroundColor Cyan

