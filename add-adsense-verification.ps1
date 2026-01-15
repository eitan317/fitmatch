$line = "GOOGLE_ADSENSE_VERIFICATION_CODE=ca-pub-9114804505473664"
$envFile = ".env"

# Check if line already exists
$content = Get-Content $envFile -ErrorAction SilentlyContinue
if ($content -match "GOOGLE_ADSENSE_VERIFICATION_CODE") {
    Write-Host "Line already exists, updating..."
    $content = $content | ForEach-Object {
        if ($_ -match "GOOGLE_ADSENSE_VERIFICATION_CODE") {
            $line
        } else {
            $_
        }
    }
    $content | Set-Content $envFile -Encoding UTF8
} else {
    Write-Host "Adding new line..."
    Add-Content -Path $envFile -Value $line -Encoding UTF8
}

Write-Host "Done!"
