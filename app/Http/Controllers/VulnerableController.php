<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VulnerableController extends Controller
{
    // Halaman Form Pencarian
    public function index()
    {
        return view('vulnerable-search');
    }

    // Proses Pencarian (TIDAK AMAN / VULNERABLE)
    public function search(Request $request)
    {
        $email = $request->input('email');

        // BAHAYA: Menggabungkan string input langsung ke dalam query SQL!
        // Ini yang membuat SQL Injection bisa masuk.
        $users = DB::select("SELECT * FROM users WHERE email = '$email'");

        return view('vulnerable-search', ['users' => $users]);
    }
}