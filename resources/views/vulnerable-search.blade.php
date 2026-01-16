<!DOCTYPE html>
<html>
<head>
    <title>Demo SQL Injection</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4 text-red-600">⚠ Demo Fitur Tidak Aman</h1>
        
        <form action="/vulnerable/search" method="GET" class="mb-6">
            <label class="block mb-2">Cari User via Email:</label>
            <input type="text" name="email" class="border p-2 w-full rounded" placeholder="Masukkan email...">
            <button type="submit" class="bg-red-500 text-white px-4 py-2 mt-2 rounded">Cari (SQL Query)</button>
        </form>

        <h2 class="font-bold border-b pb-2 mb-2">Hasil Pencarian:</h2>
        @if(isset($users))
            <ul>
                @forelse($users as $user)
                    <li class="mb-2 p-2 bg-gray-50 rounded">
                        <strong>Nama:</strong> {{ $user->name }} <br>
                        <strong>Email:</strong> {{ $user->email }} <br>
                        <strong>Password (Hash):</strong> {{ $user->password }}
                    </li>
                @empty
                    <li class="text-gray-500">User tidak ditemukan.</li>
                @endforelse
            </ul>
        @endif
    </div>
</body>
</html>