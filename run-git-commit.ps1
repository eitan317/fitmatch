$ErrorActionPreference = "Stop"
Set-Location "C:\Users\USER\OneDrive\Desktop\fitmatch"

# Try to find git
$gitPaths = @(
    "C:\Program Files\Git\bin\git.exe",
    "C:\Program Files (x86)\Git\bin\git.exe",
    "$env:LOCALAPPDATA\Programs\Git\bin\git.exe",
    "git"  # If in PATH
)

$gitExe = $null
foreach ($path in $gitPaths) {
    if ($path -eq "git") {
        try {
            $null = Get-Command git -ErrorAction Stop
            $gitExe = "git"
            break
        } catch {
            continue
        }
    } elseif (Test-Path $path) {
        $gitExe = $path
        break
    }
}

if (-not $gitExe) {
    Write-Host "Git not found. Please install Git or add it to PATH." -ForegroundColor Red
    exit 1
}

Write-Host "Using Git: $gitExe" -ForegroundColor Cyan
Write-Host "Current directory: $(Get-Location)" -ForegroundColor Cyan

Write-Host "`nAdding migration file..." -ForegroundColor Green
& $gitExe add database/migrations/2025_12_17_120000_add_status_to_trainers_table.php
if ($LASTEXITCODE -ne 0) { throw "Failed to add migration file" }

Write-Host "Adding PageController..." -ForegroundColor Green
& $gitExe add app/Http/Controllers/PageController.php
if ($LASTEXITCODE -ne 0) { throw "Failed to add PageController" }

Write-Host "Committing changes..." -ForegroundColor Green
& $gitExe commit -m "Add status column to trainers table and make PageController resilient"
if ($LASTEXITCODE -ne 0) { throw "Failed to commit" }

Write-Host "Pushing to remote..." -ForegroundColor Green
& $gitExe push
if ($LASTEXITCODE -ne 0) { throw "Failed to push" }

Write-Host "`n✅ Successfully committed and pushed!" -ForegroundColor Green

