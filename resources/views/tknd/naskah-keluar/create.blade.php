@extends('layouts.tknd')

@section('title', 'Buat Konsep Baru')
@section('page_title', 'Registrasi Pengajuan Konsep Naskah Keluar')

@section('content')
    <div class="mb-6">
        <a href="{{ route('naskah-keluar.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-sky-600 transition-colors">
            ← Kembali ke Daftar Konsep
        </a>
    </div>

    <div class="max-w-2xl bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <h3 class="text-base font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-5">📝 Draf Rincian Surat Keluar</h3>
        
        <form action="{{ route('naskah-keluar.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            
            <!-- Tujuan Surat -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Tujuan Pengiriman Surat / Instansi Penerima <span class="text-red-500">*</span></label>
                <input type="text" name="tujuan_surat" required placeholder="Contoh: Kepala Dinas Pendidikan Provinsi / Orang Tua Siswa Kelas XII"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm text-slate-700 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none transition-all">
            </div>

            <!-- Kode Klasifikasi Arsip -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kode Klasifikasi Permendagri <span class="text-red-500">*</span></label>
                <input type="text" name="kode_klasifikasi" required placeholder="Contoh: 421.3 (Pendidikan)"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm text-slate-700 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none transition-all">
            </div>

            <!-- Sifat Surat -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Sifat Dokumen</label>
                <div class="flex gap-5">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="sifat" value="biasa" checked class="w-4 h-4 text-sky-600 border-slate-300 focus:ring-sky-500">
                        <span class="ml-2 text-sm text-slate-700">Biasa</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="sifat" value="penting" class="w-4 h-4 text-amber-600 border-slate-300 focus:ring-amber-500">
                        <span class="ml-2 text-sm text-slate-700">Penting</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="sifat" value="rahasia" class="w-4 h-4 text-red-600 border-slate-300 focus:ring-red-500">
                        <span class="ml-2 text-sm text-slate-700">Rahasia</span>
                    </label>
                </div>
            </div>

            <!-- Perihal Isi Surat -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Perihal / Inti Undangan Dinas <span class="text-red-500">*</span></label>
                <textarea name="perihal" rows="3" required placeholder="Contoh: Undangan Rapat Koordinasi Penyusunan Anggaran Ujian Sekolah Semester Genap."
                          class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm text-slate-700 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none transition-all"></textarea>
            </div>

            <!-- Unggah Berkas Mentah -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Unggah Draf Berkas Mentah (Opsional)</label>
                <input type="file" name="file_draft_mentah" accept=".pdf,.doc,.docx"
                       class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 border border-slate-200 rounded-lg p-1">
                <span class="block text-[11px] text-slate-400 mt-1">Format dokumen yang didukung: PDF, DOC, atau DOCX (Maksimal 5MB).</span>
            </div>

            <!-- Tombol Simpan -->
            <div class="pt-3 flex justify-end space-x-3 border-t border-slate-100">
                <a href="{{ route('naskah-keluar.index') }}" class="px-4 py-2 text-sm font-medium text-slate-500 hover:bg-slate-100 rounded-lg transition-colors text-center block">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-sm rounded-lg shadow-sm transition-all cursor-pointer">
                    💾 Simpan & Ajukan Konsep
                </button>
            </div>
        </form>
    </div>
@endsection
