@extends('layouts.tknd')

@section('title', 'Pusat Arsip')
@section('page_title', 'Buku Agenda & Pusat Arsip Laporan Sekolah')

@section('content')
<!-- TAMBAHAN CSS AKSES KHUSUS UNTUK PROSES CETAK MANUAL (100% CLEAN PRINT) -->
<style>
    @media print {
        /* 1. Sembunyikan sidebar navigasi, topbar header bawaan layout, form filter, dan tombol cetak */
        aside, header, form, button, .mb-6 {
            display: none !important;
        }
        /* 2. Bersihkan wrapper utama agar tabel melebar penuh 100% memenuhi kertas */
        .min-h-screen, main, .bg-white, .p-6, .overflow-x-auto {
            padding: 0 !important;
            margin: 0 !important;
            border: 0 !important;
            shadow: none !important;
            background: transparent !important;
            width: 100% !important;
            overflow: visible !important;
        }
        /* 3. Berikan judul print khusus di atas tabel yang hanya muncul di kertas cetak */
        .print-header {
            display: block !important;
            text-align: center;
            margin-bottom: 24px;
        }
        /* 4. Sembunyikan kolom berkas unduhan/PDF karena tidak berguna di kertas fisik */
        th:last-child, td:last-child {
            display: none !important;
        }
    }
    /* Sembunyikan judul print header saat tampil di layar komputer biasa */
    .print-header {
        display: none;
    }
</style>

<div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
    
    <!-- JUDUL KHUSUS KERTAS CETAK (Hanya Muncul Saat di-Print) -->
    <div class="print-header">
        <h2 class="text-xl font-bold text-black uppercase tracking-wide">Buku Agenda & Pusat Arsip Laporan Kepegawaian</h2>
        <h3 class="text-md font-semibold text-slate-700">SMA Negeri 1 Sumber Marga Telang</h3>
        <p class="text-xs text-slate-500 mt-1">Dicetak secara resmi via Sistem E-Arsip Platform TKND pada: {{ date('d-m-Y H:i') }} WIB</p>
        <hr class="border-black border-1 mt-3">
    </div>

    <!-- 1. HEADER MODUL ARSIP LAYAR (Otomatis Hilang Saat Print) -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-slate-100 pb-4 mb-6 gap-4">
        <div>
            <h3 class="text-base font-semibold text-slate-900">🗂️ Rekapitulasi Dokumen Dinas Terarsip</h3>
            <p class="text-xs text-slate-400 mt-0.5">Daftar kronologis gabungan seluruh naskah masuk dan surat keluar resmi sekolah.</p>
        </div>
        <!-- Tombol Cetak Utama -->
        <button onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors cursor-pointer whitespace-nowrap flex items-center gap-1.5 self-end sm:self-auto">
            🖨️ Cetak Buku Agenda
        </button>
    </div>

    <!-- 2. FORUM/BILAH FILTER PENCARIAN DATA (Otomatis Hilang Saat Print) -->
    <form action="{{ route('arsip.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-200 mb-6">
        <div>
            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Cari Kata Kunci:</label>
            <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Nomor surat, perihal..."
                   class="w-full px-3 py-1.5 border border-slate-200 bg-white rounded-lg text-xs outline-none focus:border-sky-500 text-slate-700 shadow-xs">
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Tanggal Mulai:</label>
            <input type="date" name="tgl_mulai" value="{{ $tgl_mulai ?? '' }}"
                   class="w-full px-3 py-1.5 border border-slate-200 bg-white rounded-lg text-xs outline-none focus:border-sky-500 text-slate-700 shadow-xs">
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Tanggal Selesai:</label>
            <input type="date" name="tgl_selesai" value="{{ $tgl_selesai ?? '' }}"
                   class="w-full px-3 py-1.5 border border-slate-200 bg-white rounded-lg text-xs outline-none focus:border-sky-500 text-slate-700 shadow-xs">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 py-1.5 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs rounded-lg transition-colors shadow-sm cursor-pointer text-center">
                🔍 Filter
            </button>
            @if(($search ?? false) || ($tgl_mulai ?? false) || ($tgl_selesai ?? false))
                <a href="{{ route('arsip.index') }}" class="px-2.5 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-xs rounded-lg transition-colors text-center block">
                    🔄 Reset
                </a>
            @endif
        </div>
    </form>

    <!-- 3. TABEL UTAMA BUKU AGENDA GABUNGAN KRONOLOGIS (INI YANG TERCETAK UTAMA) -->
    <div class="overflow-x-auto border border-slate-100 rounded-lg">
        <table class="w-full text-left border-collapse border border-slate-200">
            <thead>
                <tr class="bg-slate-50/70 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                    <th class="px-4 py-3 border border-slate-200 w-24">Tipe</th>
                    <th class="px-4 py-3 border border-slate-200">Nomor Resmi Dokumen</th>
                    <th class="px-4 py-3 border border-slate-200">Perihal / Isi Ringkas</th>
                    <th class="px-4 py-3 border border-slate-200">Pihak Terkait</th>
                    <th class="px-4 py-3 border border-slate-200 w-32">Tanggal</th>
                    <th class="px-4 py-3 border border-slate-200 w-24 text-center">Berkas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-slate-800 text-xs">
                @if($arsip->isEmpty())
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-400 bg-slate-50/20 font-medium">
                            📂 Belum ada rekaman arsip dokumen yang sesuai dengan kriteria pencarian.
                        </td>
                    </tr>
                @else
                    @foreach($arsip as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-3 border border-slate-200 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] uppercase tracking-wide 
                                    {{ $item->tipe_arsip == 'masuk' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-purple-50 text-purple-700 border border-purple-200' }}">
                                    {{ $item->tipe_arsip == 'masuk' ? '📥 Masuk' : '📤 Keluar' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 border border-slate-200 font-mono font-bold text-slate-900 leading-tight">
                                {{ $item->nomor_surat ?? 'Belum Registrasi' }}
                            </td>
                            <td class="px-4 py-3 border border-slate-200 font-medium text-slate-800">
                                {{ $item->perihal }}
                            </td>
                            <td class="px-4 py-3 border border-slate-200 font-semibold text-slate-600">
                                {{ $item->tipe_arsip == 'masuk' ? 'Dari: ' : 'Tujuan: ' }} {{ $item->pihak_terkait }}
                            </td>
                            <td class="px-4 py-3 border border-slate-200 text-slate-600 whitespace-nowrap font-medium">
                                {{ date('d-m-Y', strtotime($item->tanggal_dokumen)) }}
                            </td>
                            <td class="px-4 py-3 border border-slate-200 text-center whitespace-nowrap">
                                @if($item->berkas)
                                    @if($item->tipe_arsip == 'masuk')
                                        <a href="/tknd/naskah-masuk/buka-pdf/{{ $item->berkas }}" target="_blank" class="text-sky-600 hover:underline font-bold">Lihat PDF</a>
                                    @else
                                        <a href="/tknd/naskah-keluar/buka-draf/{{ $item->berkas }}" target="_blank" class="text-purple-600 hover:underline font-bold">Unduh</a>
                                    @endif
                                @else
                                    <span class="text-slate-400 italic">Kosong</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

</div>
@endsection
