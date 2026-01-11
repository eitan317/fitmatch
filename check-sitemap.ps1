# Check if sitemap.xml works
# Usage: .\check-sitemap.ps1 [URL]
# Example: .\check-sitemap.ps1 https://www.fitmatch.org.il

param(
    [string]$Url = "https://www.fitmatch.org.il"
)

Write-Host "🔍 Checking Sitemap: $Url/sitemap.xml" -ForegroundColor Cyan
Write-Host ("=" * 60) -ForegroundColor Cyan
Write-Host ""

$sitemapUrl = "$Url/sitemap.xml"

try {
    # Test 1: HTTP Status
    Write-Host "1. Testing HTTP Status..." -ForegroundColor Yellow
    $response = Invoke-WebRequest -Uri $sitemapUrl -Method Head -UseBasicParsing -ErrorAction Stop
    
    Write-Host "   ✅ Status Code: $($response.StatusCode)" -ForegroundColor Green
    
    $contentType = $response.Headers['Content-Type']
    if ($contentType -like "*xml*") {
        Write-Host "   ✅ Content-Type: $contentType" -ForegroundColor Green
    } else {
        Write-Host "   ⚠️  Content-Type: $contentType (expected XML)" -ForegroundColor Yellow
    }
    
    Write-Host ""
    
    # Test 2: Content
    Write-Host "2. Testing Content..." -ForegroundColor Yellow
    $contentResponse = Invoke-WebRequest -Uri $sitemapUrl -UseBasicParsing -ErrorAction Stop
    $content = $contentResponse.Content
    
    # Check XML declaration
    if ($content -match '^\s*<\?xml') {
        Write-Host "   ✅ Valid XML declaration found" -ForegroundColor Green
    } else {
        Write-Host "   ❌ Missing XML declaration" -ForegroundColor Red
    }
    
    # Check urlset
    if ($content -match '<urlset') {
        Write-Host "   ✅ Sitemap structure found (<urlset>)" -ForegroundColor Green
    } else {
        Write-Host "   ❌ Missing <urlset> tag" -ForegroundColor Red
    }
    
    # Count URLs
    $urlCount = ([regex]::Matches($content, '<url>')).Count
    Write-Host "   ✅ Found $urlCount URLs" -ForegroundColor Green
    
    # Check for www
    if ($content -match 'www\.fitmatch\.org\.il') {
        Write-Host "   ✅ URLs use www subdomain" -ForegroundColor Green
    } elseif ($content -match 'fitmatch\.org\.il') {
        Write-Host "   ⚠️  URLs use apex domain (not www)" -ForegroundColor Yellow
    } else {
        Write-Host "   ⚠️  Domain not found in URLs" -ForegroundColor Yellow
    }
    
    Write-Host ""
    
    # Test 3: First 500 characters
    Write-Host "3. Content Preview (first 500 chars):" -ForegroundColor Yellow
    Write-Host ($content.Substring(0, [Math]::Min(500, $content.Length))) -ForegroundColor Gray
    Write-Host ""
    
    # Test 4: Summary
    Write-Host "4. Summary:" -ForegroundColor Yellow
    Write-Host "   Status: $($response.StatusCode)" -ForegroundColor $(if ($response.StatusCode -eq 200) { "Green" } else { "Red" })
    Write-Host "   Content-Type: $contentType" -ForegroundColor $(if ($contentType -like "*xml*") { "Green" } else { "Yellow" })
    Write-Host "   URLs Count: $urlCount" -ForegroundColor Green
    Write-Host "   Content Length: $($content.Length) bytes" -ForegroundColor Green
    
    Write-Host ""
    Write-Host "✅ Sitemap appears to be working!" -ForegroundColor Green
    
} catch {
    Write-Host ""
    Write-Host "❌ Error: $_" -ForegroundColor Red
    
    if ($_.Exception.Response) {
        $statusCode = $_.Exception.Response.StatusCode.value__
        Write-Host "   Status Code: $statusCode" -ForegroundColor Red
        
        if ($statusCode -eq 404) {
            Write-Host ""
            Write-Host "💡 404 means:" -ForegroundColor Yellow
            Write-Host "   - Route not registered" -ForegroundColor Yellow
            Write-Host "   - Router not working (check router.php)" -ForegroundColor Yellow
            Write-Host "   - Deployment not completed" -ForegroundColor Yellow
        } elseif ($statusCode -eq 500) {
            Write-Host ""
            Write-Host "💡 500 means:" -ForegroundColor Yellow
            Write-Host "   - PHP error (check Railway logs)" -ForegroundColor Yellow
            Write-Host "   - Database connection issue" -ForegroundColor Yellow
        }
    }
    
    Write-Host ""
    Write-Host "❌ Sitemap is NOT working" -ForegroundColor Red
    exit 1
}
