<?php

namespace App\Http\Controllers;

use App\Support\AuditNaskah;
use App\Support\NomorAgenda;
use App\Support\NomorDisposisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DisposisiController extends Controller
{
    public function index()
    {
        $this->pastikanAksesPengelola();

        $dispositions = DB::table('disposisi')
            ->join('naskah_masuk', 'disposisi.naskah_masuk_id', '=', 'naskah_masuk.id')
            ->select(
                'disposisi.*',
                'naskah_masuk.no_surat',
                'naskah_masuk.perihal',
                'naskah_masuk.asal_pengirim'
            )
            ->orderByDesc('disposisi.created_at')
            ->get();

        $allUsers = DB::table('users')->get()->keyBy('id');

        return view('tknd.disposisi.index', compact('dispositions', 'allUsers'));
    }

    public function create($id)
    {
        $this->pastikanPenerbitDisposisi();

        $naskah = DB::table('naskah_masuk')->where('id', $id)->first();
        abort_unless($naskah, 404, 'Naskah masuk tidak ditemukan.');

        if (DB::table('disposisi')->where('naskah_masuk_id', $id)->exists()) {
            return redirect()->route('naskah-masuk.show', $id)
                ->with('error', 'Naskah ini sudah memiliki lembar disposisi.');
        }

        $daftarGuru = DB::table('users')
            ->whereIn('role', [
                'guru',
                'kepala_tu',
                'pengadministrasi_perkantoran',
                'operator_layanan',
            ])
            ->orderBy('name')
            ->get();

        return view('tknd.disposisi.create', compact('naskah', 'daftarGuru'));
    }

    public function store(Request $request, $id)
    {
        $this->pastikanPenerbitDisposisi();

        $rolesPenerima = [
            'guru',
            'kepala_tu',
            'pengadministrasi_perkantoran',
            'operator_layanan',
        ];

        $data = $request->validate([
            'sifat_instruksi' => 'required|in:tindak_lanjuti,hadiri_wakili,tanggapi_saran,pedomani,arsipkan',
            'klasifikasi_keamanan' => 'required|in:biasa,terbatas,rahasia',
            'batas_waktu' => 'nullable|date|after_or_equal:today',
            'guru_dituju' => 'required|array|min:1',
            'guru_dituju.*' => [
                'integer',
                'distinct',
                Rule::exists('users', 'id')->where(
                    fn ($query) => $query->whereIn('role', $rolesPenerima)
                ),
            ],
            'catatan' => 'nullable|string|max:2000',
        ]);

        $naskah = DB::table('naskah_masuk')->where('id', $id)->first();
        abort_unless($naskah, 404, 'Naskah masuk tidak ditemukan.');

        if (DB::table('disposisi')->where('naskah_masuk_id', $id)->exists()) {
            return redirect()->route('naskah-masuk.show', $id)
                ->with('error', 'Naskah ini sudah memiliki lembar disposisi.');
        }

        DB::transaction(function () use ($data, $id) {
            $disposisiId = DB::table('disposisi')->insertGetId([
                'nomor_disposisi' => NomorDisposisi::berikutnya(),
                'naskah_masuk_id' => $id,
                'klasifikasi_keamanan' => $data['klasifikasi_keamanan'],
                'sifat_instruksi' => $data['sifat_instruksi'],
                'guru_id_tujuan' => json_encode($data['guru_dituju']),
                'catatan_pimpinan' => $data['catatan'] ?? null,
                'batas_waktu' => $data['batas_waktu'] ?? null,
                'diterbitkan_oleh' => Auth::id(),
                'diterbitkan_pada' => now(),
                'status_tugas' => 'proses',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('naskah_masuk')->where('id', $id)->update([
                'status_disposisi' => 'proses',
                'updated_at' => now(),
            ]);

            AuditNaskah::catat(
                'disposisi',
                $disposisiId,
                'diterbitkan',
                null,
                'proses',
                'Lembar disposisi diterbitkan oleh pejabat berwenang.'
            );
        });

        return redirect()->route('disposisi.index')
            ->with('success', 'Lembar disposisi resmi berhasil diterbitkan.');
    }

    public function laporTugas($id)
    {
        $disposisi = DB::table('disposisi')->where('id', $id)->first();

        if (!$disposisi || !NomorAgenda::userDitugaskan($disposisi, (int) Auth::id())) {
            return redirect('/tknd')->with('error', 'Anda tidak berhak membuka laporan disposisi ini.');
        }

        $naskah = DB::table('naskah_masuk')->where('id', $disposisi->naskah_masuk_id)->first();

        return view('tknd.disposisi.lapor', compact('disposisi', 'naskah'));
    }

    public function simpanLaporan(Request $request, $id)
    {
        $request->validate([
            'laporan_balik_guru' => 'required|string|min:10|max:2000',
        ]);

        $disposisi = DB::table('disposisi')->where('id', $id)->first();

        if (!$disposisi || !NomorAgenda::userDitugaskan($disposisi, (int) Auth::id())) {
            return redirect('/tknd')->with('error', 'Anda tidak berhak mengirim laporan untuk disposisi ini.');
        }

        DB::transaction(function () use ($request, $id, $disposisi) {
            DB::table('disposisi')->where('id', $id)->update([
                'laporan_balik_guru' => $request->laporan_balik_guru,
                'status_tugas' => 'selesai',
                'updated_at' => now(),
            ]);

            DB::table('naskah_masuk')->where('id', $disposisi->naskah_masuk_id)->update([
                'status_disposisi' => 'selesai',
                'updated_at' => now(),
            ]);

            AuditNaskah::catat(
                'disposisi',
                $id,
                'laporan_diterima',
                'proses',
                'selesai',
                'Laporan tindak lanjut dikirim oleh penerima disposisi.'
            );
        });

        return redirect('/tknd')->with('success', 'Laporan tindak lanjut berhasil dikirim.');
    }

    private function pastikanAksesPengelola(): void
    {
        abort_unless(
            Auth::check() && in_array(Auth::user()->role, [
                'kepala_sekolah',
                'kepala_tu',
                'pengadministrasi_perkantoran',
            ], true),
            403,
            'Anda tidak memiliki akses ke data disposisi.'
        );
    }

    private function pastikanPenerbitDisposisi(): void
    {
        abort_unless(
            Auth::check() && Auth::user()->role === 'kepala_sekolah',
            403,
            'Hanya Kepala Sekolah atau pejabat yang didelegasikan yang dapat menerbitkan disposisi.'
        );
    }
}