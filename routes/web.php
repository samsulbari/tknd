<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NaskahMasukController;
use App\Http\Controllers\NaskahKeluarController; 
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisposisiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArsipController; // 🔑 SUNTIKKAN BARIS PERBAIKAN INI DI SINI


// ===================================================
// 1. ROUTE PUBLIC & GERBANG UTAMA (BISA DIAKSES TANPA LOGIN)
// ===================================================
Route::get('/', function () { return view('public.index'); });
Route::get('/login', function () { return view('auth.login'); })->name('login');
Route::post('/login-proses', [AuthController::class, 'loginProses']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// ===================================================
// 2. GERBANG INTERNAL TKND (PROTEKSI PENUH MIDDLEWARE AUTH)
// ===================================================
Route::middleware(['auth'])->group(function () {

    // Beranda Utama / Dashboard Dinamis
    Route::get('/tknd', [DashboardController::class, 'index'])->name('dashboard');

    // 📥 KELOMPOK MODUL NASKAH MASUK
    Route::get('/tknd/naskah-masuk', [NaskahMasukController::class, 'index'])->name('naskah-masuk.index');
    Route::get('/tknd/naskah-masuk/buat', [NaskahMasukController::class, 'create'])->name('naskah-masuk.create');
    Route::post('/tknd/naskah-masuk/simpan', [NaskahMasukController::class, 'store'])->name('naskah-masuk.store');
    Route::get('/tknd/naskah-masuk/buka-pdf/{filename}', [NaskahMasukController::class, 'viewPdf'])->name('naskah-masuk.pdf');
    Route::get('/tknd/naskah-masuk/detail/{id}', [NaskahMasukController::class, 'show'])->name('naskah-masuk.show');

    // ⇄ KELOMPOK MODUL LEMBAR DISPOSISI PIMPINAN
    Route::get('/tknd/disposisi', [DisposisiController::class, 'index'])->name('disposisi.index');
    Route::get('/tknd/disposisi/buat/{id}', [DisposisiController::class, 'create'])->name('disposisi.create');
    Route::post('/tknd/disposisi/simpan/{id}', [DisposisiController::class, 'store'])->name('disposisi.store');

    // Rute untuk menampilkan formulir laporan balik dari guru/waka pelaksana
    Route::get('/tknd/tugas-disposisi/lapor/{id}', [App\Http\Controllers\DisposisiController::class, 'laporTugas'])->name('disposisi.lapor');

    // Rute untuk memproses dan menyimpan laporan balik ke database
    Route::post('/tknd/tugas-disposisi/simpan-laporan/{id}', [App\Http\Controllers\DisposisiController::class, 'simpanLaporan'])->name('disposisi.simpan-laporan');


    // 📤 KELOMPOK MODUL NASKAH KELUAR & ALUR VERIFIKASI
    Route::get('/tknd/naskah-keluar', [NaskahKeluarController::class, 'index'])->name('naskah-keluar.index');
    Route::get('/tknd/naskah-keluar/buat', [NaskahKeluarController::class, 'create'])->name('naskah-keluar.create');
    Route::post('/tknd/naskah-keluar/simpan', [NaskahKeluarController::class, 'store'])->name('naskah-keluar.store');
    Route::get('/tknd/naskah-keluar/edit/{id}', [NaskahKeluarController::class, 'edit'])->name('naskah-keluar.edit');
    Route::post('/tknd/naskah-keluar/update/{id}', [NaskahKeluarController::class, 'update'])->name('naskah-keluar.update');
    Route::get('/tknd/naskah-keluar/buka-draf/{filename}', [NaskahKeluarController::class, 'downloadDraft'])->name('naskah-keluar.download');
    Route::get('/tknd/naskah-keluar/detail/{id}', [NaskahKeluarController::class, 'show'])->name('naskah-keluar.show');
    Route::post('/tknd/naskah-keluar/proses-alur/{id}', [NaskahKeluarController::class, 'prosesAlur'])->name('naskah-keluar.proses-alur');

    // 📁 KELOMPOK MODUL PUSAT ARSIP & LAPORAN
    // Catatan: Ini rute untuk agenda esok hari, sudah kita siapkan jalurnya di sini
    // Route::get('/tknd/arsip', [ArsipController::class, 'index'])->name('arsip.index');
    // 📁 KELOMPOK MODUL PUSAT ARSIP & LAPORAN (SEKARANG AKTIF)
    Route::get('/tknd/arsip', [ArsipController::class, 'index'])->name('arsip.index');


});
