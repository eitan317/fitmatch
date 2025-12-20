@echo off
setlocal enabledelayedexpansion
cd /d "%~dp0"

echo ========================================
echo Automated Git Setup and Commit
echo ========================================
echo.

REM Find Git
set "GIT_EXE="
where git >nul 2>&1
if %errorlevel% equ 0 (
    set "GIT_EXE=git"
) else if exist "C:\Program Files\Git\bin\git.exe" (
    set "GIT_EXE=C:\Program Files\Git\bin\git.exe"
) else if exist "C:\Program Files (x86)\Git\bin\git.exe" (
    set "GIT_EXE=C:\Program Files (x86)\Git\bin\git.exe"
) else (
    echo ERROR: Git not found. Please install Git.
    pause
    exit /b 1
)

echo Using Git: %GIT_EXE%
echo.

REM Initialize Git if needed
if not exist ".git" (
    echo Initializing Git repository...
    "%GIT_EXE%" init
    if %errorlevel% neq 0 (
        echo ERROR: Failed to initialize Git
        pause
        exit /b 1
    )
    
    REM Create basic .gitignore
    if not exist ".gitignore" (
        echo Creating .gitignore...
        (
            echo /vendor/
            echo /node_modules/
            echo /.env
            echo /storage/*.key
            echo /.phpunit.result.cache
            echo /bootstrap/cache/*
            echo !/bootstrap/cache/.gitignore
        ) > .gitignore
    )
    
    echo.
    echo NOTE: Git repository initialized.
    echo You need to add a remote repository URL.
    echo Example: git remote add origin https://github.com/username/repo.git
    echo.
)

REM Stage files
echo Staging files...
"%GIT_EXE%" add database/migrations/2025_12_17_120000_add_status_to_trainers_table.php 2>nul
"%GIT_EXE%" add app/Http/Controllers/PageController.php 2>nul

REM Check if there are changes to commit
"%GIT_EXE%" diff --cached --quiet
if %errorlevel% equ 0 (
    echo No changes to commit. Files may already be committed.
) else (
    echo Committing changes...
    "%GIT_EXE%" commit -m "Add status column to trainers table and make PageController resilient" --no-verify
    if %errorlevel% equ 0 (
        echo Commit successful!
    ) else (
        echo WARNING: Commit may have failed or there were no changes.
    )
)

REM Try to push (will fail silently if no remote)
echo.
echo Attempting to push...
"%GIT_EXE%" push -u origin HEAD 2>nul
if %errorlevel% equ 0 (
    echo Push successful!
) else (
    echo.
    echo NOTE: Push failed or no remote configured.
    echo To set up remote, run:
    echo   git remote add origin YOUR_REPO_URL
    echo   git push -u origin main
)

echo.
echo ========================================
echo Script completed!
echo ========================================
timeout /t 3 >nul

