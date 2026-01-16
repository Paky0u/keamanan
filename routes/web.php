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
use App\Http\Controllers\VulnerableController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


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

    Route::get('/vulnerable', [VulnerableController::class, 'index']);
    Route::get('/vulnerable/search', [VulnerableController::class, 'search']);

    
});

require __DIR__.'/auth.php';

// --- TARUH DI PALING BAWAH ROUTES/WEB.PHP ---
Route::get('/demo-sql', function (Illuminate\Http\Request $request) {
    $email = $request->input('email');
    $password = $request->input('password'); // Ambil input password
    
    $users = [];
    $error = null;
    
    // Jika tombol ditekan
    if ($request->has('email')) {
        try {
            // 1. QUERY VULNERABLE (Cek SQL Injection)
            // Password di DB ter-hash, jadi login normal pasti gagal di query ini
            $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
            $users = Illuminate\Support\Facades\DB::select($query);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        // 2. FALLBACK: CEK LOGIN NORMAL (Support Hashing)
        // Jika query vulnerable kosong (tidak ada injection), cek login valid Laravel
        if (empty($users) && !$error) {
            $user = \App\Models\User::where('email', $email)->first();
            if ($user && \Illuminate\Support\Facades\Hash::check($password, $user->password)) {
                $users = [$user];
            }
        }

        // --- LOGIKA BARU: REDIRECT KE OTP JIKA LOGIN BERHASIL ---
        if (!empty($users)) {
            // Ambil user pertama (Jika SQL Injection 'OR 1=1', biasanya dapat user ID 1 / Admin)
            // Kita butuh Model Eloquent untuk Auth::login
            $userId = $users[0]->id;
            $userModel = \App\Models\User::find($userId);

            if ($userModel) {
                // 1. Login User secara resmi ke sesi Laravel
                \Illuminate\Support\Facades\Auth::login($userModel);

                // 2. Generate OTP & Set Session (Simulasi masuk flow 2FA)
                $otp = rand(100000, 999999);
                $userModel->update(['otp_code' => $otp, 'otp_expires_at' => now()->addMinutes(5), 'otp_attempts' => 0]);
                
                // 3. KIRIM EMAIL OTP (Agar penyerang/tester bisa mendapatkan kodenya)
                try {
                    \Illuminate\Support\Facades\Mail::to($userModel->email)->send(new \App\Mail\OTPMail($otp));
                } catch (\Exception $e) {
                    // Abaikan jika gagal kirim email (misal tidak ada koneksi internet/SMTP)
                }

                // Set session agar middleware OTP tahu user ini butuh verifikasi
                $request->session()->put('auth.otp_needed', true);

                return redirect()->route('otp.verify');
            }
        }
    }

    return view('demo_sql', [
        'users' => $users, 
        'input_email' => $email,
        'input_password' => $password,
        'error' => $error
    ]);
});