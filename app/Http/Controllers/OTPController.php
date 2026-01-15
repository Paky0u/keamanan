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

        // 1. Cek Apakah OTP Cocok
        if ($user->otp_code !== $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP salah!']);
        }

        // 2. Cek Apakah Kadaluarsa
        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluarsa. Silakan login ulang.']);
        }

        // 3. Jika Sukses: Hapus OTP dari DB & Session Flag
        $user->update(['otp_code' => null, 'otp_expires_at' => null]);
        $request->session()->forget('auth.otp_needed');
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}