<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - TKND Platform Sekolah</title>
    
    <!-- HANYA PANGGIL VITE LOKAL (100% OFFLINE & INSTAN) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full" x-data="{ sidebarOpen: true }">

    <!-- Wrapper Utama -->
    <div class="flex min-h-screen">
        
        <!-- SIDEBAR NAVIGASI -->
        <aside class="bg-slate-900 text-slate-200 w-64 fixed inset-y-0 left-0 z-20 transform transition-transform duration-300 md:translate-x-0 md:relative"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-64'">
            
            <!-- Logo & Identitas -->
            <div class="h-16 flex items-center px-6 bg-slate-950 font-bold text-white border-b border-slate-800">
                <span class="text-sky-400 mr-2">■</span> TKND INTERNAL
            </div>

            <!-- Menu Navigasi Sidebar Samping (Dinamis & Terproteksi Hak Akses) -->
            <nav class="mt-4 px-4 space-y-1">
                
                <!-- 1. Menu Dashboard (Bisa diakses oleh semua jabatan/role yang sudah login) -->
                <a href="/tknd" 
                   class="flex items-center px-4 py-2.5 rounded-lg font-medium transition-colors cursor-pointer {{ Request::is('tknd') ? 'bg-sky-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <span class="mr-3 text-base">📊</span> Dashboard
                </a>
                
                @auth
                    <!-- PROTEKSI PERSURATAN: Hanya muncul untuk internal TU dan Pimpinan (Guru Disembunyikan) -->
                    @if(in_array(auth()->user()->role, ['kepala_sekolah', 'kepala_tu', 'pengadministrasi_perkantoran', 'operator_layanan']))
                        
                        <!-- 2. Menu Naskah Masuk -->
                        <a href="/tknd/naskah-masuk" 
                           class="flex items-center px-4 py-2.5 rounded-lg font-medium transition-colors cursor-pointer {{ Request::is('tknd/naskah-masuk*') ? 'bg-sky-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <span class="mr-3 text-base">📥</span> Naskah Masuk
                        </a>
                        
                        <!-- 3. Menu Disposisi -->
                        <a href="/tknd/disposisi" 
                           class="flex items-center px-4 py-2.5 rounded-lg font-medium transition-colors cursor-pointer {{ Request::is('tknd/disposisi*') ? 'bg-sky-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <span class="mr-3 text-base">⇄</span> Disposisi
                        </a>
                        
                        <!-- 4. Menu Naskah Keluar -->
                        <a href="/tknd/naskah-keluar" 
                           class="flex items-center px-4 py-2.5 rounded-lg font-medium transition-colors cursor-pointer {{ Request::is('tknd/naskah-keluar*') ? 'bg-sky-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <span class="mr-3 text-base">📤</span> Naskah Keluar
                        </a>
                        
                    @endif

                    <!-- PROTEKSI PUSAT ARSIP: Eksklusif Hanya Terbuka untuk Kepala Sekolah & Kepala TU saja -->
                    @if(in_array(auth()->user()->role, ['kepala_sekolah', 'kepala_tu', 'pengadministrasi_perkantoran']))
                        
                        <!-- 5. Menu Arsip & Laporan -->
                        <a href="/tknd/arsip" 
                           class="flex items-center px-4 py-2.5 rounded-lg font-medium transition-colors cursor-pointer {{ Request::is('tknd/arsip*') ? 'bg-sky-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <span class="mr-3 text-base">📁</span> Arsip & Laporan
                        </a>
                        
                    @endif
                @endauth

            </nav>

        </aside>

        <!-- AREA KONTEN KANAN -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- TOPBAR / HEADER -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 z-30 w-full">
                
                <!-- SISI KIRI: Tombol Hamburger & Judul Halaman -->
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-slate-700 md:hidden focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-lg font-semibold text-slate-800 ml-4 md:ml-0">@yield('page_title')</h1>
                </div>
                
                <!-- SISI KANAN: Informasi Akun & Pemicu Dropdown User Dinamis -->
                <div class="relative flex items-center" x-data="{ userMenuOpen: false }">
                    
                    @auth
                        <!-- Tombol Klik Profil Dinamis -->
                        <button type="button" 
                                @click="userMenuOpen = !userMenuOpen" 
                                class="flex items-center space-x-2 focus:outline-none py-1.5 px-2 rounded-lg hover:bg-slate-100/80 transition-all cursor-pointer">
                            
                            <!-- Inisial Otomatis Berdasarkan Karakter Pertama Nama Pengguna -->
                            <div class="w-8 h-8 rounded-full bg-sky-100 flex items-center justify-center text-sky-700 font-bold text-sm border border-sky-200 shadow-sm uppercase">
                                {{ substr(auth()->user()->name, 0, 2) }}
                            </div>
                            
                            <!-- Detail Nama & Singkatan Jabatan Kepegawaian Sekolah -->
                            <div class="hidden sm:flex flex-col text-left">
                                <span class="text-xs font-semibold text-slate-700 leading-tight">{{ auth()->user()->name }}</span>
                                <span class="text-[9px] font-bold text-sky-600 uppercase font-mono tracking-wider">
                                    @if(auth()->user()->role == 'kepala_sekolah') Kepala Sekolah
                                    @elseif(auth()->user()->role == 'kepala_tu') Kepala TU
                                    @elseif(auth()->user()->role == 'pengadministrasi_perkantoran') Staf Administrasi
                                    @elseif(auth()->user()->role == 'operator_layanan') Operator Layanan
                                    @elseif(auth()->user()->role == 'guru') Tenaga Pendidik / Guru
                                    @endif
                                </span>
                            </div>
                            <span class="text-xs text-slate-400">▼</span>
                        </button>
                    @else
                        <!-- Tampilan Cadangan Apabila Sesi Kosong Sebelum Redirect -->
                        <a href="/login" class="text-xs font-semibold text-sky-600 hover:text-sky-700">Silakan Login</a>
                    @endauth
                    
                    <!-- Kotak Menu Dropdown Aksi User -->
                    <div x-show="userMenuOpen" 
                        @click.outside="userMenuOpen = false"
                        x-transition
                        class="absolute right-0 top-full mt-2 w-48 bg-white border border-slate-200 rounded-lg shadow-xl py-1 text-sm text-slate-700 z-50 origin-top-right"
                        style="display: none;">
                        <a href="/tknd/profil" class="block px-4 py-2 hover:bg-slate-50 transition-colors">Profil Saya</a>
                        
                        <!-- JALUR KELUAR AMAN: Mengarah ke Rute Prosedur Penghancuran Token Sesi -->
                        <a href="{{ route('logout') }}" class="block px-4 py-2 hover:bg-slate-50 text-red-600 border-t border-slate-100 transition-colors font-semibold">
                            Keluar Sistem
                        </a>
                    </div>

                </div>
            </header>

            <!-- MAIN CONTENT AREA -->
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>

        </div>
    </div>

</body>
</html>
