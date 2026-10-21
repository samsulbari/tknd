<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('naskah_masuk', function (Blueprint $table) {
            $table->enum('klasifikasi_keamanan', ['biasa', 'terbatas', 'rahasia'])->default('biasa')->after('kode_klasifikasi');
        });

        Schema::table('naskah_keluar', function (Blueprint $table) {
            $table->enum('klasifikasi_keamanan', ['biasa', 'terbatas', 'rahasia'])->default('biasa')->after('kode_klasifikasi');
            $table->foreignId('konseptor_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->foreignId('diperiksa_oleh')->nullable()->after('konseptor_id')->constrained('users')->nullOnDelete();
            $table->timestamp('diperiksa_pada')->nullable()->after('diperiksa_oleh');
            $table->foreignId('disetujui_oleh')->nullable()->after('diperiksa_pada')->constrained('users')->nullOnDelete();
            $table->timestamp('disetujui_pada')->nullable()->after('disetujui_oleh');
        });

        Schema::table('disposisi', function (Blueprint $table) {
            $table->string('nomor_disposisi', 32)->nullable()->unique()->after('id');
            $table->enum('klasifikasi_keamanan', ['biasa', 'terbatas', 'rahasia'])->default('biasa')->after('naskah_masuk_id');
            $table->date('batas_waktu')->nullable()->after('catatan_pimpinan');
            $table->foreignId('diterbitkan_oleh')->nullable()->after('batas_waktu')->constrained('users')->nullOnDelete();
            $table->timestamp('diterbitkan_pada')->nullable()->after('diterbitkan_oleh');
        });
    }

    public function down(): void
    {
        Schema::table('disposisi', function (Blueprint $table) {
            $table->dropForeign(['diterbitkan_oleh']);
            $table->dropUnique(['nomor_disposisi']);
            $table->dropColumn(['nomor_disposisi', 'klasifikasi_keamanan', 'batas_waktu', 'diterbitkan_oleh', 'diterbitkan_pada']);
        });

        Schema::table('naskah_keluar', function (Blueprint $table) {
            $table->dropForeign(['konseptor_id']);
            $table->dropForeign(['diperiksa_oleh']);
            $table->dropForeign(['disetujui_oleh']);
            $table->dropColumn(['klasifikasi_keamanan', 'konseptor_id', 'diperiksa_oleh', 'diperiksa_pada', 'disetujui_oleh', 'disetujui_pada']);
        });

        Schema::table('naskah_masuk', function (Blueprint $table) {
            $table->dropColumn('klasifikasi_keamanan');
        });
    }
};
