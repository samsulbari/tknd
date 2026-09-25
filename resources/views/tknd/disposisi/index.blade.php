@extends('layouts.tknd')

@section('title', 'Riwayat Disposisi')
@section('page_title', 'Catatan Disposisi Resmi')

@section('content')
<div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
    
    <!-- Bagian Header Ringkas -->
    <div class="border-b border-slate-100 pb-4 mb-6">
        <h3 class="text-base font-semibold text-slate-900">⇄ Riwayat Transaksi Disposisi Pimpinan</h3>
        <p class="text-xs text-slate-400 mt-0.5">Daftar seluruh instruksi naskah masuk yang didistribusikan oleh Kepala Sekolah kepada para Guru.</p>
    </div>

    <!-- Tabel Catatan Data Log -->
    <div class="overflow-x-auto border border-slate-100 rounded-lg">
        <table class="w-full text-left border-collapse table-fixed sm:table-auto">
            <thead>
                <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="px-6 py-3.5 w-44">Nomor Surat</th>
                    <th class="px-6 py-3.5 w-60">Perihal / Asal Surat</th>
                    <th class="px-6 py-3.5 w-36">Sifat Instruksi</th>
                    <th class="px-6 py-3.5 w-56">Diteruskan Kepada</th> <!-- Kolom Baru Perbaikan UX -->
                    <th class="px-6 py-3.5">Petunjuk Pimpinan</th>
                    <th class="px-6 py-3.5 w-28 text-center">Status Kerja</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                @if($dispositions->isEmpty())
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-400 bg-slate-50/20 font-medium">
                            📭 Belum ada rekaman lembar disposisi yang diterbitkan.
                        </td>
                    </tr>
                @else
                    @foreach($dispositions as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <!-- 1. Nomor Surat Masuk -->
                            <td class="px-6 py-4 font-mono font-bold text-slate-900 leading-normal break-words whitespace-normal">
                                {{ $item->no_surat }}
                            </td>
                            
                            <!-- 2. Perihal dan Instansi Pengirim -->
                            <td class="px-6 py-4 whitespace-normal">
                                <div class="font-medium text-slate-800 leading-normal">{{ $item->perihal }}</div>
                                <div class="text-[10px] text-slate-400 mt-1">Dari: {{ $item->asal_pengirim }}</div>
                            </td>
                            
                            <!-- 3. Badge Sifat Instruksi Pimpinan -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $instruksi = isset($item->sifat_instruksi) ? $item->sifat_instruksi : 'tindak_lanjuti';
                                @endphp
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] uppercase tracking-wide inline-block
                                    @if($instruksi == 'tindak_lanjuti') bg-sky-50 text-sky-700 border border-sky-200
                                    @elseif($instruksi == 'hadiri_wakili') bg-amber-50 text-amber-700 border border-amber-200
                                    @else bg-slate-50 text-slate-700 border border-slate-200
                                    @endif">
                                    @if($instruksi == 'tindak_lanjuti') 👉 Tindak Lanjuti
                                    @elseif($instruksi == 'hadiri_wakili') 📅 Hadiri / Wakili
                                    @elseif($instruksi == 'tanggapi_saran') ✍️ Tanggapi & Saran
                                    @elseif($instruksi == 'pedomani') 📌 Pedomani Tugas
                                    @else 📁 Arsipkan
                                    @endif
                                </span>
                            </td>

                            <!-- 4. PERBAIKAN LOGIKA UX: Menampilkan Daftar Jabatan Penerima Tugas -->
                            <td class="px-6 py-4 whitespace-normal space-y-1">
                                @php
                                    // Dekode JSON ID Penerima
                                    $ids = json_decode($item->guru_id_tujuan, true) ?? [];
                                @endphp
                                @if(count($ids) > 0)
                                    @foreach($ids as $id)
                                        @if(isset($allUsers[$id]))
                                            <span class="inline-block px-2 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded text-[10px] font-medium leading-tight">
                                                • {{ $allUsers[$id]->name }}
                                            </span>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="text-slate-400 italic">Tidak ada target</span>
                                @endif
                            </td>
                            
                            <!-- 5. Petunjuk Pimpinan -->
                            <td class="px-6 py-4 font-medium text-slate-600 whitespace-normal break-words leading-relaxed max-w-sm">
                                {{ $item->catatan_pimpinan ?? ($item->catatan ?? '-') }}
                            </td>
                            
                            <!-- 6. Badge Status Pelaksanaan Tugas Guru -->
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @php
                                    $status = isset($item->status_tugas) ? $item->status_tugas : 'proses';
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] uppercase tracking-wide
                                    {{ $status == 'selesai' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    @if($status == 'selesai') Slse / Arsip @else ⏳ Proses @endif
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

</div>
@endsection
