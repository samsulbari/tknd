<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Website Resmi Sekolah</title>
    
    <!-- MENGGUNAKAN CDN UTAMA YANG LEBIH STABIL -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- MENGGUNAKAN CDN ALPINE JS TERBARU -->
    <script defer src="https://jsdelivr.net"></script>
</head>

<body class="bg-slate-50 text-slate-800 antialiased font-sans">

    <!-- NAVBAR -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo & Nama Sekolah -->
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center space-x-2">
                        <span class="text-2xl">🏫</span>
                        <span class="font-bold text-xl text-slate-900 tracking-tight">SMAN 1 SMarT</span>
                    </div>
                    <!-- Menu Desktop -->
                    <div class="hidden md:ml-8 md:flex md:space-x-6 text-sm font-medium">
                        <a href="#profil" class="text-slate-600 hover:text-sky-600 py-2">Profil</a>
                        <a href="#berita" class="text-slate-600 hover:text-sky-600 py-2">Berita</a>
                        <a href="#informasi" class="text-slate-600 hover:text-sky-600 py-2">Informasi</a>
                        <a href="#galeri" class="text-slate-600 hover:text-sky-600 py-2">Galeri</a>
                    </div>
                </div>
                
                <!-- Tombol Login TKND (Kanan - Versi Desktop) -->
                <div class="hidden md:flex items-center">
                    <a href="/login" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 rounded-lg shadow-sm transition-colors cursor-pointer">
                        Masuk Sistem TKND →
                    </a>
                </div>

                <!-- Menu Mobile Panel (Versi HP) -->
                <div x-show="mobileMenuOpen" @click.outside="mobileMenuOpen = false" class="md:hidden border-t border-slate-200 bg-white px-4 pt-2 pb-4 space-y-1 shadow-inner">
                    <!-- ... link menu lainnya ... -->
                    <div class="pt-4 border-t border-slate-100">
                        <a href="/login" class="w-full flex items-center justify-center px-4 py-2.5 text-base font-medium text-white bg-sky-600 hover:bg-sky-700 rounded-lg shadow-sm cursor-pointer">
                            Masuk Sistem TKND
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- Menu Mobile Panel -->
        <div x-show="mobileMenuOpen" @click.outside="mobileMenuOpen = false" class="md:hidden border-t border-slate-200 bg-white px-4 pt-2 pb-4 space-y-1 shadow-inner">
            <a href="#profil" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-sky-600">Profil</a>
            <a href="#berita" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-sky-600">Berita</a>
            <a href="#informasi" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-sky-600">Informasi</a>
            <a href="#galeri" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-sky-600">Galeri</a>
            <div class="pt-4 border-t border-slate-100">
                <a href="/tknd" class="w-full flex items-center justify-center px-4 py-2.5 text-base font-medium text-white bg-sky-600 hover:bg-sky-700 rounded-lg shadow-sm">
                    Masuk Sistem TKND
                </a>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main>
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-slate-400 py-12 mt-20 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm">
            <p class="text-white font-semibold mb-2">SMAN 1 SMarT</p>
            <p>Mewujudkan Ekosistem Pendidikan Berbasis Digital yang Terintegrasi.</p>
            <p class="mt-6 text-xs text-slate-600">&copy; {{ date('Y') }} Platform Digital Sekolah. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

</body>
</html>
