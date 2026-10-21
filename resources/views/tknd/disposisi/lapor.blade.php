@extends('layouts.tknd')

@section('title', 'Lapor Pelaksanaan Tugas')
@section('page_title', 'Tindak Lanjut & Laporan Tugas Disposisi')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    
    <!-- Tombol Kembali -->
    <div>
        <a href="/tknd/naskah-masuk" class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
            ⬅ Kembali ke Daftar Surat
        </a>
    </div>

    <!-- Informasi Ringkas Instruksi Pimpinan -->
    <div class="bg-amber-50/60 border border-amber-200 p-5 rounded-xl text-xs space-y-2.5">
        <h4 class="font-bold text-amber-900 text-sm flex items-center gap-1.5">📢 Mandat/Arahan Kepala Sekolah:</h4>
        <div class="bg-white p-3 rounded border border-amber-200/80 font-medium text-slate-700 leading-relaxed whitespace-normal break-words">
            {{ $disposisi->catatan_pimpinan ?? 'Lakukan tindak lanjut sesuai dengan perihal surat masuk terkait.' }}
        </div>
        <p class="text-[11px] text-amber-800 italic">Surat Asal: <span class="font-semibold">{{ $naskah->asal_pengirim }}</span> | Perihal: "{{ $naskah->perihal }}"</p>
    </div>

    <!-- Formulir Pengisian Laporan Balik -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('disposisi.simpan-laporan', $disposisi->id) }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label for="laporan_balik_guru" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                    Laporan Hasil Pelaksanaan Tugas Dinas <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-slate-400 mb-2">Tuliskan secara ringkas dan jelas mengenai langkah tindak lanjut atau hasil kinerja yang telah dilaksanakan.</p>
                
                <textarea 
                    id="laporan_balik_guru" 
                    name="laporan_balik_guru" 
                    rows="5" 
                    class="w-full text-sm p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all placeholder:text-slate-400 @error('laporan_balik_guru') border-red-500 bg-red-50/30 @enderror"
                    placeholder="Contoh: Telah dilaksanakan koordinasi dengan tim kurikulum. Draf jadwal KBM semester ganjil selesai disusun dan siap diajukan ke pimpinan..."></textarea>
                
                @error('laporan_balik_guru')
                    <p class="text-xs text-red-600 font-medium mt-1">⚠️ {{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Aksi Submit -->
            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-5 py-2.5 rounded-lg shadow-sm transition-colors cursor-pointer">
                    🚀 Kirim Laporan ke Pimpinan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
