@echo off
cd /d "%~dp0"

echo ========================================
echo Git Repository Setup and Commit Script
echo ========================================
echo.

REM Check if .git exists
if exist ".git" (
    echo Git repository already exists.
    goto :commit
)

echo This directory is not a Git repository.
echo.
echo Options:
echo 1. Initialize a new Git repository and connect to remote
echo 2. Skip initialization (if you want to do it manually)
echo.
set /p choice="Enter choice (1 or 2): "

if "%choice%"=="1" (
    echo.
    echo Initializing Git repository...
    git init
    if %errorlevel% neq 0 (
        echo ERROR: Failed to initialize Git repository
        pause
        exit /b 1
    )
    
    echo.
    set /p remote_url="Enter your Git remote URL (e.g., https://github.com/username/repo.git): "
    if not "%remote_url%"=="" (
        echo Adding remote origin...
        git remote add origin "%remote_url%"
        
        echo.
        echo Fetching from remote...
        git fetch origin
        
        echo.
        set /p branch="Enter branch name to use (default: main): "
        if "%branch%"=="" set "branch=main"
        
        echo Checking out or creating branch: %branch%
        git checkout -b %branch% 2>nul
        git branch --set-upstream-to=origin/%branch% %branch% 2>nul
    )
    
    echo.
    echo Creating .gitignore if it doesn't exist...
    if not exist ".gitignore" (
        echo /vendor/ > .gitignore
        echo /node_modules/ >> .gitignore
        echo /.env >> .gitignore
        echo /storage/*.key >> .gitignore
        echo /.phpunit.result.cache >> .gitignore
        echo /bootstrap/cache/* >> .gitignore
        echo !/bootstrap/cache/.gitignore >> .gitignore
    )
)

:commit
echo.
echo ========================================
echo Staging and committing files...
echo ========================================
echo.

echo Adding migration file...
git add database/migrations/2025_12_17_120000_add_status_to_trainers_table.php
if %errorlevel% neq 0 (
    echo WARNING: Failed to add migration file (might not exist or already committed)
)

echo Adding PageController...
git add app/Http/Controllers/PageController.php
if %errorlevel% neq 0 (
    echo WARNING: Failed to add PageController (might not exist or already committed)
)

echo.
echo Checking status...
git status

echo.
set /p do_commit="Do you want to commit these changes? (y/n): "
if /i not "%do_commit%"=="y" (
    echo Commit cancelled.
    pause
    exit /b 0
)

echo.
echo Committing changes...
git commit -m "Add status column to trainers table and make PageController resilient"
if %errorlevel% neq 0 (
    echo ERROR: Failed to commit. There might be no changes to commit.
    pause
    exit /b 1
)

echo.
set /p do_push="Do you want to push to remote? (y/n): "
if /i not "%do_push%"=="y" (
    echo Push cancelled. You can push later with: git push
    pause
    exit /b 0
)

echo.
echo Pushing to remote...
git push -u origin HEAD
if %errorlevel% neq 0 (
    echo.
    echo WARNING: Push failed. You may need to:
    echo   1. Set up remote: git remote add origin YOUR_REPO_URL
    echo   2. Or pull first: git pull origin main --allow-unrelated-histories
    echo   3. Then push: git push -u origin main
)

echo.
echo ========================================
echo Done!
echo ========================================
pause

