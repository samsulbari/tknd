@extends('layouts.tknd')

@section('title', 'Naskah Masuk')
@section('page_title', 'Daftar Naskah Masuk')

@section('content')
<!-- PEMBUNGKUS UTAMA LIVE SEARCH ALPINE.JS -->
<div x-data="{ 
    search: '',
    matchRow(noSurat, pengirim, perihal, klasifikasi) {
        if (this.search.trim() === '') return true;
        let k = this.search.toLowerCase();
        return noSurat.toLowerCase().includes(k) || 
               pengirim.toLowerCase().includes(k) || 
               perihal.toLowerCase().includes(k) || 
               klasifikasi.toLowerCase().includes(k);
    }
}">

    <!-- HEADER MODUL & TOMBOL REGISTRASI -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <p class="text-sm text-slate-500">Pantau dan kelola surat masuk dinas, disposisi pimpinan, serta berkas digital sekolah.</p>
        </div>
        <div>
            <a href="{{ route('naskah-masuk.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-lg shadow-sm transition-colors cursor-pointer">
                ➕ Registrasi Naskah Baru
            </a>
        </div>
    </div>

    <!-- AREA WIDGET INDIKATOR NOTIFIKASI ALERT SUKSES (LENGKAP) -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl shadow-xs flex items-center justify-between">
            <div class="flex items-center">
                <span class="text-lg mr-2">✅</span>
                <p class="text-xs font-semibold text-emerald-800">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 text-sm font-bold ml-4">✕</button>
        </div>
    @endif

    <!-- AREA INPUT PENCARIAN REAL-TIME -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm mb-6">
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 text-sm">🔍</span>
            <input type="text" x-model="search" placeholder="Ketik langsung untuk mencari nomor surat, pengirim, perihal, atau kode klasifikasi secara instan..." 
                   class="w-full pl-9 pr-4 py-2.5 border border-slate-200 rounded-lg text-sm text-slate-700 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none transition-all">
        </div>
    </div>

    <!-- TABEL UTAMA DATA NASKAH MASUK SMAN 1 SMART -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Tgl. Terima</th>
                        <th class="px-6 py-3.5">Nomor & Kode</th>
                        <th class="px-6 py-3.5">Perihal & Asal Pengirim</th>
                        <th class="px-6 py-3.5">Status Disposisi</th>
                        <th class="px-6 py-3.5 text-center">Berkas</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    
    @if($naskah->isEmpty())
        <tr>
            <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-400 font-medium bg-slate-50/30">
                <span class="text-2xl block mb-2">📂</span> Belum ada data naskah masuk yang terdaftar di database SMAN 1 Smart.
            </td>
        </tr>
    @else
        @foreach($naskah as $item)
            <!-- BARIS TR DINAMIS DENGAN LIVE SEARCH & TRANSISI -->
            <tr x-show="matchRow('{{ $item->no_surat }}', '{{ $item->asal_pengirim }}', '{{ $item->perihal }}', '{{ $item->kode_klasifikasi }}')"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                class="hover:bg-slate-50/70 transition-colors">
                
                <!-- 1. Tanggal Terima Sistem (DIAMANKAN JIKA DATABASE NULL/KOSONG) -->
                <td class="px-6 py-4 whitespace-nowrap text-slate-500 text-xs">
                    {{ $item->tgl_terima && $item->tgl_terima != '0000-00-00' ? \Carbon\Carbon::parse($item->tgl_terima)->translatedFormat('d M Y') : \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y') }}
                </td>
                
                <!-- 2. Nomor Surat & Klasifikasi Permendagri 83/2022 -->
                <td class="px-6 py-4">
                    <span class="font-semibold text-slate-900 block font-mono text-xs">{{ $item->no_surat }}</span>
                    <span class="text-[11px] text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded-sm font-medium inline-block mt-1">
                        📂 Kode: {{ $item->kode_klasifikasi ?? '-' }}
                    </span>
                </td>
                
                <!-- 3. Perihal & Asal Pengirim Instansi -->
                <td class="px-6 py-4">
                    <span class="text-slate-800 font-medium block text-sm leading-snug">{{ $item->perihal }}</span>
                    <span class="text-xs text-sky-600 font-semibold block mt-1">🏢 {{ $item->asal_pengirim }}</span>
                </td>
                
                <!-- 4. Badge Dinamis Status Disposisi Pimpinan (FIXED: BISA LIVE BERUBAH) -->
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($item->status_disposisi == 'menunggu')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                            ⏳ Menunggu Disposisi
                        </span>
                    @elseif($item->status_disposisi == 'proses')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-800 border border-blue-200">
                            ⚙️ Proses Tindak Lanjut
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            ✓ Selesai Disposisi
                        </span>
                    @endif
                </td>
                
                <!-- 5. Jalur Aman Pratinjau Berkas PDF Internal Proyek -->
                <td class="px-6 py-4 text-center whitespace-nowrap">
                    <a href="/tknd/naskah-masuk/buka-pdf/{{ $item->file_pdf }}" target="_blank" 
                       class="inline-flex items-center text-xs font-semibold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-2.5 py-1.5 rounded-lg border border-red-100 transition-colors">
                        📄 <span class="underline ml-1">Lihat PDF</span>
                    </a>
                </td>

                <!-- 6. Tombol Tindakan Cepat / Aksi Akses Rute Resmi -->
                <td class="px-6 py-4 text-right whitespace-nowrap font-medium text-xs">
                    <div class="inline-flex gap-3">
                        @if($item->status_disposisi == 'menunggu')
                            <!-- Rute mengarah ke nama rute bahasa inggris 'create' yang tadi kita buat -->
                            <a href="/tknd/disposisi/buat/{{ $item->id }}" class="text-sky-600 hover:text-sky-800 font-bold transition-colors">Disposisi</a>
                        @else
                            <a href="/tknd/naskah-masuk/detail/{{ $item->id }}" class="text-slate-500 hover:text-slate-700 transition-colors">Detail</a>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    @endif

</tbody>

            </table>
        </div>

        <!-- AREA PAGINATION (Navigasi Halaman) -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="text-xs text-slate-500">Menampilkan 1 sampai {{ $naskah->count() }} dari {{ $naskah->count() }} total naskah masuk</span>
            <div class="inline-flex rounded-lg border border-slate-200 overflow-hidden bg-white shadow-sm text-xs font-medium text-slate-700">
                <button class="px-3 py-2 bg-slate-50 text-slate-400 border-r border-slate-200 cursor-not-allowed">Sebelumnya</button>
                <button class="px-3 py-2 bg-sky-50 text-sky-600 border-r border-slate-200 font-semibold">1</button>
                <button class="px-3 py-2 hover:bg-slate-50">Selanjutnya</button>
            </div>
        </div>
    </div>

</div> <!-- PENUTUP LIVE SEARCH ALPINE.JS (FIX) -->
@endsection
