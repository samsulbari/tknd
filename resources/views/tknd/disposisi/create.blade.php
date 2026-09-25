@extends('layouts.tknd')

@section('title', 'Buat Disposisi')
@section('page_title', 'Lembar Disposisi Digital Pimpinan')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
    
    <!-- Informasi Identitas Naskah Masuk -->
    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 mb-6 text-xs text-slate-700 space-y-1.5">
        <h4 class="font-bold text-slate-900 text-sm mb-1">📄 Identitas Data Surat</h4>
        <p><strong>Nomor Surat:</strong> {{ $naskah->no_surat }}</p>
        <p><strong>Asal Pengirim:</strong> {{ $naskah->asal_pengirim }}</p>
        <p><strong>Perihal / Isi Ringkas:</strong> {{ $naskah->perihal }}</p>
    </div>

    <!-- Formulir Aksi Disposisi -->
    <form action="/tknd/disposisi/simpan/{{ $naskah->id }}" method="POST" class="space-y-4">
        @csrf

        <!-- Pilihan Sifat Instruksi Mandat Standar SRIKANDI Nasional -->
        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Sifat Instruksi Pimpinan:</label>
            <select name="sifat_instruksi" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white outline-none focus:border-sky-500 cursor-pointer text-slate-700">
                <option value="tindak_lanjuti">👉 1. Tindak Lanjuti & Laporkan Hasilnya</option>
                <option value="hadiri_wakili">📅 2. Hadiri / Wakili Pimpinan</option>
                <option value="tanggapi_saran">✍️ 3. Tanggapi & Berikan Saran/Koreksi</option>
                <option value="pedomani">📌 4. Pedomani / Pelajari Prinsipnya</option>
                <option value="arsipkan">📁 5. Arsipkan Dokumen Resmi</option>
            </select>
        </div>

        <!-- Pilihan Banyak Jabatan / Penerima Tugas Dinas (Grouping Mode) -->
        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Diteruskan Kepada (Bisa Pilih Lebih Dari 1 Jabatan):</label>
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 max-h-72 overflow-y-auto space-y-4">
                
                <!-- KELOMPOK 1: STAF TATA USAHA & OPERATOR -->
                <div>
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">💼 Bagian Tata Usaha & Administrasi</span>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach($daftarGuru->whereIn('name', ['Operator Layanan Operasional', 'Pengadministrasi Perkantoran', 'Staf Tata Usaha', 'Operator Sekolah (Dapodik/Sistem)']) as $guru)
                            <label class="flex items-center space-x-2 p-2 bg-white rounded border border-slate-200 hover:bg-slate-100 transition-all cursor-pointer text-xs font-semibold text-slate-700 shadow-2xs">
                                <input type="checkbox" name="guru_dituju[]" value="{{ $guru->id }}" class="rounded text-sky-600 focus:ring-sky-500 cursor-pointer w-4 h-4">
                                <span>{{ $guru->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- KELOMPOK 2: JABATAN GURU & WAKIL KEPALA SEKOLAH -->
                <div>
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">🧑‍🏫 Bagian Wakil Kepala Sekolah & Koordinator</span>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach($daftarGuru->whereNotIn('name', ['Operator Layanan Operasional', 'Pengadministrasi Perkantoran', 'Staf Tata Usaha', 'Operator Sekolah (Dapodik/Sistem)']) as $guru)
                            <label class="flex items-center space-x-2 p-2 bg-white rounded border border-slate-200 hover:bg-slate-100 transition-all cursor-pointer text-xs font-semibold text-slate-700 shadow-2xs">
                                <input type="checkbox" name="guru_dituju[]" value="{{ $guru->id }}" class="rounded text-sky-600 focus:ring-sky-500 cursor-pointer w-4 h-4">
                                <span>{{ $guru->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        <!-- Catatan Tambahan Petunjuk Khusus -->
        <div>
            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Catatan Tambahan / Petunjuk Khusus Pimpinan:</label>
            <textarea name="catatan" rows="3" placeholder="Tulis rincian instruksi khusus atau memo tambahan di sini jika diperlukan..." 
                      class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none focus:border-sky-500 text-slate-700 shadow-xs"></textarea>
        </div>

        <!-- Tombol Aksi Pengiriman -->
        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
            <a href="/tknd" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-lg transition-colors text-center">Batal</a>
            <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs rounded-lg shadow-md transition-colors cursor-pointer">
                🚀 Terbitkan & Kirim Lembar Disposisi
            </button>
        </div>
    </form>

</div>
@endsection
