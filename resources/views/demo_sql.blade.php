<!DOCTYPE html>
<html>
<head>
    <title>Demo Login Bypass</title>
    <style>
        body { font-family: sans-serif; padding: 50px; background-color: #f3f4f6; }
        .box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto; }
        .danger { color: red; text-align: center; }
        input { width: 100%; padding: 10px; margin: 5px 0 15px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; background-color: #ef4444; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:hover { background-color: #dc2626; }
        .result { margin-top: 20px; padding: 15px; background: #d1fae5; color: #065f46; border-radius: 4px; }
        code { background: #eee; padding: 2px 5px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="box">
        <h2 class="danger">⚠ VULNERABLE LOGIN</h2>
        <p style="text-align: center; font-size: 14px; color: #666;">
            Form ini mensimulasikan login yang <strong>TIDAK AMAN</strong>.<br>
            Password di database di-hash, tapi kita bisa mem-bypass-nya!
        </p>

        <form method="GET">
            <label>Email:</label>
            <input type="text" name="email" value="{{ $input_email ?? '' }}" placeholder="admin@gmail.com">
            
            <label>Password:</label>
            <input type="text" name="password" value="{{ $input_password ?? '' }}" placeholder="Masukkan password...">
            
            <button type="submit">LOGIN (UNSECURE)</button>
        </form>

        @if(isset($error))
            <div style="background-color: #fee; color: red; padding: 10px; margin-top: 20px; border: 1px solid red; border-radius: 4px;">
                <strong>SQL Error:</strong> {{ $error }}
            </div>
        @endif

        @if(!empty($users))
            <div class="result">
                <strong>✅ LOGIN BERHASIL!</strong><br>
                Selamat datang, <strong>{{ $users[0]->name }}</strong>!<br>
                <small>Email: {{ $users[0]->email }}</small>
            </div>
        @elseif(request()->has('email'))
            <p style="color: red; text-align: center; margin-top: 20px;">
                ❌ Login Gagal! Email atau Password salah.
            </p>
        @endif
    </div>
</body>
</html>