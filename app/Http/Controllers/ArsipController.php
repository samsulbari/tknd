<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        // Batasi akses arsip berdasarkan peran pengguna.
        $user = Auth::user();

        if (
            !$user ||
            !in_array($user->role, [
                'kepala_sekolah',
                'kepala_tu',
                'pengadministrasi_perkantoran',
            ], true)
        ) {
            abort(403, 'Anda tidak memiliki hak akses ke arsip TKND.');
        }

        // Validasi kata kunci dan rentang tanggal.
        $validated = $request->validate([
            'q' => 'nullable|string|max:255',
            'tgl_mulai' => 'nullable|date',
            'tgl_selesai' => 'nullable|date|after_or_equal:tgl_mulai',
        ]);

        $search = $validated['q'] ?? null;
        $tgl_mulai = $validated['tgl_mulai'] ?? null;
        $tgl_selesai = $validated['tgl_selesai'] ?? null;

        // Arsip naskah masuk menggunakan tanggal penerimaan.
        $masuk = DB::table('naskah_masuk')->select(
            'id',
            'nomor_agenda',
            DB::raw('no_surat as nomor_surat'),
            'perihal',
            DB::raw('asal_pengirim as pihak_terkait'),
            DB::raw('tgl_terima as tanggal_dokumen'),
            DB::raw("'masuk' as tipe_arsip"),
            DB::raw('file_pdf as berkas')
        );

        if ($search) {
            $masuk->where(function ($q) use ($search) {
                $q->where('no_surat', 'like', "%{$search}%")
                    ->orWhere('nomor_agenda', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%")
                    ->orWhere('asal_pengirim', 'like', "%{$search}%");
            });
        }

        if ($tgl_mulai) {
            $masuk->whereDate('tgl_terima', '>=', $tgl_mulai);
        }

        if ($tgl_selesai) {
            $masuk->whereDate('tgl_terima', '<=', $tgl_selesai);
        }

        // Arsip naskah keluar hanya memuat naskah yang selesai.
        // Berkas dan tanggal menggunakan kolom khusus final/kirim.
        $keluar = DB::table('naskah_keluar')
            ->select(
                'id',
                'nomor_agenda',
                DB::raw('no_surat_resmi as nomor_surat'),
                'perihal',
                DB::raw('tujuan_surat as pihak_terkait'),
                DB::raw('tgl_kirim as tanggal_dokumen'),
                DB::raw("'keluar' as tipe_arsip"),
                DB::raw('file_pdf_final as berkas')
            )
            ->where('status_alur', 'selesai');

        if ($search) {
            $keluar->where(function ($q) use ($search) {
                $q->where('no_surat_resmi', 'like', "%{$search}%")
                    ->orWhere('nomor_agenda', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%")
                    ->orWhere('tujuan_surat', 'like', "%{$search}%");
            });
        }

        if ($tgl_mulai) {
            $keluar->whereDate('tgl_kirim', '>=', $tgl_mulai);
        }

        if ($tgl_selesai) {
            $keluar->whereDate('tgl_kirim', '<=', $tgl_selesai);
        }

        $arsip = $masuk
            ->union($keluar)
            ->orderByDesc('tanggal_dokumen')
            ->get();

        return view('tknd.arsip.index', compact(
            'arsip',
            'search',
            'tgl_mulai',
            'tgl_selesai'
        ));
    }
    
    /**
     * Menampilkan PDF naskah masuk dari pusat arsip.
     */
    public function pdfNaskahMasuk(int $id)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, [
            'kepala_sekolah',
            'kepala_tu',
            'pengadministrasi_perkantoran',
        ], true)) {
            abort(403, 'Anda tidak memiliki hak akses ke arsip TKND.');
        }

        $naskah = DB::table('naskah_masuk')
            ->where('id', $id)
            ->first();

        abort_if(!$naskah || !$naskah->file_pdf, 404);

        $filename = basename($naskah->file_pdf);
        abort_if($filename !== $naskah->file_pdf, 404);

        $path = storage_path('app/private/naskah_masuk/' . $filename);

        abort_unless(is_file($path), 404, 'Berkas PDF tidak ditemukan.');

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    /**
     * Menampilkan PDF final naskah keluar dari pusat arsip.
     */
    public function pdfNaskahKeluar(int $id)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, [
            'kepala_sekolah',
            'kepala_tu',
            'pengadministrasi_perkantoran',
        ], true)) {
            abort(403, 'Anda tidak memiliki hak akses ke arsip TKND.');
        }

        $naskah = DB::table('naskah_keluar')
            ->where('id', $id)
            ->where('status_alur', 'selesai')
            ->first();

        abort_if(!$naskah || !$naskah->file_pdf_final, 404);

        $filename = basename($naskah->file_pdf_final);
        abort_if($filename !== $naskah->file_pdf_final, 404);

        $path = storage_path('app/private/naskah_keluar/' . $filename);

        abort_unless(is_file($path), 404, 'Berkas PDF final tidak ditemukan.');

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

}
