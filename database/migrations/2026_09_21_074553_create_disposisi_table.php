<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan perintah untuk membuat tabel disposisi.
     */
    public function up(): void
    {
        Schema::create('disposisi', function (Blueprint $table) {
            $table->id();
            
            // Relasi FK ke tabel naskah masuk. Jika surat dihapus, lembar disposisi otomatis ikut terhapus
            $table->foreignId('naskah_masuk_id')->constrained('naskah_masuk')->onDelete('cascade');
            
            $table->json('diteruskan_kepada'); // Menyimpan daftar wakasek dalam bentuk array JSON
            $table->date('batas_waktu'); // Tanggal deadline tugas
            $table->enum('sifat', ['biasa', 'penting', 'rahasia'])->default('biasa'); // Sifat arahan
            $table->text('catatan_instruksi'); // Memo arahan tertulis dari Kepala Sekolah
            
            $table->timestamps(); // Catatan waktu pembuatan untuk Audit Trail pimpinan
        });
    }

    /**
     * Batalkan pembuatan tabel jika terjadi rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposisi');
    }
};
