<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Memproses Verifikasi Login Akun & Validasi Tingkat Akses 5 Peran (Role)
     */
    public function loginProses(Request $request)
    {
        // 1. Validasi Inputan Form Login
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|in:kepala_sekolah,kepala_tu,pengadministrasi_perkantoran,operator_layanan,guru',
        ]);

        // 2. Coba Lakukan Autentikasi ke Database MySQL
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            
            // 3. Proteksi: Periksa apakah role di database cocok dengan role yang dipilih saat login
            if (Auth::user()->role !== $credentials['role']) {
                Auth::logout(); // Paksa keluar jika pilihan jabatan salah
                return back()->withErrors([
                    'role' => 'Hak akses divisi yang Anda pilih salah atau tidak sesuai jabatan akun!'
                ])->withInput();
            }

            // Jika sukses cocok semuanya, perbarui sesi dan masuk ke dashboard
            $request->session()->regenerate();
            return redirect()->intended('/tknd');
        }

        // Jika email atau password salah
        return back()->withErrors([
            'email' => 'Alamat email resmi atau kata sandi yang Anda masukkan keliru!'
        ])->withInput();
    }

    /**
     * Memproses Aksi Keluar Sistem (Logout)
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
