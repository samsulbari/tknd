<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan perintah untuk membuat tabel naskah_keluar.
     */
    public function up(): void
    {
        Schema::create('naskah_keluar', function (Blueprint $table) {
            $table->id();
            $table->string('perihal'); // Perihal / Hal Utama Surat Keluar (Contoh: Undangan Rapat Pleno Komite)
            $table->string('tujuan_surat'); // Kepada Yth. Tujuan Surat (Contoh: Orang Tua / Wali Murid Kelas X)
            $table->text('pokok_isi'); // Poin-poin atau ringkasan isi naskah yang diajukan
            $table->string('kode_klasifikasi'); // Kode Klasifikasi Permendagri 83/2022 (Contoh: 400.3.5)
            $table->enum('sifat', ['biasa', 'penting', 'segera'])->default('biasa'); // Sifat naskah keluar
            $table->string('file_draft_mentah')->nullable(); // Nama file lampiran kasar (Word/PDF) sebelum ditandatangani
            $table->string('no_surat_resmi')->unique()->nullable(); // Nomor resmi dari tata usaha (diisi jika sudah disetujui & diberi nomor)
            $table->text('catatan_konseptor')->nullable(); // Catatan tambahan dari operator untuk pembuat verifikasi (Ka. TU)
            
            // Tahapan Alur Status sesuai konsep dokumen: 1. Konsep Draf, 2. Pemeriksaan TU, 3. Persetujuan Pimpinan, 4. Penomoran, 5. Selesai/Arsip
            $table->enum('status_alur', ['konsep', 'pemeriksaan_tu', 'persetujuan_pimpinan', 'penomoran', 'selesai'])->default('konsep');
            
            $table->timestamps(); // Pelacakan waktu untuk keperluan audit trail riwayat naskah keluar
        });
    }

    /**
     * Batalkan pembuatan tabel jika terjadi rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('naskah_keluar');
    }
};
