<!DOCTYPE html>
<html>
<head>
    <title>Kode OTP Login</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #333; text-align: center; }
        .otp-code { font-size: 36px; font-weight: bold; letter-spacing: 5px; color: #4F46E5; text-align: center; margin: 20px 0; padding: 10px; background: #eef2ff; border-radius: 5px; }
        p { color: #666; font-size: 16px; line-height: 1.5; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Verifikasi Login</h1>
        <p>Halo,</p>
        <p>Kami mendeteksi permintaan login ke akun Anda. Gunakan kode rahasia berikut untuk melanjutkan:</p>
        
        <div class="otp-code">{{ $otp }}</div>
        
        <p>Kode ini hanya berlaku selama <strong>5 menit</strong>.</p>
        <p>Jika Anda tidak merasa melakukan login, abaikan email ini dan segera ganti password Anda.</p>
        
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>