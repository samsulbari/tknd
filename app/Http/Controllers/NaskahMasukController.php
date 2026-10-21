<?php

namespace App\Http\Controllers;

use App\Models\NaskahMasuk;
use App\Support\NomorAgenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NaskahMasukController extends Controller
{
            /**
     * Tampilan Utama: Menampilkan Daftar Naskah Masuk dengan Fitur Pencarian Dinamis & Status Disposisi Live
     */
    public function index(Request $request)
    {
        $this->pastikanAksesNaskahMasuk();
        // 1. Tangkap kata kunci pencarian dari input text frontend
        $search = $request->input('search');

        // 2. Buat query dasar gabungan tabel naskah masuk dan disposisi untuk mendeteksi alur UX
        $query = DB::table('naskah_masuk')
            ->leftJoin('disposisi', 'naskah_masuk.id', '=', 'disposisi.naskah_masuk_id')
            ->select(
                'naskah_masuk.*',
                // Tentukan status disposisi dinamis berdasarkan eksistensi data di tabel disposisi
                DB::raw('CASE 
                    WHEN disposisi.id IS NULL THEN "menunggu"
                    WHEN disposisi.status_tugas = "proses" THEN "proses"
                    ELSE "selesai"
                END as status_disposisi')
            );

        // 3. Jika pengguna mengetik sesuatu, lakukan penyaringan multi-parameter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('naskah_masuk.no_surat', 'LIKE', "%{$search}%")
                  ->orWhere('naskah_masuk.asal_pengirim', 'LIKE', "%{$search}%")
                  ->orWhere('naskah_masuk.perihal', 'LIKE', "%{$search}%")
                  ->orWhere('naskah_masuk.kode_klasifikasi', 'LIKE', "%{$search}%")
                  ->orWhere('naskah_masuk.nomor_agenda', 'LIKE', "%{$search}%");
            });
        }

        // 4. Ambil data hasil filter dan urutkan berdasarkan yang terbaru
        $naskah = $query->orderBy('naskah_masuk.created_at', 'desc')->get();
        
        return view('tknd.naskah-masuk.index', compact('naskah'));
    }

    /**
     * Menampilkan detail naskah masuk sekaligus lembar disposisi terkait secara terintegrasi.
     * (Solusi Gabungan: Mencegah Eror Rediklarasi / Bentrok Fungsi Kembar)
     */
    public function show($id)
    {
        $this->pastikanAksesNaskahMasuk();
        // 1. Ambil data spesifik naskah masuk menggunakan Query Builder DB
        $naskah = DB::table('naskah_masuk')->where('id', $id)->first();
        
        if (!$naskah) {
            return redirect('/tknd/naskah-masuk')->with('error', 'Data naskah tidak ditemukan.');
        }

        // 2. Ambil berkas lembar disposisi pasangannya (jika sudah diterbitkan)
        $disposisi = DB::table('disposisi')->where('naskah_masuk_id', $id)->first();

        // 3. Tarik seluruh daftar user untuk konversi nama jabatan di halaman view
        $allUsers = DB::table('users')->get()->keyBy('id');

        // 4. Kembalikan ke view detail naskah masuk Anda yang sudah dilengkapi sistem fallback
        return view('tknd.naskah-masuk.detail', compact('naskah', 'disposisi', 'allUsers'));
    }
    
    /**
     * Tampilan Formulir: Membuka halaman input registrasi baru
     * Lokasi: resources/views/tknd/naskah-masuk/create.blade.php
     */
    public function create()
    {
        $this->pastikanAksesNaskahMasuk();
        return view('tknd.naskah-masuk.create');
    }

    /**
     * Aksi Simpan: Menangkap data form input dan memproses file upload PDF scan
     */
    public function store(Request $request)
    {
        $this->pastikanAksesNaskahMasuk();
        // 1. Validasi Ketat Sisi Server (Server-Side Validation)
        $request->validate([
            'no_surat' => 'required|unique:naskah_masuk,no_surat',
            'asal_pengirim' => 'required|string|max:255',
            'tgl_surat' => 'required|date',
            'perihal' => 'required|string',
            'kode_klasifikasi' => 'required|string',
            'file_pdf' => 'required|file|mimes:pdf|max:5120', // Wajib berkas PDF, Maksimal 5MB
        ]);

        // 2. Pemrosesan Aman Berkas PDF (Bypass Eror Symlink Windows & Ramah Shared Hosting)
        $fileName = null;
        if ($request->hasFile('file_pdf')) {
            $file = $request->file('file_pdf');
            
            // Membuat penamaan file yang unik berbasis waktu Unix agar tidak tumpang tindih di server hosting
            $fileName = 'naskah_masuk_'
                . \Illuminate\Support\Str::uuid()
                . '.pdf';
            
            // Memaksa file fisik berpindah langsung ke folder tujuan absolut ekosistem internal
            //$file->move(storage_path('app/public/naskah_masuk'), $fileName);
            $folderPrivat = storage_path('app/private/naskah_masuk');

            if (!is_dir($folderPrivat)) {
                mkdir($folderPrivat, 0750, true);
            }

            $file->move($folderPrivat, $fileName);
        }

        DB::transaction(function () use ($request, $fileName) {
            NaskahMasuk::create([
                'nomor_agenda' => NomorAgenda::berikutnya('naskah_masuk', 'M'),
                'no_surat' => $request->no_surat,
                'asal_pengirim' => $request->asal_pengirim,
                'tgl_surat' => $request->tgl_surat,
                'tgl_terima' => date('Y-m-d'),
                'perihal' => $request->perihal,
                'kode_klasifikasi' => $request->kode_klasifikasi,
                'file_pdf' => $fileName,
                'status_disposisi' => 'menunggu',
            ]);
        });

        return redirect()->route('naskah-masuk.index')->with('success', 'Naskah masuk berhasil didaftarkan. Nomor agenda tercatat otomatis.');
    }

    /**
     * Membuka Berkas PDF Secara Langsung dari Folder Internal Proyek 
     * Solusi Bypass Eror 403/404 Windows & Desain Siap Diupload ke Hosting
     */

    public function viewPdf($filename)
    {
        $user = Auth::user();
    
        if (!$user || !in_array($user->role, [
            'kepala_sekolah',
            'kepala_tu',
            'pengadministrasi_perkantoran',
        ], true)) {
            abort(403, 'Anda tidak memiliki hak akses untuk membuka PDF naskah masuk.');
        }
    
        // Hanya izinkan nama berkas sederhana, bukan path atau traversal.
        if (
            !is_string($filename) ||
            basename($filename) !== $filename ||
            !preg_match('/\A[a-zA-Z0-9._-]+\.pdf\z/i', $filename)
        ) {
            abort(404);
        }
    
        // Pastikan berkas memang tercatat pada data naskah masuk.
        $terdaftar = DB::table('naskah_masuk')
            ->where('file_pdf', $filename)
            ->exists();
    
        abort_unless($terdaftar, 404, 'Berkas tidak terdaftar.');
    
        // Baca dari lokasi privat, bukan storage/app/public.
        $path = storage_path('app/private/naskah_masuk/' . $filename);
    
        abort_unless(is_file($path), 404, 'Berkas PDF tidak ditemukan.');
    
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    
    private function pastikanAksesNaskahMasuk(): void
    {
        $user = Auth::user();

        abort_unless(
            $user && in_array($user->role, [
                'kepala_sekolah',
                'kepala_tu',
                'pengadministrasi_perkantoran',
            ], true),
            403,
            'Anda tidak memiliki hak akses untuk mengelola naskah masuk.'
        );
    }

    
}
