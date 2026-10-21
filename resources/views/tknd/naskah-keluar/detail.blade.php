@extends('layouts.tknd')

@section('title', 'Pantau Naskah Keluar')
@section('page_title', 'Lembar Monitoring Alur Surat Keluar')

@section('content')
    <div class="mb-6">
        <a href="{{ route('naskah-keluar.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-sky-600 transition-colors">
            ← Kembali ke Daftar Naskah
        </a>
    </div>
    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-semibold text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-xs font-semibold text-red-800">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-800">
            <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <!-- GRID UTAMA -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- KOLOM KIRI (2/3): INFORMASI RINCIAN DOKUMEN -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-base font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-4">📋 Rincian Draf Naskah Keluar</h3>
                
                <div class="space-y-4 text-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase">Tujuan Pengiriman</span>
                            <span class="font-bold text-slate-800 block mt-0.5">🏢 {{ $naskah->tujuan_surat }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase">Nomor Agenda Keluar</span>
                            <span class="font-mono font-bold text-xs block mt-0.5 {{ $naskah->nomor_agenda ? 'text-slate-900' : 'text-slate-400 italic' }}">
                                {{ $naskah->nomor_agenda ?? 'Terbit saat registrasi TU' }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase">Nomor Resmi Dinas</span>
                            <span class="font-mono font-bold text-xs block mt-0.5 {{ $naskah->no_surat_resmi ? 'text-slate-900' : 'text-slate-400 italic' }}">
                                {{ $naskah->no_surat_resmi ?? 'Belum Diterbitkan (Menunggu ACC)' }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-3">
                        <span class="block text-xs font-semibold text-slate-400 uppercase">Perihal / Pokok Isi Surat</span>
                        <p class="text-slate-700 font-medium leading-relaxed bg-slate-50 p-3 rounded-lg border border-slate-200 mt-1">
                            {{ $naskah->perihal }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-3 text-xs">
                        <div>
                            <span class="block font-semibold text-slate-400 uppercase">Kode Klasifikasi</span>
                            <span class="inline-block mt-1 px-2 py-0.5 bg-slate-100 text-slate-700 rounded font-mono font-medium">{{ $naskah->kode_klasifikasi }}</span>
                        </div>
                        <div>
                            <span class="block font-semibold text-slate-400 uppercase">Sifat Dokumen</span>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded font-bold uppercase tracking-wider
                                {{ $naskah->sifat == 'biasa' ? 'bg-slate-100 text-slate-700' : ($naskah->sifat == 'penting' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                ● {{ $naskah->sifat }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN (1/3): WORKFLOW AKSI & STATUS VERIFIKASI -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-base font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-4">⚙️ Status & Aksi Alur</h3>
                
                <div class="space-y-4">
                    @php
                        $pengguna = auth()->user();
                        $tahap = $naskah->status_alur;
                    @endphp

                    <!-- Tampilan Status Saat Ini -->
                    <div class="p-3 rounded-lg text-center border text-xs font-semibold
                        @if($tahap == 'konsep') bg-slate-50 text-slate-700 border-slate-200
                        @elseif($tahap == 'pemeriksaan_tu') bg-amber-50 text-amber-800 border-amber-200
                        @elseif($tahap == 'persetujuan_pimpinan') bg-purple-50 text-purple-800 border-purple-200
                        @elseif($tahap == 'penomoran') bg-sky-50 text-sky-800 border-sky-200
                        @else bg-emerald-50 text-emerald-800 border-emerald-200
                        @endif">
                        Tahapan:
                        @switch($tahap)
                            @case('konsep') Penyusunan Konsep @break
                            @case('pemeriksaan_tu') Pemeriksaan Ka. TU @break
                            @case('persetujuan_pimpinan') Persetujuan Kepala Sekolah @break
                            @case('penomoran') Penomoran & Agenda TU @break
                            @default Selesai & Diarsip
                        @endswitch
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        @if($tahap == 'konsep')
                            @if($pengguna->isKonseptorNaskahKeluar())
                                <form action="{{ route('naskah-keluar.proses-alur', $naskah->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="tindakan" value="ajukan_tu">
                                    <button type="submit" class="w-full py-2 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs rounded-lg transition-colors cursor-pointer text-center block">
                                        Kirim ke Kepala TU
                                    </button>
                                </form>
                                <p class="text-[11px] text-slate-400 mt-2 text-center">Ajukan draf agar diperiksa Kepala Tata Usaha.</p>
                            @else
                                <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-center text-xs text-slate-400">
                                    Menunggu Operator Layanan atau Pengadministrasi mengajukan konsep ke Kepala TU.
                                </div>
                            @endif

                        @elseif($tahap == 'pemeriksaan_tu')
                            @if($pengguna->role == 'kepala_tu')
                                <form action="{{ route('naskah-keluar.proses-alur', $naskah->id) }}" method="POST" class="space-y-2">
                                    @csrf
                                    <input type="hidden" name="tindakan" value="setuju_tu">
                                    <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-lg transition-colors cursor-pointer text-center block">
                                        Verifikasi & Teruskan ke Kepsek
                                    </button>
                                </form>
                                <form action="{{ route('naskah-keluar.proses-alur', $naskah->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="tindakan" value="kembalikan_konsep">
                                    <button type="submit" class="w-full py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-lg transition-colors cursor-pointer text-center block">
                                        Kembalikan ke Konsep
                                    </button>
                                </form>
                            @else
                                <div class="p-3 bg-amber-50 text-amber-800 border border-amber-200 rounded-lg text-center text-xs">
                                    Sedang diperiksa Kepala TU.
                                </div>
                            @endif

                        @elseif($tahap == 'persetujuan_pimpinan')
                            @if($pengguna->role == 'kepala_sekolah')
                                <form action="{{ route('naskah-keluar.proses-alur', $naskah->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="tindakan" value="setuju_pimpinan">
                                    <button type="submit" class="w-full py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs rounded-lg transition-colors cursor-pointer text-center block">
                                        Setujui Naskah
                                    </button>
                                </form>
                                <p class="text-[11px] text-slate-400 mt-2 text-center">Setelah disetujui, Tata Usaha memberikan nomor agenda dan nomor surat resmi.</p>
                            @else
                                <div class="p-3 bg-purple-50 text-purple-800 border border-purple-200 rounded-lg text-center text-xs">
                                    Menunggu persetujuan Kepala Sekolah.
                                </div>
                            @endif

                        @elseif($tahap == 'penomoran')
                            @if($pengguna->isPenomoranNaskahKeluar())
                                <form action="{{ route('naskah-keluar.proses-alur', $naskah->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="tindakan" value="registrasi_nomor">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Nomor Surat Resmi Keluar:</label>
                                        <input type="text" name="no_surat_resmi" required placeholder="Contoh: 421.3/024/SMAN1-SMART/2026"
                                               class="w-full px-2.5 py-1.5 border border-slate-200 rounded-md text-xs text-slate-700 outline-none focus:border-sky-500">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Unggah PDF Surat Resmi:</label>
                                        <input type="file" name="file_pdf_final" accept=".pdf" required
                                               class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-semibold file:bg-sky-50 file:text-sky-700 border border-slate-200 rounded-md p-1">
                                    </div>
                                    <button type="submit" class="w-full py-2 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs rounded-lg transition-colors cursor-pointer text-center block">
                                        Registrasi Agenda & Arsipkan
                                    </button>
                                </form>
                                <p class="text-[11px] text-slate-400 mt-2 text-center">Nomor agenda keluar diisi otomatis saat registrasi.</p>
                            @else
                                <div class="p-3 bg-sky-50 text-sky-800 border border-sky-200 rounded-lg text-center text-xs">
                                    Menunggu Kepala TU atau Pengadministrasi menomori dan mengarsipkan.
                                </div>
                            @endif

                        @else
                            <div class="bg-emerald-50 p-4 rounded-lg border border-emerald-200 text-center text-xs text-emerald-800 font-medium">
                                Naskah keluar telah diregistrasi dan diarsip.
                                @if($naskah->nomor_agenda)
                                    <span class="block mt-1 font-mono">Agenda: {{ $naskah->nomor_agenda }}</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Akses Unduh Draf -->
                    @if($naskah->file_draft_mentah)
                        <div class="pt-2">
                            <a href="{{ route('naskah-keluar.download', $naskah->file_draft_mentah) }}" 
                               class="w-full py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-xs rounded-lg transition-colors text-center block">
                                📥 Unduh File Draf Mentah
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection
