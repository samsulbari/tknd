<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisposisiController extends Controller
{
        /**
     * Menampilkan riwayat transaksi disposisi resmi sekolah.
     */
        public function index()
        {
            // 1. Ambil data riwayat disposisi gabungan dengan naskah masuknya
            $dispositions = DB::table('disposisi')
                ->join('naskah_masuk', 'disposisi.naskah_masuk_id', '=', 'naskah_masuk.id')
                ->select(
                    'disposisi.*',
                    'naskah_masuk.no_surat',
                    'naskah_masuk.perihal',
                    'naskah_masuk.asal_pengirim'
                )
                ->orderBy('disposisi.created_at', 'desc')
                ->get();
    
            // 2. Ambil semua data user untuk mencocokkan ID dengan Nama Jabatan nanti di View
            $allUsers = DB::table('users')->get()->keyBy('id');
    
            return view('tknd.disposisi.index', compact('dispositions', 'allUsers'));
        }
    

    /**
     * Show the form for creating a new digital disposition (Leader Only).
     */
    public function create($id)
    {
        // 1. Ambil data naskah masuk yang mau didisposisikan
        $naskah = DB::table('naskah_masuk')->where('id', $id)->first();
        
        // 2. Tarik daftar semua pengguna yang memiliki peranan sebagai 'guru'
        $daftarGuru = DB::table('users')->where('role', 'guru')->get();

        // Mengarah ke nama file bahasa Inggris 'create'
        return view('tknd.disposisi.create', compact('naskah', 'daftarGuru'));
    }

    /**
     * Store a newly created disposition in storage.
     */
    public function store(Request $request, $id)
    {
        // 1. Validasi Inputan Pimpinan
        $request->validate([
            'sifat_instruksi' => 'required|in:tindak_lanjuti,hadiri_wakili,tanggapi_saran,pedomani,arsipkan',
            'guru_dituju'     => 'required|array', 
            'catatan'         => 'nullable|string',
        ]);

        // 2. Simpan Lembar Disposisi Terintegrasi ke Database
        DB::table('disposisi')->insert([
            'naskah_masuk_id'   => $id,
            'sifat_instruksi'   => $request->sifat_instruksi,
            'guru_id_tujuan'    => json_encode($request->guru_dituju), 
            'catatan_pimpinan'  => $request->catatan,
            'status_tugas'      => 'proses',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return redirect('/tknd')->with('sukses', 'Official Disposition Form Dispatched Successfully!');
    }

    /**
     * Menampilkan formulir laporan balik untuk Guru/Waka pelaksana tugas.
     */
    public function laporTugas($id)
    {
        // 1. Ambil data disposisi beserta naskah masuknya
        $disposisi = DB::table('disposisi')->where('id', $id)->first();
        if (!$disposisi) {
            return redirect('/tknd')->with('error', 'Data disposisi tidak ditemukan.');
        }

        $naskah = DB::table('naskah_masuk')->where('id', $disposisi->naskah_masuk_id)->first();

        return view('tknd.disposisi.lapor', compact('disposisi', 'naskah'));
    }

    /**
     * Memproses penyimpanan laporan balik guru dan memperbarui status alur menjadi selesai.
     */
    public function simpanLaporan(Request $request, $id)
    {
        // 1. Validasi input laporan balik
        $request->validate([
            'laporan_balik_guru' => 'required|string|min:10',
        ], [
            'laporan_balik_guru.required' => 'Kolom laporan perkembangan wajib diisi.',
            'laporan_balik_guru.min' => 'Laporan minimal harus berisi 10 karakter agar informatif.',
        ]);

        // 2. Update data laporan dan ubah status tugas menjadi selesai
        DB::table('disposisi')->where('id', $id)->update([
            'laporan_balik_guru' => $request->laporan_balik_guru,
            'status_tugas' => 'selesai',
            'updated_at' => now(),
        ]);

        return redirect('/tknd/naskah-masuk')->with('success', 'Laporan perkembangan tugas berhasil dikirim ke Pimpinan!');
    }
    
}
