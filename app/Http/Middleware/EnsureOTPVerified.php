<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOTPVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user login TAPI masih butuh OTP
        if (Auth::check() && $request->session()->has('auth.otp_needed')) {
            // Jika dia sedang tidak di halaman OTP, lempar ke halaman OTP
            if (!$request->routeIs('otp.verify') && !$request->routeIs('otp.verify.store')) {
                return redirect()->route('otp.verify');
            }
        }

        return $next($request);
    }
}
