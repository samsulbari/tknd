@extends('layouts.tknd')

@section('title', 'Dashboard')
@section('page_title', 'Ringkasan Kerja & Dashboard')

@section('content')
    <!-- 1. AREA KARTU INDIKATOR STATISTIK DINAMIS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        
        <!-- KOTAK 1: NASKAH MASUK -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-xs flex justify-between items-center">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Naskah Masuk</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $totalMasuk }}</h3>
            </div>
            <span class="text-xl">📥</span>
        </div>

        <!-- KOTAK 2: PERLU DISPOSISI -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-xs flex justify-between items-center">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Perlu Disposisi</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $perluDisposisi }}</h3>
            </div>
            <span class="text-xl">⇄</span>
        </div>

        <!-- KOTAK 3: NASKAH KELUAR -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-xs flex justify-between items-center">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Naskah Keluar</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $totalKeluar }}</h3>
            </div>
            <span class="text-xl">📤</span>
        </div>

        <!-- KOTAK 4: SELESAI DIARSIP -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-xs flex justify-between items-center">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Selesai Diarsip</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $totalArsip }}</h3>
            </div>
            <span class="text-xl">✓</span>
        </div>
    </div>

    <!-- 2. TABEL DAFTAR TINDAK LANJUT NASKAH TERKINI -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-semibold text-slate-800">Daftar Tindak Lanjut Naskah Terkini</h3>
            <a href="{{ route('naskah-masuk.index') }}" class="text-xs font-semibold text-sky-600 hover:text-sky-700 transition-colors">Lihat Semua</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3">No. Naskah</th>
                        <th class="px-6 py-3">Perihal</th>
                        <th class="px-6 py-3">Asal Pengirim</th>
                        <th class="px-6 py-3">Status Tindakan</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                    @if($naskahTerkini->isEmpty())
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400 bg-slate-50/10 font-medium">
                                ✨ Luar biasa! Seluruh naskah masuk terkini sudah selesai ditindaklanjuti.
                            </td>
                        </tr>
                    @else
                        @foreach($naskahTerkini as $index => $surat)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ $surat->no_surat }}</td>
                                <td class="px-6 py-4 font-medium text-slate-800">{{ $surat->perihal }}</td>
                                <td class="px-6 py-4 text-slate-500 font-semibold">{{ $surat->asal_pengirim }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded-sm font-bold text-[10px] uppercase {{ $surat->status_tindakan == 'Selesai Disposisi' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        {{ $surat->status_tindakan }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="/tknd/naskah-masuk/detail/{{ $surat->id }}" class="px-3 py-1 bg-sky-50 text-sky-700 border border-sky-200 rounded-md hover:bg-sky-100 transition-colors font-bold text-[10px]">
                                        Buka
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>

            </table>
        </div>
    </div>
@endsection
