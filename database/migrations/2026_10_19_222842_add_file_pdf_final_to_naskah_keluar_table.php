<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('naskah_keluar', function (Blueprint $table) {
            $table->string('file_pdf_final')
                ->nullable()
                ->after('file_draft_mentah');
        });

        // Salin referensi berkas yang jelas bernama PDF final.
        // Kolom lama tidak dihapus atau diubah.
        DB::table('naskah_keluar')
            ->where('status_alur', 'selesai')
            ->whereRaw('LEFT(file_draft_mentah, 13) = ?', ['final_keluar_'])
            ->update([
                'file_pdf_final' => DB::raw('file_draft_mentah'),
            ]);
    }

    public function down(): void
    {
        Schema::table('naskah_keluar', function (Blueprint $table) {
            $table->dropColumn('file_pdf_final');
        });
    }
};
