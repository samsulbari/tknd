<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // 🔑 AMAN: Mengimpor Fasad DB agar kalkulasi berjalan lancar

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Utama dengan Hitungan Indikator Riil & Tindak Lanjut Terkini
     */
    public function index()
    {
        // 1. Kumpulkan Hitungan Riil untuk 4 Kotak Ringkasan Atas dari Database
        $totalMasuk = DB::table('naskah_masuk')->count();
        
        // Menghitung naskah masuk yang BELUM memiliki lembar instruksi di tabel disposisi
        $perluDisposisi = DB::table('naskah_masuk')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('disposisi')
                      ->whereColumn('disposisi.naskah_masuk_id', 'naskah_masuk.id');
            })->count();

        $totalKeluar = DB::table('naskah_keluar')->count();
        
        // Akumulasi Dokumen Terarsip Resmi (Semua Naskah Masuk + Naskah Keluar yang Selesai)
        $arsipKeluar = DB::table('naskah_keluar')->where('status_alur', 'selesai')->count();
        $totalArsip = $totalMasuk + $arsipKeluar;

        // 2. Tarik Daftar Tindak Lanjut Naskah Terkini (Maksimal 5 surat masuk terbaru)
        $naskahTerkini = DB::table('naskah_masuk')
            ->leftJoin('disposisi', 'naskah_masuk.id', '=', 'disposisi.naskah_masuk_id')
            ->select(
                'naskah_masuk.id',
                'naskah_masuk.nomor_agenda',
                'naskah_masuk.no_surat',
                'naskah_masuk.perihal',
                'naskah_masuk.asal_pengirim',
                DB::raw("CASE
                    WHEN disposisi.id IS NULL THEN 'Menunggu Disposisi'
                    WHEN disposisi.status_tugas = 'selesai' THEN 'Selesai Disposisi'
                    ELSE 'Dalam Proses'
                END as status_tindakan")
            )
            ->orderBy('naskah_masuk.created_at', 'desc')
            ->limit(5)
            ->get();

        // 3. Lemparkan seluruh variabel hitungan ke halaman view dashboard
        // Menyesuaikan dengan path folder view dashboard Anda (tknd.dashboard)
        return view('tknd.dashboard', compact('totalMasuk', 'perluDisposisi', 'totalKeluar', 'totalArsip', 'naskahTerkini'));
    }
}
