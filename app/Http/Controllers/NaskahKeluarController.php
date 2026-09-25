<?php

namespace App\Http\Controllers;

use App\Models\NaskahKeluar;
use Illuminate\Http\Request;

class NaskahKeluarController extends Controller
{
    /**
     * Tampilan Utama: Menampilkan Daftar Konsep Naskah Keluar
     * Lokasi: resources/views/tknd/naskah-keluar/index.blade.php
     */
    public function index()
    {
        // Mengambil seluruh data naskah keluar diurutkan dari yang terbaru diajukan
        $naskahKeluar = NaskahKeluar::orderBy('created_at', 'desc')->get();
        
        return view('tknd.naskah-keluar.index', compact('naskahKeluar'));
    }

    /**
     * Tampilan Formulir: Membuka halaman input penyusunan konsep draf baru
     * Lokasi: resources/views/tknd/naskah-keluar/create.blade.php
     */
    public function create()
    {
        return view('tknd.naskah-keluar.create');
    }

    /**
     * Aksi Simpan: Menangkap draf konsep dari operator dan memindahkan file (.docx/.pdf)
     */
    public function store(Request $request)
    {
        // 1. Validasi Ketat Sisi Server
        $request->validate([
            'tujuan_surat' => 'required|string|max:255',
            'perihal' => 'required|string',
            'kode_klasifikasi' => 'required|string',
            'sifat' => 'required|in:biasa,penting,rahasia',
            'file_draft_mentah' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // Maksimal 5MB
        ]);

        // 2. Pemrosesan Upload Berkas Draf Mentah secara aman
        $fileName = null;
        if ($request->hasFile('file_draft_mentah')) {
            $file = $request->file('file_draft_mentah');
            $fileName = 'draf_keluar_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Memindahkan berkas ke folder yang baru saja Anda buat
            $file->move(storage_path('app/public/naskah_keluar'), $fileName);
        }

        // 3. MASUKKAN DATA KE DATABASE (Mengisi perihal dan pokok_isi sekaligus)
        NaskahKeluar::create([
            'tujuan_surat' => $request->tujuan_surat,
            'perihal' => $request->perihal,       // MENGISI KOLOM PERIHAL
            'pokok_isi' => $request->perihal,      // MENGISI KOLOM POKOK_ISI
            'kode_klasifikasi' => $request->kode_klasifikasi,
            'sifat' => $request->sifat,
            'file_draft_mentah' => $fileName,
            'status_alur' => 'konsep',
            'no_surat_resmi' => null,
        ]);

        // 4. Redirect kembali ke tabel utama dengan membawa flash message sukses
        return redirect()->route('naskah-keluar.index')->with('success', 'Konsep naskah keluar berhasil diajukan untuk diperiksa Kepala TU!');
    }

    /**
     * Tampilan Formulir Edit: Mengambil data lama untuk ditampilkan di form
     * Lokasi: resources/views/tknd/naskah-keluar/edit.blade.php
     */
    public function edit($id)
    {
        // Mencari data konsep naskah keluar, jika tidak ada otomatis memunculkan eror 404
        $naskah = NaskahKeluar::findOrFail($id);

        // Proteksi: Hanya surat yang berstatus masih 'konsep' yang boleh diedit oleh operator
        if ($naskah->status_alur !== 'konsep') {
            return redirect()->route('naskah-keluar.index')
                             ->with('error', 'Naskah tidak dapat diedit karena sedang dalam tahap pemeriksaan!');
        }

        return view('tknd.naskah-keluar.edit', compact('naskah'));
    }

