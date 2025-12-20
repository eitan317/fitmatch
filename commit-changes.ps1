Set-Location "C:\Users\USER\OneDrive\Desktop\fitmatch"
Write-Host "Adding migration file..." -ForegroundColor Green
& git add database/migrations/2025_12_17_120000_add_status_to_trainers_table.php
Write-Host "Adding PageController..." -ForegroundColor Green
& git add app/Http/Controllers/PageController.php
Write-Host "Committing changes..." -ForegroundColor Green
& git commit -m "Add status column to trainers table and make PageController resilient"
Write-Host "Pushing to remote..." -ForegroundColor Green
& git push
Write-Host "Done!" -ForegroundColor Green

