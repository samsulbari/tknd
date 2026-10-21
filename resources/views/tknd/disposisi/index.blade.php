@extends('layouts.tknd')

@section('title', 'Riwayat Disposisi')
@section('page_title', 'Catatan Disposisi Resmi')

@section('content')
<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

    <div class="mb-6 border-b border-slate-100 pb-4">
        <h3 class="text-base font-semibold text-slate-900">
            Riwayat Transaksi Disposisi Pimpinan
        </h3>
        <p class="mt-0.5 text-xs text-slate-400">
            Daftar seluruh instruksi naskah masuk yang didistribusikan oleh Kepala Sekolah kepada para Guru.
        </p>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-200">
        <table class="w-full min-w-[1080px] table-fixed border-collapse text-left">
            <colgroup>
                <col class="w-[19%]">
                <col class="w-[22%]">
                <col class="w-[14%]">
                <col class="w-[18%]">
                <col class="w-[19%]">
                <col class="w-[8%]">
            </colgroup>

            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    <th class="px-4 py-3.5">Nomor Surat</th>
                    <th class="px-4 py-3.5">Perihal / Asal Surat</th>
                    <th class="px-4 py-3.5">Sifat Instruksi</th>
                    <th class="px-4 py-3.5">Diteruskan Kepada</th>
                    <th class="px-4 py-3.5">Petunjuk Pimpinan</th>
                    <th class="px-4 py-3.5 text-center">Status</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                @if($dispositions->isEmpty())
                    <tr>
                        <td colspan="6" class="bg-slate-50/20 px-6 py-10 text-center font-medium text-slate-400">
                            Belum ada rekaman lembar disposisi yang diterbitkan.
                        </td>
                    </tr>
                @else
                    @foreach($dispositions as $item)
                        <tr class="align-top transition-colors hover:bg-sky-50/40">
                            <td class="break-words px-4 py-4 font-mono text-[11px] font-bold leading-5 text-slate-900">
                                {{ $item->no_surat }}
                            </td>

                            <td class="px-4 py-4">
                                <div class="font-semibold leading-5 text-slate-800">
                                    {{ $item->perihal }}
                                </div>
                                <div class="mt-1 text-[11px] leading-4 text-slate-400">
                                    Dari: {{ $item->asal_pengirim }}
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                @php
                                    $instruksi = $item->sifat_instruksi ?? 'tindak_lanjuti';
                                @endphp

                                <span class="inline-block rounded border px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide
                                    @if($instruksi == 'tindak_lanjuti') bg-sky-50 text-sky-700 border-sky-200
                                    @elseif($instruksi == 'hadiri_wakili') bg-amber-50 text-amber-700 border-amber-200
                                    @else bg-slate-50 text-slate-700 border-slate-200
                                    @endif">
                                    @if($instruksi == 'tindak_lanjuti')
                                        Tindak Lanjuti
                                    @elseif($instruksi == 'hadiri_wakili')
                                        Hadiri / Wakili
                                    @elseif($instruksi == 'tanggapi_saran')
                                        Tanggapi &amp; Saran
                                    @elseif($instruksi == 'pedomani')
                                        Pedomani Tugas
                                    @else
                                        Arsipkan
                                    @endif
                                </span>
                            </td>

                            <td class="px-4 py-4">
                                @php
                                    $ids = json_decode($item->guru_id_tujuan, true) ?? [];
                                @endphp

                                @if(count($ids) > 0)
                                    @foreach($ids as $id)
                                        @if(isset($allUsers[$id]))
                                            <span class="mb-1 mr-1 inline-flex max-w-full items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-1 text-[10px] font-medium leading-tight text-slate-700">
                                                &bull; {{ $allUsers[$id]->name }}
                                            </span>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="italic text-slate-400">Tidak ada target</span>
                                @endif
                            </td>

                            <td class="break-words px-4 py-4 text-xs font-medium leading-5 text-slate-600">
                                {{ $item->catatan_pimpinan ?? ($item->catatan ?? '-') }}
                            </td>

                            <td class="px-4 py-4 text-center">
                                @php
                                    $status = $item->status_tugas ?? 'proses';
                                @endphp

                                <span class="rounded-full border px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide
                                    {{ $status == 'selesai'
                                        ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                        : 'border-amber-200 bg-amber-50 text-amber-700' }}">
                                    {{ $status == 'selesai' ? 'Selesai' : 'Proses' }}
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