    /**
     * Aksi Update: Memproses perubahan data form dan menangani penggantian file lama
     */
    public function update(Request $request, $id)
    {
        $naskah = NaskahKeluar::findOrFail($id);

        // 1. Validasi Inputan Sisi Server
        $request->validate([
            'tujuan_surat' => 'required|string|max:255',
            'perihal' => 'required|string',
            'kode_klasifikasi' => 'required|string',
            'sifat' => 'required|in:biasa,penting,rahasia',
            'file_draft_mentah' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // Maksimal 5MB
        ]);

        // 2. Pemrosesan File Jika Operator Mengunggah Berkas Baru
        $fileName = $naskah->file_draft_mentah; // Default menggunakan file lama
        if ($request->hasFile('file_draft_mentah')) {
            // Hapus file fisik lama di folder jika sebelumnya sudah pernah ada berkas
            if ($naskah->file_draft_mentah && file_exists(storage_path('app/public/naskah_keluar/' . $naskah->file_draft_mentah))) {
                unlink(storage_path('app/public/naskah_keluar/' . $naskah->file_draft_mentah));
            }

            // Simpan berkas yang baru diunggah
            $file = $request->file('file_draft_mentah');
            $fileName = 'draf_keluar_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(storage_path('app/public/naskah_keluar'), $fileName);
        }

        // 3. Perbarui Data di Database MySQL SMAN 1 Smart
        $naskah->update([
            'tujuan_surat' => $request->tujuan_surat,
            'perihal' => $request->perihal,
            'pokok_isi' => $request->perihal, // Mengisi kedua kolom sekaligus agar sinkron
            'kode_klasifikasi' => $request->kode_klasifikasi,
            'sifat' => $request->sifat,
            'file_draft_mentah' => $fileName,
        ]);

        return redirect()->route('naskah-keluar.index')->with('success', 'Perubahan konsep naskah keluar berhasil disimpan!');
    }

        /**
     * Membuka atau Mengunduh Berkas Draf Mentah dari Folder Proyek Internal
     * Solusi Bypass Eror Windows & Aman Saat Upload ke Hosting
     */
    public function downloadDraft($filename)
    {
        // Mengunci lokasi pencarian file pada folder fisik storage internal naskah keluar
        $path = storage_path('app/public/naskah_keluar/' . $filename);

        // Proteksi jika nama berkas ada di database namun file fisiknya tidak sengaja terhapus di server
        if (!file_exists($path)) {
            abort(404, 'Berkas fisik draf dokumen tidak ditemukan di dalam folder server SMAN 1 Smart.');
        }

        // Otomatis mengunduh berkas ke komputer pengguna dengan mempertahankan nama aslinya
        return response()->download($path);
    }
    
/**
 * Menampilkan Halaman Detail & Pemantauan Tahapan Alur Naskah Keluar
 * Lokasi: resources/views/tknd/naskah-keluar/detail.blade.php
 */
    public function show($id)
    {
        $naskah = NaskahKeluar::findOrFail($id);
        return view('tknd.naskah-keluar.detail', compact('naskah'));
    }

    /**
    * Memproses Perubahan Tahapan Status Alur Surat Keluar (Verifikasi Ka. TU & TTD Pimpinan)
    */
    public function prosesAlur(Request $request, $id)
    {
        $naskah = NaskahKeluar::findOrFail($id);
        
        // Sesuaikan validasi agar mendukung pilihan 'registrasi_nomor'
        $request->validate([
            'tindakan' => 'required|in:ajukan_tu,setuju_tu,registrasi_nomor',
            'no_surat_resmi' => 'required_if:tindakan,registrasi_nomor|nullable|string',
            'file_pdf_final' => 'required_if:tindakan,registrasi_nomor|nullable|file|mimes:pdf|max:5120',
        ]);

        if ($request->tindakan === 'ajukan_tu') {
            $naskah->update(['status_alur' => 'pemeriksaan_tu']);
            $pesan = 'Konsep naskah keluar berhasil dikirim ke meja Kepala TU untuk diperiksa!';
            
        } elseif ($request->tindakan === 'setuju_tu') {
            $naskah->update(['status_alur' => 'persetujuan_pimpinan']);
            $pesan = 'Naskah keluar berhasil diverifikasi Ka. TU dan diteruskan ke Kepala Sekolah!';
            
        } elseif ($request->tindakan === 'registrasi_nomor') {
            // Proses Upload File PDF Final yang sudah ditandatangani basah/elektronik
            $fileNameFinal = $naskah->file_draft_mentah; // Backup jika tidak upload file baru
            if ($request->hasFile('file_pdf_final')) {
                $file = $request->file('file_pdf_final');
                $fileNameFinal = 'final_keluar_' . time() . '.' . $file->getClientOriginalExtension();
                // Disimpan di folder yang sama agar aman
                $file->move(storage_path('app/public/naskah_keluar'), $fileNameFinal);
            }

            // Update nomor resmi dan kunci status menjadi selesai / diarsip
            $naskah->update([
                'no_surat_resmi' => $request->no_surat_resmi,
                'file_draft_mentah' => $fileNameFinal, // Menimpa berkas lama dengan versi bertanda tangan
                'status_alur' => 'selesai'
            ]);
            $pesan = 'Selamat! Nomor surat resmi berhasil registrasi dan dokumen resmi diarsip!';
        }

        return redirect()->route('naskah-keluar.index')->with('success', $pesan);
    }    

}
