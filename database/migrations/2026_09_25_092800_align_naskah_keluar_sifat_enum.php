<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('naskah_keluar')->where('sifat', 'segera')->update(['sifat' => 'penting']);

        DB::statement("ALTER TABLE naskah_keluar MODIFY sifat ENUM('biasa', 'penting', 'rahasia') NOT NULL DEFAULT 'biasa'");
    }

    public function down(): void
    {
        DB::table('naskah_keluar')->where('sifat', 'rahasia')->update(['sifat' => 'penting']);

        DB::statement("ALTER TABLE naskah_keluar MODIFY sifat ENUM('biasa', 'penting', 'segera') NOT NULL DEFAULT 'biasa'");
    }
};
