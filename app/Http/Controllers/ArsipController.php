<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArsipController extends Controller
{
    /**
     * Menampilkan Halaman Buku Agenda & Pusat Arsip Gabungan (Naskah Masuk & Keluar)
     * Hanya dapat diakses oleh Kepala Sekolah dan Kepala TU
     */
    public function index(Request $request)
    {
        // 1. Tangkap parameter inputan filter dari halaman view
        $search = $request->input('q');
        $tgl_mulai = $request->input('tgl_mulai');
        $tgl_selesai = $request->input('tgl_selesai');

        // 2. Query untuk Naskah Masuk
        $masuk = DB::table('naskah_masuk')
            ->select(
                'id',
                'no_surat as nomor_surat', 
                'perihal',
                'asal_pengirim as pihak_terkait',
                'created_at as tanggal_dokumen', 
                DB::raw("'masuk' as tipe_arsip"),
                'file_pdf as berkas'
            );

        // Filter kata kunci Naskah Masuk
        if ($search) {
            $masuk->where(function($query) use ($search) {
                $query->where('no_surat', 'like', "%{$search}%")
                      ->orWhere('perihal', 'like', "%{$search}%")
                      ->orWhere('asal_pengirim', 'like', "%{$search}%");
            });
        }
        // Filter rentang tanggal Naskah Masuk
        if ($tgl_mulai && $tgl_selesai) {
            $masuk->whereBetween(DB::raw('DATE(created_at)'), [$tgl_mulai, $tgl_selesai]);
        }

        // 3. Query untuk Naskah Keluar
        $keluar = DB::table('naskah_keluar')
            ->select(
                'id',
                'no_surat_resmi as nomor_surat',
                'perihal',
                'tujuan_surat as pihak_terkait',
                'created_at as tanggal_dokumen',
                DB::raw("'keluar' as tipe_arsip"),
                'file_draft_mentah as berkas'
            )
            ->where('status_alur', 'selesai');

        // Filter kata kunci Naskah Keluar
        if ($search) {
            $keluar->where(function($query) use ($search) {
                $query->where('no_surat_resmi', 'like', "%{$search}%")
                      ->orWhere('perihal', 'like', "%{$search}%")
                      ->orWhere('tujuan_surat', 'like', "%{$search}%");
            });
        }
        // Filter rentang tanggal Naskah Keluar
        if ($tgl_mulai && $tgl_selesai) {
            $keluar->whereBetween(DB::raw('DATE(created_at)'), [$tgl_mulai, $tgl_selesai]);
        }

        // 4. Penggabungan Union dan Urutkan
        $arsip = $masuk->union($keluar)
            ->orderBy('tanggal_dokumen', 'desc')
            ->get();

        return view('tknd.arsip.index', compact('arsip', 'search', 'tgl_mulai', 'tgl_selesai'));
    }

}
