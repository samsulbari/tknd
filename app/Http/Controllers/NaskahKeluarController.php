<?php

namespace App\Http\Controllers;

use App\Models\NaskahKeluar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NaskahKeluarController extends Controller
{
    public function index()
    {
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
            $fileName = 'draf_keluar_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(storage_path('app/public/naskah_keluar'), $fileName);
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
            if ($naskah->file_draft_mentah && file_exists(storage_path('app/public/naskah_keluar/' . $naskah->file_draft_mentah))) {
                unlink(storage_path('app/public/naskah_keluar/' . $naskah->file_draft_mentah));
            }

            $file = $request->file('file_draft_mentah');
            $fileName = 'draf_keluar_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(storage_path('app/public/naskah_keluar'), $fileName);
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
        $filename = basename($filename);
        $path = storage_path('app/public/naskah_keluar/' . $filename);

        if (! file_exists($path)) {
            abort(404, 'Berkas fisik draf dokumen tidak ditemukan.');
        }

        return response()->download($path);
    }

    public function show($id)
    {
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

            $fileNameFinal = $naskah->file_draft_mentah;
            if ($request->hasFile('file_pdf_final')) {
                $file = $request->file('file_pdf_final');
                $fileNameFinal = 'final_keluar_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(storage_path('app/public/naskah_keluar'), $fileNameFinal);
            }

            $naskah->update([
                'no_surat_resmi' => $request->no_surat_resmi,
                'file_draft_mentah' => $fileNameFinal,
                'status_alur' => 'selesai',
            ]);
            $pesan = 'Nomor surat resmi tercatat dan naskah keluar diarsip.';
        } else {
            return $this->alurDitolak();
        }

        return redirect()->route('naskah-keluar.show', $naskah->id)->with('success', $pesan);
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
