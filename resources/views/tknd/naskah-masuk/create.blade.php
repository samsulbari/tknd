@extends('layouts.tknd')

@section('title', 'Registrasi Naskah Masuk')
@section('page_title', 'Registrasi Naskah Masuk Baru')

@section('content')
    <!-- NAVIGASI KEMBALI -->
    <div class="mb-6">
        <a href="{{ route('naskah-masuk.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-sky-600 transition-colors">
            ← Kembali ke Daftar Naskah Masuk
        </a>
    </div>

    <!-- WIDGET PELACAK EROR VALIDASI SERVER (TAMBAHKAN INI) -->
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-xs">
            <div class="flex items-center mb-2">
                <span class="text-red-600 font-bold mr-2">⚠️ Pendaftaran Gagal!</span>
                <span class="text-xs text-red-500 font-medium">Periksa kembali data masukan Anda:</span>
            </div>
            <ul class="list-disc list-inside text-xs text-red-700 space-y-1 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- FORM UTAMA DENGAN KEAMANAN CSRF & ENCTYPE MULTIPART -->
    <form action="{{ route('naskah-masuk.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- KOLOM KIRI & TENGAH (2/3 Halaman): INFORMASI UTAMA SURAT MASUK -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-5">📋 Informasi Naskah / Surat Masuk</h3>
                    
                    <div class="space-y-4">
                        <!-- Asal Pengirim -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Asal Pengirim Instansi / Surat <span class="text-red-500">*</span></label>
                            <input type="text" name="asal_pengirim" placeholder="Contoh: Dinas Pendidikan Provinsi" required
                                   class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm text-slate-700 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none transition-all">
                        </div>

                        <!-- Nomor Surat & Tanggal Surat -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Nomor Naskah / Surat Masuk <span class="text-red-500">*</span></label>
                                <input type="text" name="no_surat" placeholder="Contoh: 400.3.1/112/Disdik" required
                                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm text-slate-700 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Tertera pada Surat <span class="text-red-500">*</span></label>
                                <input type="date" name="tgl_surat" required
                                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm text-slate-700 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none transition-all">
                            </div>
                        </div>

                        <!-- Perihal / Isi Ringkas -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Perihal / Isi Ringkas Surat Masuk <span class="text-red-500">*</span></label>
                            <textarea name="perihal" rows="5" placeholder="Tuliskan perihal utama surat masuk secara jelas..." required
                                      class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm text-slate-700 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none transition-all"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN (1/3 Halaman): TATA KELOLA & AREA UNGHAH BERKAS -->
            <div class="space-y-6">
                
                <!-- Aturan Klasifikasi & Tanggal Terima -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-base font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-4">⚙️ Tata Kelola Sistem</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Diterima Sistem <span class="text-red-500">*</span></label>
                            <input type="date" value="{{ date('Y-m-d') }}" readonly
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-500 outline-none cursor-not-allowed">
                        </div>
                        
                        <!-- Dropdown Pencarian Klasifikasi Cerdas Alpine.js -->
                        <div x-data="klasifikasiArsip()">
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Kode Klasifikasi Arsip (Permendagri 83/2022) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative mb-2">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs text-slate-400">🔍</span>
                                <input type="text" x-model="search" placeholder="Ketik kata kunci urusan..." 
                                       class="w-full pl-8 pr-3 py-1.5 border border-slate-200 rounded-lg text-xs text-slate-700 bg-slate-50 focus:bg-white focus:ring-1 focus:ring-sky-500 outline-none transition-all">
                            </div>
                            <select required name="kode_klasifikasi" 
                                    class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm text-slate-600 bg-white focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none transition-all">
                                <option value="">-- Pilih Kode Klasifikasi --</option>
                                <template x-for="item in filteredItems()" :key="item.kode">
                                    <option :value="item.kode" x-text="item.kode + ' - ' + item.nama"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Area Upload Berkas PDF Digital dengan Validasi Mandiri -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm" 
                     x-data="{ 
                        fileName: '', 
                        errorMessage: '', 
                        validateFile(e) {
                            const file = e.target.files[0];
                            this.errorMessage = '';
                            this.fileName = '';
                            if (!file) return;
                            if (file.type !== 'application/pdf') {
                                this.errorMessage = '❌ Format berkas salah! Wajib dokumen PDF.';
                                e.target.value = '';
                                return;
                            }
                            const maxSize = 5 * 1024 * 1024;
                            if (file.size > maxSize) {
                                this.errorMessage = '❌ Ukuran berkas terlalu besar! Maksimal 5MB.';
                                e.target.value = '';
                                return;
                            }
                            this.fileName = file.name;
                        }
                     }">
                    <h3 class="text-base font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-4">📄 Berkas Digital (Scan Surat Masuk)</h3>
                    <label class="border-2 border-dashed border-slate-200 hover:border-sky-500 rounded-xl p-6 text-center cursor-pointer transition-colors bg-slate-50/50 block relative">
                        <span class="text-3xl block mb-2">📥</span>
                        <span class="block text-sm font-medium text-slate-700 mb-0.5" x-text="fileName ? 'Berkas Siap' : 'Unggah Berkas Naskah'"></span>
                        <span class="block text-xs text-slate-400" x-text="fileName ? fileName : 'Hanya menerima format berkas PDF (Maks. 5MB)'"></span>
                        <input type="file" name="file_pdf" accept=".pdf" class="hidden" id="file_pdf_surat" @change="validateFile" required>
                    </label>
                    <div x-show="errorMessage" x-text="errorMessage" class="mt-3 p-2.5 bg-red-50 text-red-700 text-xs rounded-lg border border-red-100 font-medium" style="display: none;"></div>
                </div>

                <!-- Tombol Simpan Dokumen -->
                <div class="pt-2">
                    <button type="submit" class="w-full py-3 px-4 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-sm rounded-xl shadow-md transition-all text-center cursor-pointer">
                        💾 Simpan & Daftarkan Naskah Masuk
                    </button>
                </div>

            </div>
        </div>
    </form>
@endsection

<!-- SKRIP ALPINE DATA KODE KLASIFIKASI -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('klasifikasiArsip', () => ({
        search: '',
        items: [],

        init() {
            fetch('/js/klasifikasi.json')
                .then(response => response.json())
                .then(data => {
                    this.items = data;
                })
                .catch(error => console.error('Gagal mengambil kode klasifikasi:', error));
        },

        filteredItems() {
            if (this.search.trim() === '') {
                return this.items;
            }
            return this.items.filter(item => 
                item.kode.toLowerCase().includes(this.search.toLowerCase()) || 
                item.nama.toLowerCase().includes(this.search.toLowerCase())
            );
        }
    }));
});
</script>
