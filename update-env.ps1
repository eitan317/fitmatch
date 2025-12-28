# PowerShell script to update .env file with new domain
$envFile = "C:\laragon\www\fitmatch\.env"

Write-Host "Updating .env file..." -ForegroundColor Yellow

if (-not (Test-Path $envFile)) {
    Write-Host "ERROR: .env file not found at $envFile" -ForegroundColor Red
    exit 1
}

# Read current content
$content = Get-Content $envFile -Raw

# Define new values
$newAppUrl = "APP_URL=https://www.fitmatch.org.il"
$newGoogleRedirect = "GOOGLE_REDIRECT_URI=https://www.fitmatch.org.il/auth/google/callback"
$newSessionDomain = "SESSION_DOMAIN=null"

# Replace or add APP_URL
if ($content -match "APP_URL=.*") {
    $content = $content -replace "APP_URL=.*", $newAppUrl
    Write-Host "Updated APP_URL" -ForegroundColor Green
} else {
    $content += "`n$newAppUrl"
    Write-Host "Added APP_URL" -ForegroundColor Green
}

# Replace or add GOOGLE_REDIRECT_URI
if ($content -match "GOOGLE_REDIRECT_URI=.*") {
    $content = $content -replace "GOOGLE_REDIRECT_URI=.*", $newGoogleRedirect
    Write-Host "Updated GOOGLE_REDIRECT_URI" -ForegroundColor Green
} else {
    $content += "`n$newGoogleRedirect"
    Write-Host "Added GOOGLE_REDIRECT_URI" -ForegroundColor Green
}

# Replace or add SESSION_DOMAIN
if ($content -match "SESSION_DOMAIN=.*") {
    $content = $content -replace "SESSION_DOMAIN=.*", $newSessionDomain
    Write-Host "Updated SESSION_DOMAIN" -ForegroundColor Green
} else {
    $content += "`n$newSessionDomain"
    Write-Host "Added SESSION_DOMAIN" -ForegroundColor Green
}

# Write back to file
$content | Set-Content $envFile -Encoding UTF8 -NoNewline

Write-Host "`n✅ Successfully updated .env file!" -ForegroundColor Green
Write-Host "`nNext steps:" -ForegroundColor Yellow
Write-Host "1. Run: php artisan config:clear" -ForegroundColor Cyan
Write-Host "2. Run: php artisan cache:clear" -ForegroundColor Cyan
Write-Host "3. Run: php artisan route:clear" -ForegroundColor Cyan
Write-Host "4. Run: php artisan view:clear" -ForegroundColor Cyan
