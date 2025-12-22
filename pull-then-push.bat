@echo off
echo === Pulling latest changes from GitHub ===
echo.

REM First, pull the latest changes
echo Fetching and merging remote changes...
git pull origin main

if errorlevel 1 (
    echo.
    echo Warning: Pull failed. Trying to merge with --no-edit...
    git pull origin main --no-edit
)

echo.
echo === Now pushing your changes ===
echo.

git push origin main

if errorlevel 1 (
    echo.
    echo Push still failed. Your branch might be behind.
    echo You may need to resolve conflicts manually.
) else (
    echo.
    echo ✅ Successfully pushed!
    echo Railway will deploy automatically.
)

pause

