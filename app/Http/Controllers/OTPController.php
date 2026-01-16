<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OTPController extends Controller
{
    public function show()
    {
        return view('auth.verify-otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $user = Auth::user();

        // 1. CEK APAKAH SUDAH DIBLOKIR (LEBIH DARI 3 KALI SALAH)
        if ($user->otp_attempts >= 3) {
            Auth::guard('web')->logout(); // Tendang user keluar
            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda dikunci sementara karena salah memasukkan OTP 3 kali. Silakan login ulang untuk kode baru.']);
        }

        // 2. CEK KADALUARSA (LEBIH DARI 1 MENIT)
        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluarsa (Maks 1 Menit). Silakan login ulang.']);
        }

        // 3. CEK APAKAH KODE SALAH
        if ($user->otp_code !== $request->otp) {
            // Tambah hitungan salah
            $user->increment('otp_attempts');
            $sisa = 3 - $user->otp_attempts;
            
            // Jika pas salah ke-3, langsung tendang
            if ($sisa <= 0) {
                 Auth::guard('web')->logout();
                 return redirect()->route('login')->withErrors(['email' => 'Anda salah memasukkan OTP 3 kali. Silakan login ulang.']);
            }

            return back()->withErrors(['otp' => "Kode OTP salah! Sisa percobaan: {$sisa} kali."]);
        }

        // 4. JIKA SUKSES (BERSIHKAN DATA)
        $user->update([
            'otp_code' => null, 
            'otp_expires_at' => null,
            'otp_attempts' => 0
        ]);
        
        $request->session()->forget('auth.otp_needed');
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}