@extends('layouts.public')

@section('title', 'Selamat Datang')

@section('content')
    <!-- HERO SECTION -->
    <section class="bg-white border-b border-slate-200 py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 mb-4 border border-sky-200">
                    🚀 Selamat Datang di Era Baru
                </span>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Satu Platform Utama Untuk Layanan <span class="text-sky-600">Digital Sekolah</span>
                </h1>
                <p class="mt-4 text-lg text-slate-600 leading-relaxed">
                    Akses informasi sekolah secara transparan serta kelola seluruh urusan administrasi dan tata kelola naskah dinas kedinasan secara terstruktur dan aman dalam satu ekosistem terintegrasi.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="/login" class="px-6 py-3 font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-xl shadow-md transition-all cursor-pointer">
                        Ruang Kerja TKND Internal
                    </a>
                    <a href="#profil" class="px-6 py-3 font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all">
                        Pelajari Selengkapnya ↓
                    </a>
                </div>

            </div>
            <div class="hidden lg:block bg-slate-100 h-80 rounded-2xl border border-slate-200 flex items-center justify-center text-slate-400 font-medium shadow-inner">
                [ Ilustrasi / Foto Banner Utama Sekolah ]
            </div>
        </div>
    </section>

    <!-- SECTION 1: PROFIL SEKOLAH -->
    <section id="profil" class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-3xl font-bold text-slate-900">Profil Sekolah</h2>
            <div class="h-1 w-12 bg-sky-600 mx-auto mt-3 rounded-full"></div>
            <p class="mt-3 text-slate-600">Berkomitmen tinggi dalam mencetak generasi unggul yang melek teknologi dan siap berkompetisi.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="text-xl font-bold text-slate-900 mb-3">👁️ Visi Sekolah</h3>
                <p class="text-slate-600 leading-relaxed">Menjadi lembaga pendidikan kejuruan unggulan yang menghasilkan lulusan berkarakter, kompeten, berdaya saing global, dan menguasai teknologi digital pada era modern.</p>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="text-xl font-bold text-slate-900 mb-3">🎯 Misi Sekolah</h3>
                <ul class="text-slate-600 space-y-2 list-disc list-inside">
                    <li>Menyelenggarakan pembelajaran berbasis industri dan digital.</li>
                    <li>Menanamkan budi pekerti luhur dan nilai kedisiplinan kerja.</li>
                    <li>Mengembangkan tata kelola sekolah yang transparan dan akuntabel.</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- SECTION 2: BERITA TERKINI -->
    <section id="berita" class="py-16 bg-slate-100/60 border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900">Berita & Kegiatan</h2>
                    <p class="mt-2 text-slate-600">Ikuti terus berita terbaru dan update agenda resmi dari sekolah kami.</p>
                </div>
                <button class="text-sm font-semibold text-sky-600 hover:text-sky-700 hidden sm:block">Lihat Semua Berita →</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card Berita 1 -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                    <div class="h-48 bg-slate-200 flex items-center justify-center text-slate-400 text-sm font-medium">[ Thumbnail Foto ]</div>
                    <div class="p-5 flex-1 flex flex-col">
                        <span class="text-xs font-semibold text-sky-600 uppercase tracking-wider">Prestasi</span>
                        <h4 class="font-bold text-slate-900 mt-2 text-lg leading-snug hover:text-sky-600 transition-colors">
                            <a href="#">Siswa Jurusan RPL Raih Juara 1 LKS Tingkat Provinsi Tahun Ini</a>
                        </h4>
                        <p class="text-slate-500 text-sm mt-2 line-clamp-3">Selamat atas capaian luar biasa tim Web Technologies SMK dalam ajang bergengsi kompetisi keahlian nasional...</p>
                        <span class="text-xs text-slate-400 mt-5 block pt-3 border-t border-slate-100">12 September 2026</span>
                    </div>
                </div>
                <!-- Card Berita 2 -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                    <div class="h-48 bg-slate-200 flex items-center justify-center text-slate-400 text-sm font-medium">[ Thumbnail Foto ]</div>
                    <div class="p-5 flex-1 flex flex-col">
                        <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Pengumuman</span>
                        <h4 class="font-bold text-slate-900 mt-2 text-lg leading-snug hover:text-sky-600 transition-colors">
                            <a href="#">Implementasi E-Office TKND Mulai Resmi Diterapkan Sekolah</a>
                        </h4>
                        <p class="text-slate-500 text-sm mt-2 line-clamp-3">Langkah awal transformasi efisiensi digital, persuratan dinas kini beralih sepenuhnya via web internal /tknd...</p>
                        <span class="text-xs text-slate-400 mt-5 block pt-3 border-t border-slate-100">10 September 2026</span>
                    </div>
                </div>
                <!-- Card Berita 3 -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                    <div class="h-48 bg-slate-200 flex items-center justify-center text-slate-400 text-sm font-medium">[ Thumbnail Foto ]</div>
                    <div class="p-5 flex-1 flex flex-col">
                        <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Kegiatan</span>
                        <h4 class="font-bold text-slate-900 mt-2 text-lg leading-snug hover:text-sky-600 transition-colors">
                            <a href="#">Pelaksanaan Workshop Link and Match Bersama Dunia Industri</a>
                        </h4>
                        <p class="text-slate-500 text-sm mt-2 line-clamp-3">Sinkronisasi kurikulum sekolah merdeka bersama para praktisi dan CEO teknologi terkemuka asal ibu kota...</p>
                        <span class="text-xs text-slate-400 mt-5 block pt-3 border-t border-slate-100">08 September 2026</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 3: INFORMASI & GALERI -->
    <section id="informasi" class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Informasi (2 Kolom) -->
        <div class="lg:col-span-2">
            <h3 class="text-2xl font-bold text-slate-900 mb-6">📢 Pengumuman & Informasi Terbaru</h3>
            <div class="space-y-4">
                <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-start space-x-4">
                    <div class="p-2 bg-amber-50 text-amber-700 rounded-lg text-lg font-bold">📅</div>
                    <div>
                        <h5 class="font-semibold text-slate-900">Jadwal Penilaian Tengah Semester Ganjil 2026</h5>
                        <p class="text-sm text-slate-500 mt-1">Ujian berbasis komputer (CBT) dilaksanakan mulai tanggal 21 - 26 September secara bertahap.</p>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-start space-x-4">
                    <div class="p-2 bg-blue-50 text-blue-700 rounded-lg text-lg font-bold">📂</div>
                    <div>
                        <h5 class="font-semibold text-slate-900">Unduh Panduan Pendaftaran Ekstrakurikuler Wajib</h5>
                        <p class="text-sm text-slate-500 mt-1">Bagi seluruh peserta didik baru kelas X, berkas dokumen administrasi dapat diakses secara bebas.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Galeri Mini (1 Kolom) -->
        <div id="galeri">
            <h3 class="text-2xl font-bold text-slate-900 mb-6">📸 Dokumentasi Galeri</h3>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-slate-200 aspect-square rounded-xl flex items-center justify-center text-xs text-slate-400 font-medium">[ Foto ]</div>
                <div class="bg-slate-200 aspect-square rounded-xl flex items-center justify-center text-xs text-slate-400 font-medium">[ Foto ]</div>
                <div class="bg-slate-200 aspect-square rounded-xl flex items-center justify-center text-xs text-slate-400 font-medium">[ Foto ]</div>
                <div class="bg-slate-200 aspect-square rounded-xl flex items-center justify-center text-xs text-slate-400 font-medium">[ Foto ]</div>
            </div>
        </div>
    </section>
@endsection
