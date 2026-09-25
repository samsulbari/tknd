@extends('layouts.tknd')

@section('title', 'Profil Saya')
@section('page_title', 'Pengaturan Profil Pengguna')

@section('content')
    <!-- GRID UTAMA: KARTU IDENTITAS KIRI, FORM DETAIL KANAN -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- KOLOM KIRI: Ringkasan Akun & Status (1/3 Halaman) -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm text-center">
                <!-- Avatar Lingkaran Besar -->
                <div class="w-24 h-24 rounded-full bg-sky-100 flex items-center justify-center text-sky-700 font-bold text-3xl mx-auto border-2 border-sky-200 shadow-sm mb-4">
                    KS
                </div>
                <!-- Nama & Jabatan -->
                <h3 class="text-lg font-bold text-slate-900">Dr. H. Ahmad Fauzi, M.Pd.</h3>
                <p class="text-sm font-medium text-slate-500">Kepala Sekolah</p>
                
                <!-- Badge Role / Hak Akses -->
                <div class="mt-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                        🔑 Hak Akses: Pimpinan
                    </span>
                </div>

                <!-- Informasi Sistem Singkat -->
                <div class="mt-6 pt-6 border-t border-slate-100 text-left space-y-3 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-400">NIP / ID:</span>
                        <span class="font-mono text-slate-700 font-medium">19780816 200501 1 002</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Status Akun:</span>
                        <span class="text-emerald-600 font-semibold">● Aktif</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: Form Detail Akun & Log Aktivitas (2/3 Halaman) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Card Form Data Diri -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h4 class="text-base font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-5">ℹ️ Informasi Detail Akun</h4>
                
                <form action="#" method="POST" class="space-y-4">
                    <!-- Baris 1: Email & No. HP -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Alamat Email Instansi</label>
                            <input type="email" value="kepsek@smknegativedigital.sch.id" readonly
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-500 outline-none cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nomor WhatsApp Aktif</label>
                            <input type="text" value="081234567890" readonly
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-500 outline-none cursor-not-allowed">
                        </div>
                    </div>

                    <!-- Baris 2: Informasi Sistem Kewenangan -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tingkat Kewenangan Naskah Dinas</label>
                        <textarea rows="3" readonly
                                  class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-500 outline-none cursor-not-allowed">Otoritas Penuh: Penandatanganan Naskah Keluar Utama, Persetujuan Draf Konsep/Verifikasi Akhir, dan Pemberian Lembar Instruksi Disposisi Internal.</textarea>
                    </div>
                </form>
            </div>

            <!-- Card Audit Trail / Log Aktivitas Terakhir (Sesuai Konsep Dokumen) -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h4 class="text-base font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-4">⏱️ Log Aktivitas Akun Terakhir (Audit Trail)</h4>
                
                <div class="flow-root">
                    <ul class="-mb-8">
                        <!-- Log item 1 -->
                        <li>
                            <div class="relative pb-8">
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs">📝</span>
                                    </div>
                                    <div class="flex-1 min-w-0 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-slate-600">Memberikan instruksi disposisi pada naskah masuk <span class="font-medium text-slate-900">045.2/112/Disdik</span></p>
                                        </div>
                                        <div class="text-right text-xs whitespace-nowrap text-slate-400">
                                            <span>Hari ini, 09:15</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <!-- Log item 2 -->
                        <li>
                            <div class="relative pb-8">
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">🔓</span>
                                    </div>
                                    <div class="flex-1 min-w-0 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-slate-600">Berhasil masuk ke dalam sistem menggunakan otentikasi IP Lokal</p>
                                        </div>
                                        <div class="text-right text-xs whitespace-nowrap text-slate-400">
                                            <span>Hari ini, 08:02</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
@endsection
