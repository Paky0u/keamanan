<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Mail;
use App\Mail\OTPMail;
use Carbon\Carbon;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Validasi Username & Password (Bawaan Laravel)
        $request->authenticate();

        // 2. Generate OTP 6 Digit
        $otp = rand(100000, 999999);
        $user = $request->user();

        // 3. Simpan OTP ke Database (Berlaku 5 Menit)
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // 4. Kirim Email Asli
        try {
            Mail::to($user->email)->send(new OTPMail($otp));
        } catch (\Exception $e) {
            // Jika email gagal, logout user & beri pesan error
            Auth::guard('web')->logout();
            return back()->withErrors(['email' => 'Gagal mengirim OTP. Cek koneksi internet/SMTP.']);
        }

        // 5. Simpan Flag di Session bahwa user BELUM verifikasi OTP
        // Kita tidak regenerate session dulu sebelum OTP valid
        $request->session()->put('auth.otp_needed', true);
        $request->session()->put('auth.user_id', $user->id);
        
        // Logout dulu (Security Practice) agar user tidak bisa akses dashboard sebelum OTP
        // ATAU: Biarkan login tapi pakai Middleware khusus. 
        // AGAR MUDAH: Kita biarkan login, tapi arahkan ke halaman OTP.
        
        return redirect()->route('otp.verify');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}