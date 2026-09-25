<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan perintah untuk membuat tabel naskah_masuk.
     */
    public function up(): void
    {
        Schema::create('naskah_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat')->unique(); // Nomor Naskah / Surat Resmi (Contoh: 400.3.1/112/Disdik)
            $table->string('asal_pengirim'); // Instansi Pengirim (Contoh: Dinas Pendidikan Provinsi)
            $table->date('tgl_surat'); // Tanggal yang tertera pada fisik surat
            $table->date('tgl_terima'); // Tanggal ketika surat diterima oleh sistem sekolah
            $table->text('perihal'); // Perihal / Isi ringkas naskah dinas
            $table->string('kode_klasifikasi'); // Kode Klasifikasi Permendagri 83/2022 (Contoh: 400.3.1)
            $table->string('file_pdf'); // Nama file dokumen PDF scan yang tersimpan di storage hosting
            $table->enum('status_disposisi', ['menunggu', 'proses', 'selesai'])->default('menunggu'); // Status siklus lembar disposisi
            $table->timestamps(); // Otomatis membuat kolom created_at & updated_at untuk pelacakan audit trail
        });
    }

    /**
     * Batalkan pembuatan tabel jika terjadi rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('naskah_masuk');
    }
};
