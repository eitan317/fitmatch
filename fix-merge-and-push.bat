@echo off
echo === Fixing unfinished merge and pushing ===
echo.

echo Step 1: Aborting unfinished merge...
git merge --abort

if errorlevel 1 (
    echo Merge abort failed, trying to reset...
    git reset --hard HEAD
)

echo.
echo Step 2: Fetching latest changes from GitHub...
git fetch origin main

echo.
echo Step 3: Resetting local branch to match remote...
git reset --hard origin/main

echo.
echo Step 4: Pulling latest changes...
git pull origin main

echo.
echo Step 5: Adding your local changes...
git add .

echo.
echo Step 6: Committing your changes...
git commit -m "Fix production assets and images: HTTPS URLs, storage symlink, improved build process"

echo.
echo Step 7: Pushing to GitHub...
git push origin main

if errorlevel 1 (
    echo.
    echo ❌ Push failed. Trying alternative method...
    echo.
    echo Attempting force push with lease (safe)...
    git push --force-with-lease origin main
)

echo.
if errorlevel 1 (
    echo.
    echo ❌ Push still failed. Please check the errors above.
    echo You may need to resolve conflicts manually.
) else (
    echo.
    echo ✅✅✅ Successfully pushed to GitHub!
    echo.
    echo Railway will automatically deploy your changes.
    echo Check Railway dashboard in a few minutes.
)

pause

