# Simple LMS - Setup Guide

## Quick Start

### Option 1: Automated Setup (Recommended)

**Windows:**
```bash
setup.bat
```

**Linux/Mac:**
```bash
chmod +x setup.sh
./setup.sh
```

### Option 2: Manual Setup

1. **Install dependencies:**
   ```bash
   composer install --ignore-platform-reqs
   npm install
   ```

2. **Environment setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database setup:**
   ```bash
   # Create SQLite database
   touch database/database.sqlite
   
   # Run migrations
   php artisan migrate
   ```

4. **Storage and assets:**
   ```bash
   php artisan storage:link
   npm run build
   ```

5. **Start server:**
   ```bash
   php artisan serve
   ```

## Demo Data (Optional)

To populate the system with demo data for testing:

```bash
php artisan db:seed --class=DemoSeeder
```

This creates:
- **Teacher account:** teacher@example.com / password
- **Student accounts:** alice@example.com / password, bob@example.com / password
- **Sample classes:** MATH01, SCI001
- **Sample announcements and assignments**

## System Overview

### Core Features Implemented

✅ **User Authentication**
- Registration and login
- Profile management
- Password updates

✅ **Class Management**
- Create classes with auto-generated codes
- Join classes using 6-digit codes
- Edit and delete classes

✅ **Class Stream**
- Post announcements
- View chronological posts
- Delete own announcements

✅ **Materials System**
- Upload PDFs, images, videos
- Download materials
- File type categorization
- File size display

✅ **Assignment System**
- Create assignments with deadlines
- Submit text and file responses
- Basic grading system
- Late submission tracking

✅ **Dashboard**
- Overview of all classes
- Recent announcements
- Upcoming assignments
- Quick actions

### File Structure

```
app/
├── Http/Controllers/
│   ├── Auth/                    # Authentication controllers
│   ├── DashboardController.php  # Main dashboard
│   ├── ClassController.php      # Class CRUD operations
│   ├── AnnouncementController.php
│   ├── MaterialController.php   # File upload/download
│   ├── AssignmentController.php
│   └── SubmissionController.php
├── Models/
│   ├── User.php
│   ├── ClassModel.php          # Classes table
│   ├── Announcement.php
│   ├── Material.php
│   ├── Assignment.php
│   └── Submission.php
database/migrations/
├── create_classes_table.php
├── create_class_user_table.php  # Many-to-many pivot
├── create_announcements_table.php
├── create_materials_table.php
├── create_assignments_table.php
└── create_submissions_table.php
resources/views/
├── layouts/
│   ├── app.blade.php           # Main layout
│   └── navigation.blade.php    # Top navigation
├── components/                 # Reusable components
├── auth/                       # Login/register forms
├── classes/                    # Class management views
├── materials/                  # Material upload/view
├── assignments/                # Assignment management
├── dashboard.blade.php         # Main dashboard
└── welcome.blade.php          # Landing page
```

### Database Schema

**Users Table:**
- id, name, email, password, timestamps

**Classes Table:**
- id, name, subject, description, class_code, created_by, timestamps

**Class_User Pivot Table:**
- id, class_id, user_id, joined_at, timestamps

**Announcements Table:**
- id, class_id, user_id, title, content, timestamps

**Materials Table:**
- id, class_id, user_id, title, description, file_name, file_path, file_type, file_size, timestamps

**Assignments Table:**
- id, class_id, user_id, title, description, due_date, max_points, timestamps

**Submissions Table:**
- id, assignment_id, user_id, content, file_name, file_path, grade, feedback, submitted_at, timestamps

### Key Routes

```php
// Authentication
GET|POST /login
GET|POST /register
POST /logout

// Dashboard
GET /dashboard

// Classes
GET /classes                    # List all classes
GET /classes/create            # Create class form
POST /classes                  # Store new class
GET /classes/{class}           # Show class details
GET /classes/{class}/edit      # Edit class form
PUT /classes/{class}           # Update class
DELETE /classes/{class}        # Delete class
POST /classes/join             # Join class with code

// Announcements
POST /classes/{class}/announcements
PUT /classes/{class}/announcements/{announcement}
DELETE /classes/{class}/announcements/{announcement}

// Materials
GET /classes/{class}/materials
GET /classes/{class}/materials/create
POST /classes/{class}/materials
GET /classes/{class}/materials/{material}
GET /classes/{class}/materials/{material}/download
DELETE /classes/{class}/materials/{material}

// Assignments & Submissions
GET /classes/{class}/assignments
GET /classes/{class}/assignments/create
POST /classes/{class}/assignments
GET /classes/{class}/assignments/{assignment}
POST /classes/{class}/assignments/{assignment}/submissions
```

## Security Features

- **CSRF Protection:** All forms include CSRF tokens
- **File Upload Validation:** Type, size, and extension checks
- **Access Control:** Users can only edit their own content
- **SQL Injection Protection:** Eloquent ORM prevents SQL injection
- **Password Hashing:** Bcrypt hashing for all passwords
- **Authentication Middleware:** Protected routes require login

## Customization Options

### Adding File Types
Edit `MaterialController::getFileType()` method:

```php
private function getFileType(string $extension): string
{
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $videoExtensions = ['mp4', 'avi', 'mov', 'mkv'];
    $documentExtensions = ['pdf', 'doc', 'docx', 'txt'];
    
    // Add your logic here
}
```

### Changing Upload Limits
1. Update validation rules in controllers
2. Modify PHP settings in `php.ini`:
   ```ini
   upload_max_filesize = 100M
   post_max_size = 100M
   max_execution_time = 300
   ```

### Styling Customization
The system uses Tailwind CSS. Modify classes in Blade templates or add custom CSS in `resources/css/app.css`.

### Database Configuration
For MySQL instead of SQLite, update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## Troubleshooting

### Common Issues

**1. PHP Version Error**
```
Composer detected issues in your platform: Your Composer dependencies require a PHP version ">= 8.4.0"
```
**Solution:** Use `composer install --ignore-platform-reqs`

**2. File Upload Not Working**
- Check storage permissions: `chmod -R 755 storage`
- Verify storage link: `php artisan storage:link`
- Check PHP upload limits in `php.ini`

**3. Assets Not Loading**
- Rebuild assets: `npm run build`
- Clear cache: `php artisan cache:clear`

**4. Database Connection Error**
- Ensure database file exists (SQLite): `touch database/database.sqlite`
- Check database permissions
- Verify `.env` configuration

### Development Commands

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Reset database
php artisan migrate:fresh
php artisan migrate:fresh --seed

# Watch for file changes
npm run dev

# Build for production
npm run build
```

## Next Steps

After setup, you can:

1. **Register a new account** or use demo credentials
2. **Create your first class** and note the class code
3. **Join classes** using class codes
4. **Upload materials** (PDFs, images, videos)
5. **Create assignments** with due dates
6. **Submit assignments** as a student
7. **Grade submissions** and provide feedback

The system is designed to be simple and intuitive, similar to Google Classroom but without complex role management. All users have the same capabilities - they can create classes, join classes, and participate equally.

## Support

For issues or questions:
1. Check this guide first
2. Review the Laravel documentation
3. Check file permissions and PHP configuration
4. Verify database connectivity

The system is built with standard Laravel practices, so most Laravel troubleshooting guides will apply.