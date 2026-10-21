@extends('layouts.tknd')

@section('title', 'Detail Disposisi')
@section('page_title', 'Arsip Lembar Disposisi Resmi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <!-- Tombol Kembali & Status Kontainer -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <a href="/tknd/naskah-masuk" class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
            ⬅ Kembali ke Daftar Naskah Masuk
        </a>
        <div>
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider inline-block
                {{ (isset($disposisi->status_tugas) ? $disposisi->status_tugas : 'proses') == 'selesai' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                Status Tugas: {{ (isset($disposisi->status_tugas) ? $disposisi->status_tugas : 'proses') == 'selesai' ? '✓ Selesai' : '⏳ Proses' }}
            </span>
        </div>
    </div>

    <!-- 1. Metadata Identitas Surat Masuk -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-xs">
        <div class="border-b border-slate-100 pb-3 mb-4">
            <h4 class="font-bold text-slate-900 text-sm">📄 Metadata Asal Naskah Masuk</h4>
            <p class="text-xs text-slate-400 mt-0.5">Identitas surat resmi yang masuk ke sistem SMAN 1 Smart.</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-slate-700">
            <div class="break-words whitespace-normal">
                <label class="block font-bold text-slate-400 uppercase tracking-wide text-[10px] mb-0.5">Nomor Agenda Masuk</label>
                <p class="font-mono font-bold text-slate-900 text-sm leading-normal">{{ $naskah->nomor_agenda ?? '—' }}</p>
            </div>
            <div class="break-words whitespace-normal">
                <label class="block font-bold text-slate-400 uppercase tracking-wide text-[10px] mb-0.5">Nomor Surat Masuk</label>
                <p class="font-mono font-bold text-slate-900 text-sm leading-normal">{{ $naskah->no_surat }}</p>
            </div>
            <div>
                <label class="block font-bold text-slate-400 uppercase tracking-wide text-[10px] mb-0.5">Kode Klasifikasi Kearsipan</label>
                <p class="font-medium text-slate-900">📂 {{ $naskah->kode_klasifikasi ?? '-' }}</p>
            </div>
            <div class="break-words whitespace-normal">
                <label class="block font-bold text-slate-400 uppercase tracking-wide text-[10px] mb-0.5">Instansi Asal Pengirim</label>
                <p class="font-semibold text-sky-600 leading-normal">🏢 {{ $naskah->asal_pengirim }}</p>
            </div>
            <div>
                <label class="block font-bold text-slate-400 uppercase tracking-wide text-[10px] mb-0.5">Tanggal Terima Sistem</label>
                <p class="font-medium text-slate-900">📅 {{ \Carbon\Carbon::parse($naskah->tgl_terima)->translatedFormat('d F Y') }}</p>
            </div>
            <div class="sm:col-span-2 break-words whitespace-normal">
                <label class="block font-bold text-slate-400 uppercase tracking-wide text-[10px] mb-0.5">Perihal / Isi Ringkas Surat</label>
                <p class="font-medium text-slate-800 bg-slate-50 p-3 rounded border border-slate-100 leading-relaxed">{{ $naskah->perihal }}</p>
            </div>
        </div>
    </div>

    <!-- 2. Lembar Instruksi Disposisi Kepala Sekolah -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-xs relative overflow-hidden z-10">
        
        <!-- Konten Utama (Berada di atas dengan z-10) -->
        <div class="space-y-4 text-xs relative z-10">
            
            <div class="border-b border-slate-100 pb-3">
                <h4 class="font-bold text-slate-900 text-sm">🚀 Instruksi & Mandat Pimpinan</h4>
                <p class="text-xs text-slate-400 mt-0.5">Lembar penugasan dan arahan langsung dari Kepala Sekolah.</p>
            </div>

            <!-- Sifat Instruksi Pimpinan -->
            <div>
                <label class="block font-bold text-slate-400 uppercase tracking-wide text-[10px] mb-1">Sifat Instruksi Utama:</label>
                @php
                    $sifat = isset($disposisi->sifat_instruksi) ? $disposisi->sifat_instruksi : 'tindak_lanjuti';
                @endphp
                <span class="px-2.5 py-1 rounded font-bold text-[10px] uppercase tracking-wide bg-sky-50 text-sky-700 border border-sky-200 inline-block shadow-2xs">
                    @if($sifat == 'tindak_lanjuti') 👉 1. Tindak Lanjuti & Laporkan Hasilnya
                    @elseif($sifat == 'hadiri_wakili') 📅 2. Hadiri / Wakili Pimpinan
                    @elseif($sifat == 'tanggapi_saran') ✍️ 3. Tanggapi & Berikan Saran/Koreksi
                    @elseif($sifat == 'pedomani') 📌 4. Pedomani / Pelajari Prinsipnya
                    @else 📁 5. Arsipkan Dokumen Resmi
                    @endif
                </span>
            </div>

            <!-- Daftar Penerima Mandat -->
            <div>
                <label class="block font-bold text-slate-400 uppercase tracking-wide text-[10px] mb-1.5">Diteruskan Kepada (Pejabat/Staf Terkait):</label>
                <div class="flex flex-wrap gap-1.5">
                    @php
                        $ids = isset($disposisi->guru_id_tujuan) ? json_decode($disposisi->guru_id_tujuan, true) : [];
                    @endphp
                    @if(is_array($ids) && count($ids) > 0)
                        @foreach($ids as $id)
                            @if(isset($allUsers[$id]))
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 border border-slate-200 rounded text-xs font-semibold inline-block">
                                    • {{ $allUsers[$id]->name }}
                                </span>
                            @endif
                        @endforeach
                    @else
                        <span class="text-slate-400 italic">Tidak ada target jabatan terdaftar</span>
                    @endif
                </div>
            </div>

            <!-- Memo Petunjuk Khusus Pimpinan -->
            <div class="break-words whitespace-normal">
                <label class="block font-bold text-slate-400 uppercase tracking-wide text-[10px] mb-1">Catatan / Memo Khusus Kepala Sekolah:</label>
                <div class="p-3 bg-amber-50/50 border border-amber-200/60 rounded-lg text-slate-700 font-medium leading-relaxed">
                    {{ isset($disposisi->catatan_pimpinan) ? $disposisi->catatan_pimpinan : (isset($disposisi->catatan) ? $disposisi->catatan : '-') }}
                </div>
            </div>

            <!-- Laporan Balik Hasil Kerja Pelaksana -->
            <div class="break-words whitespace-normal">
                <label class="block font-bold text-slate-400 uppercase tracking-wide text-[10px] mb-1">Laporan Perkembangan Pelaksanaan Tugas Dinas:</label>
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-700 leading-relaxed font-mono">
                    @if(isset($disposisi->laporan_balik_guru) && !empty($disposisi->laporan_balik_guru))
                        ✅ <span class="font-sans font-medium text-slate-800">{{ $disposisi->laporan_balik_guru }}</span>
                    @else
                        ⏳ <span class="text-slate-400 italic font-sans font-normal">Belum ada unggahan laporan perkembangan hasil kerja dari pejabat/staf pelaksana terkait.</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 🔑 PENYEMBUHAN TOTAL WATERMARK: Menggunakan penempatan inline-flex kanan bawah yang aman dan rapi -->
        <div class="flex justify-end items-end mt-4 pt-2 border-t border-slate-100/60 select-none pointer-events-none">
            <span class="font-mono text-[9px] font-bold tracking-widest text-slate-300 bg-slate-100 px-2 py-0.5 rounded-sm uppercase">
                🏛️ Dokumen Disposisi Internal Digital
            </span>
        </div>


    </div>

</div>
@endsection
