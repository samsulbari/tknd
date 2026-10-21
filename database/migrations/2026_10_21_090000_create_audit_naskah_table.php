<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_naskah', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_naskah', 40);
            $table->unsignedBigInteger('naskah_id');
            $table->string('aksi', 80);
            $table->foreignId('pelaku_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status_sebelum', 80)->nullable();
            $table->string('status_sesudah', 80)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('alamat_ip', 45)->nullable();
            $table->timestamps();

            $table->index(['jenis_naskah', 'naskah_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_naskah');
    }
};
