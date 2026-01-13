@echo off
echo Setting up Simple LMS...
echo.

echo Step 1: Installing Composer dependencies...
composer install --ignore-platform-reqs
if %errorlevel% neq 0 (
    echo Error: Composer install failed. Please check your PHP version and try again.
    pause
    exit /b 1
)

echo.
echo Step 2: Installing NPM dependencies...
npm install
if %errorlevel% neq 0 (
    echo Error: NPM install failed. Please ensure Node.js is installed.
    pause
    exit /b 1
)

echo.
echo Step 3: Setting up environment file...
if not exist .env (
    copy .env.example .env
    echo Environment file created.
) else (
    echo Environment file already exists.
)

echo.
echo Step 4: Generating application key...
php artisan key:generate
if %errorlevel% neq 0 (
    echo Error: Could not generate application key.
    pause
    exit /b 1
)

echo.
echo Step 5: Creating SQLite database...
if not exist database\database.sqlite (
    type nul > database\database.sqlite
    echo SQLite database file created.
) else (
    echo SQLite database file already exists.
)

echo.
echo Step 6: Running database migrations...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo Error: Database migration failed.
    pause
    exit /b 1
)

echo.
echo Step 7: Creating storage link...
php artisan storage:link
if %errorlevel% neq 0 (
    echo Warning: Could not create storage link. You may need to do this manually.
)

echo.
echo Step 8: Building frontend assets...
npm run build
if %errorlevel% neq 0 (
    echo Error: Asset build failed.
    pause
    exit /b 1
)

echo.
echo ========================================
echo Setup completed successfully!
echo ========================================
echo.
echo To start the development server, run:
echo php artisan serve
echo.
echo Then visit: http://localhost:8000
echo.
pause