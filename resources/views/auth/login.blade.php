<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Platform Digital SMAN 1 SMarT.</title>
    
    <!-- MENGGUNAKAN VITE LOKAL (SUPER CEPAT) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">

    <!-- KONTAINER UTAMA -->
    <div class="sm:mx-auto w-full sm:max-w-md">
        <!-- Logo / Icon -->
        <div class="text-center text-4xl mb-2">🏫</div>
        <h2 class="text-center text-2xl font-bold tracking-tight text-slate-900">
            Sistem Tata Kelola Naskah Dinas
        </h2>
        <h3 class="mt-2 text-center text-2x1 font-bold text-slate-600">
            SMA Negeri 1 Sumber Marga Telang
        </h3>
    </div>

    <!-- WIDGET KOTAK LOGIN -->
    <div class="mt-8 sm:mx-auto w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-sm border border-slate-200 sm:rounded-xl sm:px-10">
            <!-- TEMPAT MENAMPILKAN PERINGATAN GAGAL LOGIN -->
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 rounded-r-lg text-xs font-semibold text-red-800">
                    ⚠️ {{ $errors->first() }}
                </div>
            @endif

            <form action="/login-proses" method="POST" class="space-y-4">
             @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Hak Akses Jabatan <span class="text-red-500">*</span></label>
                    <select name="role" required 
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm text-slate-700 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none transition-all bg-white cursor-pointer shadow-xs">
                        <option value="" disabled selected>-- Pilih Tingkat Jabatan --</option>
                        <option value="kepala_sekolah">✍️ 1. Kepala Sekolah (Pimpinan)</option>
                        <option value="kepala_tu">🔍 2. Kepala Tata Usaha (Ka. TU)</option>
                        <option value="pengadministrasi_perkantoran">💻 3. Pengadministrasi Perkantoran</option>
                        <option value="operator_layanan">⚙️ 4. Operator Layanan Operasional</option>
                        <option value="guru">🧑‍🏫 5. Guru / Tenaga Pendidik</option>
                    </select>
                </div>

                <!-- Masukan 1: Surat Elektronik (Email) / NIP -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Nama Pengguna (NIP / Email)</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="text" autocomplete="email" required placeholder="Contoh: operator@sekolah.sch.id"
                               class="block w-full px-3 py-2 border border-slate-200 rounded-lg text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition-all">
                    </div>
                </div>

                <!-- Masukan 2: Kata Sandi -->
                <div>
                    <div class="flex justify-between items-center">
                        <label for="password" class="block text-sm font-medium text-slate-700">Kata Sandi</label>
                        <a href="#" class="text-xs font-medium text-sky-600 hover:text-sky-500">Lupa kata sandi?</a>
                    </div>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="••••••••"
                               class="block w-full px-3 py-2 border border-slate-200 rounded-lg text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition-all">
                    </div>
                </div>

                <!-- Fitur Ingat Saya -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" 
                               class="h-4 w-4 text-sky-600 border-slate-300 rounded-sm focus:ring-sky-500">
                        <label for="remember-me" class="ml-2 block text-sm text-slate-700 cursor-pointer">Ingat sesi saya</label>
                    </div>
                </div>

                <button type="submit" class="w-full py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-sm rounded-lg shadow-md transition-all cursor-pointer mt-2">
                    🔒 Masuk ke Dashboard
                </button>

            </form>

            <!-- Informasi Bantuan / Footer Form -->
            <div class="mt-6 border-t border-slate-100 pt-4 text-center">
                <span class="text-xs text-slate-400">Hubungi Administrator jika akun Anda belum terdaftar.</span>
            </div>

        </div>
    </div>

</body>
</html>
