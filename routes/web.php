<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\OTPController; 
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// --- DASHBOARD ROUTE (SUDAH DIPASANG OTP) ---
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'otp']) 
    ->name('dashboard');

// --- OTP ROUTE ---
Route::middleware('auth')->group(function () {
    Route::get('/verify-otp', [OTPController::class, 'show'])->name('otp.verify');
    Route::post('/verify-otp', [OTPController::class, 'verify'])->name('otp.verify.store');
});

// --- AUTH ROUTES LAINNYA ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Classes
    Route::resource('classes', ClassController::class);
    Route::post('/classes/join', [ClassController::class, 'join'])->name('classes.join');
    
    // Announcements
    Route::post('/classes/{class}/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::put('/classes/{class}/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/classes/{class}/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    
    // Materials
    Route::get('/classes/{class}/materials', [MaterialController::class, 'index'])->name('materials.index');
    Route::get('/classes/{class}/materials/create', [MaterialController::class, 'create'])->name('materials.create');
    Route::post('/classes/{class}/materials', [MaterialController::class, 'store'])->name('materials.store');
    Route::get('/classes/{class}/materials/{material}', [MaterialController::class, 'show'])->name('materials.show');
    Route::get('/classes/{class}/materials/{material}/download', [MaterialController::class, 'download'])->name('materials.download');
    Route::delete('/classes/{class}/materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');
    
    // Assignments
    Route::get('/classes/{class}/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/classes/{class}/assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
    Route::post('/classes/{class}/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
    Route::get('/classes/{class}/assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');
    Route::get('/classes/{class}/assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('assignments.edit');
    Route::put('/classes/{class}/assignments/{assignment}', [AssignmentController::class, 'update'])->name('assignments.update');
    Route::delete('/classes/{class}/assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');
    
    // Submissions
    Route::post('/classes/{class}/assignments/{assignment}/submissions', [SubmissionController::class, 'store'])->name('submissions.store');
    Route::put('/classes/{class}/assignments/{assignment}/submissions/{submission}', [SubmissionController::class, 'update'])->name('submissions.update');
    Route::get('/classes/{class}/assignments/{assignment}/submissions/{submission}/download', [SubmissionController::class, 'download'])->name('submissions.download');
    Route::post('/classes/{class}/assignments/{assignment}/submissions/{submission}/grade', [SubmissionController::class, 'grade'])->name('submissions.grade');
});

require __DIR__.'/auth.php';