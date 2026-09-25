<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan perintah untuk membuat tabel users.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            
            // PERBAIKAN: Mengunci 5 Pilihan Jabatan Resmi Langsung di Tabel Utama
            $table->enum('role', [
                'kepala_sekolah', 
                'kepala_tu', 
                'pengadministrasi_perkantoran', 
                'operator_layanan', 
                'guru'
            ])->default('pengadministrasi_perkantoran');

            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Batalkan pembuatan tabel jika terjadi rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
