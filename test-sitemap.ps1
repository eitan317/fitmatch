# Test sitemap.xml endpoint
Write-Host "Testing https://fitmatch.org.il/sitemap.xml..." -ForegroundColor Cyan
Write-Host ""

try {
    $response = Invoke-WebRequest -Uri "https://fitmatch.org.il/sitemap.xml" -UseBasicParsing -ErrorAction Stop
    
    Write-Host "✅ Status Code: $($response.StatusCode)" -ForegroundColor Green
    Write-Host "✅ Content-Type: $($response.Headers['Content-Type'])" -ForegroundColor Green
    Write-Host "✅ Content Length: $($response.Content.Length) bytes" -ForegroundColor Green
    Write-Host ""
    
    # Check if it's valid XML
    if ($response.Content -match '^\s*<\?xml') {
        Write-Host "✅ Valid XML detected" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Response doesn't start with XML declaration" -ForegroundColor Yellow
    }
    
    # Check for sitemap structure
    if ($response.Content -match '<urlset') {
        Write-Host "✅ Sitemap structure detected (<urlset>)" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Sitemap structure not found" -ForegroundColor Yellow
    }
    
    # Show first 500 characters
    Write-Host ""
    Write-Host "First 500 characters of response:" -ForegroundColor Cyan
    Write-Host $response.Content.Substring(0, [Math]::Min(500, $response.Content.Length))
    
} catch {
    Write-Host "❌ Error: $_" -ForegroundColor Red
    Write-Host "Status Code: $($_.Exception.Response.StatusCode.value__)" -ForegroundColor Yellow
    Write-Host "Response: $($_.Exception.Response)" -ForegroundColor Yellow
}
