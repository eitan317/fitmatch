@echo off
cd /d "%~dp0"
echo Finding Git...
where git >nul 2>&1
if %errorlevel% neq 0 (
    echo Git not found in PATH. Trying common locations...
    if exist "C:\Program Files\Git\bin\git.exe" (
        set "GIT=C:\Program Files\Git\bin\git.exe"
    ) else if exist "C:\Program Files (x86)\Git\bin\git.exe" (
        set "GIT=C:\Program Files (x86)\Git\bin\git.exe"
    ) else (
        echo ERROR: Git not found. Please install Git or add it to PATH.
        pause
        exit /b 1
    )
) else (
    set "GIT=git"
)

echo.
echo Adding migration file...
"%GIT%" add database/migrations/2025_12_17_120000_add_status_to_trainers_table.php
if %errorlevel% neq 0 (
    echo ERROR: Failed to add migration file
    pause
    exit /b 1
)

echo Adding PageController...
"%GIT%" add app/Http/Controllers/PageController.php
if %errorlevel% neq 0 (
    echo ERROR: Failed to add PageController
    pause
    exit /b 1
)

echo Committing changes...
"%GIT%" commit -m "Add status column to trainers table and make PageController resilient"
if %errorlevel% neq 0 (
    echo ERROR: Failed to commit
    pause
    exit /b 1
)

echo Pushing to remote...
"%GIT%" push
if %errorlevel% neq 0 (
    echo ERROR: Failed to push
    pause
    exit /b 1
)

echo.
echo SUCCESS: All changes committed and pushed!
pause

