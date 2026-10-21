@extends('layouts.tknd')

@section('title', 'Naskah Keluar')
@section('page_title', 'Daftar Konsep Naskah Keluar')

@section('content')
<div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
    
    <!-- Bagian Atas: Keterangan & Tombol Aksi -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <div class="space-y-0.5">
            <p class="text-sm text-slate-500">Ajukan konsep draf naskah dinas baru, pantau tahapan pemeriksaan Kepala TU, dan kelola penomoran resmi surat keluar sekolah.</p>
        </div>
        @if(auth()->user()->isKonseptorNaskahKeluar())
        <div>
            <a href="/tknd/naskah-keluar/buat" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold px-4 py-2.5 rounded-lg shadow-sm transition-colors cursor-pointer">
                ➕ Buat Konsep Baru
            </a>
        </div>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg">
            <p class="text-xs font-semibold text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-xs font-semibold text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <!-- TABEL DENGAN KOLOM KODE KLASIFIKASI TERPISAH MANDIRI -->
    <div class="overflow-x-auto border border-slate-100 rounded-lg">
        <table class="w-full text-left border-collapse table-fixed sm:table-auto">
            <thead>
                <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold text-slate-700 uppercase tracking-wide">
                    <th class="px-4 py-4 w-28 text-center">AGENDA</th>
                    <th class="px-4 py-4 w-36 text-center">SIFAT DOKUMEN</th>
                    <th class="px-4 py-4 w-36 text-center">KODE KLASIFIKASI</th> <!-- 🔑 KOLOM BARU TERPISAH -->
                    <th class="px-6 py-4 w-64">PERIHAL</th>
                    <th class="px-6 py-4 w-48">TUJUAN SURAT</th>
                    <th class="px-6 py-4 w-52">NOMOR RESMI</th>
                    <th class="px-6 py-4 w-44 text-center">TAHAPAN ALUR</th>
                    <th class="px-4 py-4 w-32 text-center">DRAF MENTAH</th>
                    <th class="px-6 py-4 w-36 text-right">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700 text-sm">
                @if($naskahKeluar->isEmpty())
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-slate-400 bg-slate-50/10 font-medium">
                            <span class="text-2xl block mb-2">📂</span> Belum ada data naskah keluar yang terdaftar di database SMAN 1 Smart.
                        </td>
                    </tr>
                @else
                    @foreach($naskahKeluar as $index => $item)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            
                            <!-- 1. NOMOR -->
                            <td class="px-4 py-4 text-center font-mono text-xs font-semibold text-slate-700">
                                {{ $item->nomor_agenda ?? '—' }}
                            </td>
                            
                            <!-- 2. SIFAT DOKUMEN -->
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                @if($item->sifat == 'penting')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        Penting
                                    </span>
                                @elseif($item->sifat == 'rahasia')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-800 border border-red-200">
                                        Rahasia
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-50 text-slate-700 border border-slate-200">
                                        Biasa
                                    </span>
                                @endif
                            </td>

                            <!-- 3. KODE KLASIKASI (🔑 ISI KOLOM BARU DI TENGAH DENGAN BADGE KHUSUS) -->
                            <td class="px-4 py-4 text-center whitespace-nowrap font-mono text-xs font-bold text-slate-600">
                                <span class="bg-slate-100 px-2 py-1 rounded-md border border-slate-200/60 inline-block">
                                    📂 {{ $item->kode_klasifikasi ?? '-' }}
                                </span>
                            </td>
                            
                            <!-- 4. PERIHAL (Bersih dari teks kode klasifikasi di bawahnya) -->
                            <td class="px-6 py-4 whitespace-normal break-words">
                                <span class="font-semibold text-slate-900 block leading-normal">{{ $item->perihal }}</span>
                            </td>
                            
                            <!-- 5. TUJUAN SURAT -->
                            <td class="px-6 py-4">
                                <span class="text-xs text-sky-600 font-semibold block">🏢 {{ $item->tujuan_surat }}</span>
                            </td>
                            
                            <!-- 6. NOMOR RESMI -->
                            <td class="px-6 py-4 font-mono text-xs font-bold text-slate-900 leading-normal break-words whitespace-normal">
                                @if(!empty($item->no_surat_resmi))
                                    {{ $item->no_surat_resmi }}
                                @else
                                    <span class="text-slate-400 font-sans font-normal italic">Belum Diterbitkan</span>
                                @endif
                            </td>
                            
                            <!-- 7. TAHAPAN ALUR STATUS -->
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @switch($item->status_alur)
                                    @case('konsep')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-50 text-slate-700 border border-slate-200">Konsep</span>
                                        @break
                                    @case('pemeriksaan_tu')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">Pemeriksaan TU</span>
                                        @break
                                    @case('persetujuan_pimpinan')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-800 border border-purple-200">Persetujuan Kepsek</span>
                                        @break
                                    @case('penomoran')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-800 border border-sky-200">Penomoran</span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">Selesai / Arsip</span>
                                @endswitch
                            </td>
                            
                            <!-- 8. DRAF MENTAH (🔑 SINKRONISASI RUTE RESMI DENGAN HALAMAN DETAIL) -->
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                @if($item->file_draft_mentah)
                                    <a href="{{ route('naskah-keluar.download', $item->file_draft_mentah) }}" 
                                       class="inline-flex items-center text-xs font-semibold text-amber-600 hover:text-amber-700 bg-amber-50 hover:bg-amber-100 px-2.5 py-1.5 rounded-lg border border-amber-100 transition-colors">
                                        📄 <span class="underline ml-1">Unduh Draf</span>
                                    </a>
                                @else
                                    <span class="text-slate-400 italic text-xs">Tidak Ada File</span>
                                @endif
                            </td>
                            
                            <!-- 9. AKSI -->
                            <td class="px-6 py-4 text-right whitespace-nowrap font-medium text-xs">
                                <div class="inline-flex gap-3 justify-end w-full">
                                    @if($item->status_alur === 'konsep' && auth()->user()->isKonseptorNaskahKeluar())
                                        <a href="/tknd/naskah-keluar/edit/{{ $item->id }}" class="text-sky-600 hover:text-sky-800 font-bold transition-colors">Edit</a>
                                    @endif
                                    <a href="/tknd/naskah-keluar/detail/{{ $item->id }}" class="text-slate-500 hover:text-slate-700 transition-colors">Pantau</a>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

</div>
@endsection
