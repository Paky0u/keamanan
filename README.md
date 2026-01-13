# Simple Learning Management System (LMS)

A simple Learning Management System built with Laravel, inspired by Google Classroom. This system allows users to create and join classes, share materials, create assignments, and manage submissions without complex role-based permissions.

## Features

### 🏫 Class Management
- Create classes with auto-generated class codes
- Join classes using 6-digit class codes
- Simple class information (name, subject, description)

### 📰 Class Stream
- Post announcements to classes
- Chronological display of posts
- Simple commenting system

### 📂 Learning Materials
- Upload PDFs, images, and videos
- Download and view materials
- File type categorization

### 📝 Assignment System
- Create assignments with deadlines
- Submit assignments (file upload + text)
- View submitted files
- Simple numeric grading system

### 📊 Dashboard
- View created and joined classes
- Latest announcements
- Upcoming assignments
- Quick access to all features

## Tech Stack

- **Framework**: Laravel 11
- **Database**: SQLite (default) / MySQL
- **Frontend**: Blade templates + Tailwind CSS
- **Authentication**: Laravel default auth
- **File Storage**: Laravel Storage (local/public disk)

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd lms-project
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   # For SQLite (default)
   touch database/database.sqlite
   
   # Or configure MySQL in .env file
   # DB_CONNECTION=mysql
   # DB_HOST=127.0.0.1
   # DB_PORT=3306
   # DB_DATABASE=lms_db
   # DB_USERNAME=your_username
   # DB_PASSWORD=your_password
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Create storage link**
   ```bash
   php artisan storage:link
   ```

8. **Build assets**
   ```bash
   npm run build
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` to access the application.

## Database Schema

### Tables Created
- `users` - User accounts
- `classes` - Class information
- `class_user` - Many-to-many relationship between users and classes
- `announcements` - Class announcements
- `materials` - Uploaded learning materials
- `assignments` - Assignment information
- `submissions` - Assignment submissions

## Usage

### Getting Started
1. Register a new account or login
2. Create your first class or join an existing one using a class code
3. Start sharing materials and creating assignments

### Creating a Class
1. Go to Dashboard → Create Class
2. Fill in class name, subject, and description
3. Share the auto-generated class code with students

### Joining a Class
1. Click "Join Class" from Dashboard or Classes page
2. Enter the 6-digit class code
3. You'll be added to the class immediately

### Sharing Materials
1. Go to a class → Materials
2. Click "Upload Material"
3. Add title, description, and select file
4. Supported formats: PDF, JPG, PNG, GIF, MP4, AVI, MOV

### Creating Assignments
1. Go to a class → Assignments
2. Click "Create Assignment"
3. Set title, description, due date, and max points
4. Students can submit text and/or files

### Submitting Assignments
1. Go to assignment page
2. Add text content and/or upload file
3. Submit before the due date
4. Edit submissions until due date

## File Structure

```
app/
├── Http/Controllers/
│   ├── DashboardController.php
│   ├── ClassController.php
│   ├── AnnouncementController.php
│   ├── MaterialController.php
│   ├── AssignmentController.php
│   └── SubmissionController.php
├── Models/
│   ├── User.php
│   ├── ClassModel.php
│   ├── Announcement.php
│   ├── Material.php
│   ├── Assignment.php
│   └── Submission.php
database/migrations/
├── create_classes_table.php
├── create_class_user_table.php
├── create_announcements_table.php
├── create_materials_table.php
├── create_assignments_table.php
└── create_submissions_table.php
resources/views/
├── layouts/
├── components/
├── auth/
├── classes/
├── materials/
├── assignments/
└── dashboard.blade.php
```

## Security Features

- CSRF protection on all forms
- File upload validation (type, size)
- User authentication required
- Basic access control (users can only edit their own content)
- Password hashing
- SQL injection protection via Eloquent ORM

## Customization

### Adding File Types
Edit `MaterialController::getFileType()` method to support additional file types.

### Changing Upload Limits
Modify validation rules in controllers and update `php.ini` settings:
- `upload_max_filesize`
- `post_max_size`
- `max_execution_time`

### Styling
The application uses Tailwind CSS. Modify classes in Blade templates or add custom CSS in `resources/css/app.css`.

## Troubleshooting

### File Upload Issues
- Check storage permissions: `chmod -R 755 storage`
- Verify storage link: `php artisan storage:link`
- Check PHP upload limits in `php.ini`

### Database Issues
- Ensure database file exists (SQLite) or database is created (MySQL)
- Run migrations: `php artisan migrate:fresh`
- Check database permissions

### Asset Issues
- Rebuild assets: `npm run build`
- Clear cache: `php artisan cache:clear`

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open-sourced software licensed under the MIT license.