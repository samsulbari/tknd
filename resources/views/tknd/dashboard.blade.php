@extends('layouts.tknd')

@section('title', 'Dashboard')
@section('page_title', 'Ringkasan Kerja & Dashboard')

@section('content')
    @if(session('success') || session('sukses'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-semibold text-emerald-800">{{ session('success') ?? session('sukses') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-xs font-semibold text-red-800">{{ session('error') }}</div>
    @endif
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
        
        <!-- TABEL TINDAK LANJUT BERSIH & PRESISI -->
        <div class="overflow-x-auto border border-slate-100 rounded-lg">
            <table class="w-full text-left border-collapse table-fixed sm:table-auto">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-6 py-3.5 w-32">NO. NASKAH</th>
                        <th class="px-6 py-3.5 w-64">PERIHAL</th>
                        <th class="px-6 py-3.5 w-52">ASAL PENGIRIM</th>
                        <th class="px-6 py-3.5 w-44 text-center">STATUS TINDAKAN</th>
                        <th class="px-6 py-3.5 w-36 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                    @if($naskahTerkini->isEmpty())
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400 bg-slate-50/20 font-medium">
                                📭 Belum ada data tindak lanjut naskah terkini.
                            </td>
                        </tr>
                    @else
                        @foreach($naskahTerkini as $row)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                
                                <!-- 1. NO. NASKAH (Menampilkan nomor surat masuk) -->
                                <td class="px-6 py-4 font-mono text-slate-900 break-words whitespace-normal leading-normal">
                                    <span class="block font-bold">{{ $row->nomor_agenda ?? '—' }}</span>
                                    <span class="block text-[11px] text-slate-500 mt-0.5">{{ $row->no_surat }}</span>
                                </td>
                                
                                <!-- 2. PERIHAL -->
                                <td class="px-6 py-4 font-semibold text-slate-800 break-words whitespace-normal leading-normal">
                                    {{ $row->perihal }}
                                </td>
                                
                                <!-- 3. ASAL PENGIRIM -->
                                <td class="px-6 py-4 text-sky-600 font-medium break-words whitespace-normal leading-normal">
                                    🏢 {{ $row->asal_pengirim }}
                                </td>
                                
                                <!-- 4. STATUS TINDAKAN -->
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold 
                                        {{ $row->status_tindakan == 'Selesai Disposisi' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                        {{ $row->status_tindakan }}
                                    </span>
                                </td>
                                
                                <!-- 5. 🔑 KUNCI PERBAIKAN: Tombol Aksi Dinamis Terkunci Aman Hanya di Kolom Paling Kanan -->
                                <td class="px-6 py-4 text-right whitespace-nowrap font-medium text-xs">
                                    @if(in_array($row->status_tindakan, ['Selesai Disposisi', 'Dalam Proses']))
                                        @php
                                            // Ambil data disposisi untuk mengecek status pengerjaan guru/waka
                                            $disposisiTerbit = \DB::table('disposisi')->where('naskah_masuk_id', $row->id)->first();
                                        @endphp

                                        @if($disposisiTerbit)
                                            @if($disposisiTerbit->status_tugas == 'proses' && \App\Support\NomorAgenda::userDitugaskan($disposisiTerbit, (int) auth()->id()))
                                                <a href="/tknd/tugas-disposisi/lapor/{{ $disposisiTerbit->id }}" class="inline-block bg-sky-50 text-sky-700 hover:bg-sky-100 font-bold px-3 py-1.5 rounded-lg border border-sky-200 transition-colors cursor-pointer">
                                                    Lapor Tugas
                                                </a>
                                            @else
                                                <!-- Tombol Detail Jika Sudah Dilaporkan (Selesai) -->
                                                <a href="/tknd/naskah-masuk/detail/{{ $row->id }}" class="text-slate-500 hover:text-slate-700 font-semibold transition-colors">
                                                    Detail
                                                </a>
                                            @endif
                                        @else
                                            <!-- Jika data di seeder terputus -->
                                            <a href="/tknd/naskah-masuk/detail/{{ $row->id }}" class="text-slate-500 hover:text-slate-700 font-semibold transition-colors">Detail</a>
                                        @endif
                                    @else
                                        <span class="text-slate-400 italic">Menunggu Pimpinan</span>
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
