@extends('layouts.tknd')

@section('title', 'Riwayat Disposisi')
@section('page_title', 'Rekapitulasi Lembar Disposisi Pimpinan')

@section('content')
    <div class="mb-6">
        <p class="text-sm text-slate-500">Daftar pelacakan instruksi tertulis Kepala Sekolah yang telah didistribusikan kepada pejabat pelaksana.</p>
    </div>

    <!-- TABEL REKAP RIWAYAT DISPOSISI -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Asal & No. Surat</th>
                        <th class="px-6 py-3.5">Diteruskan Kepada</th>
                        <th class="px-6 py-3.5">Catatan Instruksi Pimpinan</th>
                        <th class="px-6 py-3.5">Sifat</th>
                        <th class="px-6 py-3.5">Batas Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @if($riwayatDisposisi->isEmpty())
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-400 font-medium bg-slate-50/30">
                                📂 Belum ada lembar disposisi pimpinan yang diterbitkan.
                            </td>
                        </tr>
                    @else
                        @foreach($riwayatDisposisi as $disposisi)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <!-- Asal & Nomor Surat Masuk terkait -->
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-slate-900 block text-sm">{{ $disposisi->naskahMasuk->asal_pengirim }}</span>
                                    <span class="text-xs text-slate-400 font-mono block mt-0.5">{{ $disposisi->naskahMasuk->no_surat }}</span>
                                </td>
                                
                                <!-- Daftar Pejabat Pelaksana (Array JSON) -->
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($disposisi->diteruskan_kepada as $penerima)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                👤 {{ $penerima }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                
                                <!-- Isi Memo Instruksi Kepala Sekolah -->
                                <td class="px-6 py-4">
                                    <span class="text-slate-700 text-xs font-medium leading-relaxed block max-w-md">{{ $disposisi->catatan_instruksi }}</span>
                                </td>
                                
                                <!-- Sifat Kedinasan -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($disposisi->sifat == 'biasa')
                                        <span class="px-2 py-0.5 text-[11px] font-semibold bg-slate-100 text-slate-700 rounded-full">Biasa</span>
                                    @elseif($disposisi->sifat == 'penting')
                                        <span class="px-2 py-0.5 text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200 rounded-full">Penting</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[11px] font-semibold bg-red-50 text-red-700 border border-red-200 rounded-full">Rahasia</span>
                                    @endif
                                </td>
                                
                                <!-- Deadline Pengerjaan Tugas -->
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-red-600 font-semibold">
                                    ⏰ {{ \Carbon\Carbon::parse($disposisi->batas_waktu)->translatedFormat('d M Y') }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
