<?php

namespace App\Http\Controllers;

use App\Models\NaskahKeluar;
use App\Models\User;
use App\Support\NomorAgenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NaskahKeluarController extends Controller
{
    public function index()
    {
        $user = $this->user();

        if (
            !$user ||
            !in_array($user->role, [
                'kepala_sekolah',
                'kepala_tu',
                'pengadministrasi_perkantoran',
                'operator_layanan',
            ], true)
        ) {
            abort(403, 'Anda tidak memiliki hak akses ke daftar naskah keluar.');
        }

        $naskahKeluar = NaskahKeluar::orderBy('created_at', 'desc')->get();

        return view('tknd.naskah-keluar.index', compact('naskahKeluar'));
    }

    public function create()
    {
        if (! $this->user()?->isKonseptorNaskahKeluar()) {
            return redirect()->route('naskah-keluar.index')
                ->with('error', 'Hanya Operator Layanan atau Pengadministrasi yang boleh menyusun konsep naskah keluar.');
        }

        return view('tknd.naskah-keluar.create');
    }

    public function store(Request $request)
    {
        if (! $this->user()?->isKonseptorNaskahKeluar()) {
            return redirect()->route('naskah-keluar.index')
                ->with('error', 'Hanya Operator Layanan atau Pengadministrasi yang boleh menyusun konsep naskah keluar.');
        }

        $request->validate([
            'tujuan_surat' => 'required|string|max:255',
            'perihal' => 'required|string',
            'kode_klasifikasi' => 'required|string',
            'sifat' => 'required|in:biasa,penting,rahasia',
            'file_draft_mentah' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $fileName = null;
        if ($request->hasFile('file_draft_mentah')) {
            $file = $request->file('file_draft_mentah');
            $fileName = 'draf_keluar_'
    . \Illuminate\Support\Str::uuid()
    . '.'
    . strtolower($file->getClientOriginalExtension());

$file->move($this->folderPrivatNaskahKeluar(), $fileName);
        }

        NaskahKeluar::create([
            'tujuan_surat' => $request->tujuan_surat,
            'perihal' => $request->perihal,
            'pokok_isi' => $request->perihal,
            'kode_klasifikasi' => $request->kode_klasifikasi,
            'sifat' => $request->sifat,
            'file_draft_mentah' => $fileName,
            'status_alur' => 'konsep',
            'no_surat_resmi' => null,
        ]);

        return redirect()->route('naskah-keluar.index')
            ->with('success', 'Konsep naskah keluar tersimpan. Ajukan ke Kepala TU dari halaman Pantau.');
    }

    public function edit($id)
    {
        $naskah = NaskahKeluar::findOrFail($id);

        if (! $this->user()?->isKonseptorNaskahKeluar()) {
            return redirect()->route('naskah-keluar.index')
                ->with('error', 'Anda tidak berhak mengubah konsep naskah keluar.');
        }

        if ($naskah->status_alur !== 'konsep') {
            return redirect()->route('naskah-keluar.index')
                ->with('error', 'Naskah tidak dapat diedit karena sudah masuk tahap pemeriksaan.');
        }

        return view('tknd.naskah-keluar.edit', compact('naskah'));
    }

    public function update(Request $request, $id)
    {
        $naskah = NaskahKeluar::findOrFail($id);

        if (! $this->user()?->isKonseptorNaskahKeluar() || $naskah->status_alur !== 'konsep') {
            return redirect()->route('naskah-keluar.index')
                ->with('error', 'Naskah tidak dapat diubah pada tahap ini.');
        }

        $request->validate([
            'tujuan_surat' => 'required|string|max:255',
            'perihal' => 'required|string',
            'kode_klasifikasi' => 'required|string',
            'sifat' => 'required|in:biasa,penting,rahasia',
            'file_draft_mentah' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $fileName = $naskah->file_draft_mentah;
        if ($request->hasFile('file_draft_mentah')) {
            $namaLama = $naskah->file_draft_mentah;

if (
    $namaLama &&
    basename($namaLama) === $namaLama &&
    is_file($this->folderPrivatNaskahKeluar() . DIRECTORY_SEPARATOR . $namaLama)
) {
    unlink($this->folderPrivatNaskahKeluar() . DIRECTORY_SEPARATOR . $namaLama);
}

            $file = $request->file('file_draft_mentah');
            $fileName = 'draf_keluar_'
    . \Illuminate\Support\Str::uuid()
    . '.'
    . strtolower($file->getClientOriginalExtension());

$file->move($this->folderPrivatNaskahKeluar(), $fileName);
        }

        $naskah->update([
            'tujuan_surat' => $request->tujuan_surat,
            'perihal' => $request->perihal,
            'pokok_isi' => $request->perihal,
            'kode_klasifikasi' => $request->kode_klasifikasi,
            'sifat' => $request->sifat,
            'file_draft_mentah' => $fileName,
        ]);

        return redirect()->route('naskah-keluar.index')->with('success', 'Perubahan konsep naskah keluar berhasil disimpan.');
    }

    public function downloadDraft($filename)
    {
        $user = $this->user();

        if (
            !$user ||
            !in_array($user->role, [
                'kepala_sekolah',
                'kepala_tu',
                'pengadministrasi_perkantoran',
                'operator_layanan',
            ], true)
        ) {
            abort(403, 'Anda tidak memiliki hak akses untuk membuka draf naskah keluar.');
        }

        if (
            !is_string($filename) ||
            basename($filename) !== $filename ||
            !preg_match('/\A[a-zA-Z0-9._-]+\.(pdf|doc|docx)\z/i', $filename)
        ) {
            abort(404);
        }

        $terdaftar = NaskahKeluar::where('file_draft_mentah', $filename)
            ->exists();

        abort_unless($terdaftar, 404, 'Berkas draf tidak terdaftar.');

        $path = $this->folderPrivatNaskahKeluar()
            . DIRECTORY_SEPARATOR
            . $filename;

        abort_unless(is_file($path), 404, 'Berkas draf tidak ditemukan.');

        return response()->download($path, $filename, [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function show($id)
    {
        $user = $this->user();

        if (
            !$user ||
            !in_array($user->role, [
                'kepala_sekolah',
                'kepala_tu',
                'pengadministrasi_perkantoran',
                'operator_layanan',
            ], true)
        ) {
            abort(403, 'Anda tidak memiliki hak akses ke detail naskah keluar.');
        }

        $naskah = NaskahKeluar::findOrFail($id);

        return view('tknd.naskah-keluar.detail', compact('naskah'));
    }

    public function prosesAlur(Request $request, $id)
    {
        $naskah = NaskahKeluar::findOrFail($id);
        $user = $this->user();

        $request->validate([
            'tindakan' => 'required|in:ajukan_tu,setuju_tu,kembalikan_konsep,setuju_pimpinan,registrasi_nomor',
            'no_surat_resmi' => 'required_if:tindakan,registrasi_nomor|nullable|string|max:100',
            'file_pdf_final' => 'required_if:tindakan,registrasi_nomor|nullable|file|mimes:pdf|max:5120',
        ]);

        $tindakan = $request->tindakan;

        if ($tindakan === 'ajukan_tu') {
            if (! $user?->isKonseptorNaskahKeluar() || $naskah->status_alur !== 'konsep') {
                return $this->alurDitolak();
            }
            $naskah->update(['status_alur' => 'pemeriksaan_tu']);
            $pesan = 'Konsep naskah keluar dikirim ke Kepala TU.';
        } elseif ($tindakan === 'setuju_tu') {
            if ($user?->role !== 'kepala_tu' || $naskah->status_alur !== 'pemeriksaan_tu') {
                return $this->alurDitolak();
            }
            $naskah->update(['status_alur' => 'persetujuan_pimpinan']);
            $pesan = 'Naskah diverifikasi Kepala TU dan diteruskan ke Kepala Sekolah.';
        } elseif ($tindakan === 'kembalikan_konsep') {
            if ($user?->role !== 'kepala_tu' || $naskah->status_alur !== 'pemeriksaan_tu') {
                return $this->alurDitolak();
            }
            $naskah->update(['status_alur' => 'konsep']);
            $pesan = 'Naskah dikembalikan ke konsep agar operator memperbaikinya.';
        } elseif ($tindakan === 'setuju_pimpinan') {
            if ($user?->role !== 'kepala_sekolah' || $naskah->status_alur !== 'persetujuan_pimpinan') {
                return $this->alurDitolak();
            }
            $naskah->update(['status_alur' => 'penomoran']);
            $pesan = 'Kepala Sekolah menyetujui naskah. Menunggu penomoran dan arsip oleh Tata Usaha.';
        } elseif ($tindakan === 'registrasi_nomor') {
            if (! $user?->isPenomoranNaskahKeluar() || $naskah->status_alur !== 'penomoran') {
                return $this->alurDitolak();
            }

            
            $file = $request->file('file_pdf_final');

            $fileNameFinal = 'final_keluar_'
                . \Illuminate\Support\Str::uuid()
                . '.pdf';

            $file->move(
    $this->folderPrivatNaskahKeluar(),
    $fileNameFinal
);

            DB::transaction(function () use ($naskah, $request, $fileNameFinal) {
                $agenda = $naskah->nomor_agenda
                    ?: NomorAgenda::berikutnya('naskah_keluar', 'K');

                $naskah->update([
                    'no_surat_resmi' => $request->no_surat_resmi,
                    'file_pdf_final' => $fileNameFinal,
                    'nomor_agenda' => $agenda,
                    'tgl_kirim' => now()->toDateString(),
                    'status_alur' => 'selesai',
                ]);
            });

            $pesan = 'Nomor agenda dan nomor surat resmi tercatat. Naskah keluar diarsip.';

        } else {
            return $this->alurDitolak();
        }

        return redirect()->route('naskah-keluar.show', $naskah->id)->with('success', $pesan);
    }

private function folderPrivatNaskahKeluar(): string
{
    $folder = storage_path('app/private/naskah_keluar');

    if (!is_dir($folder)) {
        mkdir($folder, 0750, true);
    }

    return $folder;
}

    private function user(): ?User
    {
        $user = Auth::user();

        return $user instanceof User ? $user : null;
    }

    private function alurDitolak()
    {
        return redirect()->route('naskah-keluar.index')
            ->with('error', 'Tindakan alur tidak diizinkan untuk jabatan atau tahap naskah saat ini.');
    }
}
