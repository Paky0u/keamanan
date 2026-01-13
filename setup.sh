#!/bin/bash

echo "Setting up Simple LMS..."
echo

echo "Step 1: Installing Composer dependencies..."
composer install --ignore-platform-reqs
if [ $? -ne 0 ]; then
    echo "Error: Composer install failed. Please check your PHP version and try again."
    exit 1
fi

echo
echo "Step 2: Installing NPM dependencies..."
npm install
if [ $? -ne 0 ]; then
    echo "Error: NPM install failed. Please ensure Node.js is installed."
    exit 1
fi

echo
echo "Step 3: Setting up environment file..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo "Environment file created."
else
    echo "Environment file already exists."
fi

echo
echo "Step 4: Generating application key..."
php artisan key:generate
if [ $? -ne 0 ]; then
    echo "Error: Could not generate application key."
    exit 1
fi

echo
echo "Step 5: Creating SQLite database..."
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
    echo "SQLite database file created."
else
    echo "SQLite database file already exists."
fi

echo
echo "Step 6: Running database migrations..."
php artisan migrate --force
if [ $? -ne 0 ]; then
    echo "Error: Database migration failed."
    exit 1
fi

echo
echo "Step 7: Creating storage link..."
php artisan storage:link
if [ $? -ne 0 ]; then
    echo "Warning: Could not create storage link. You may need to do this manually."
fi

echo
echo "Step 8: Building frontend assets..."
npm run build
if [ $? -ne 0 ]; then
    echo "Error: Asset build failed."
    exit 1
fi

echo
echo "========================================"
echo "Setup completed successfully!"
echo "========================================"
echo
echo "To start the development server, run:"
echo "php artisan serve"
echo
echo "Then visit: http://localhost:8000"
echo