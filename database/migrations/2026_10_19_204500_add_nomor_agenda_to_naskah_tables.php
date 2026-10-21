<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('naskah_masuk', function (Blueprint $table) {
            $table->string('nomor_agenda', 32)->nullable()->unique()->after('id');
        });

        Schema::table('naskah_keluar', function (Blueprint $table) {
            $table->string('nomor_agenda', 32)->nullable()->unique()->after('id');
            $table->date('tgl_kirim')->nullable()->after('no_surat_resmi');
        });

        $this->isiAgendaLama('naskah_masuk', 'M');
        $this->isiAgendaLama('naskah_keluar', 'K', true);
    }

    public function down(): void
    {
        Schema::table('naskah_masuk', function (Blueprint $table) {
            $table->dropUnique(['nomor_agenda']);
            $table->dropColumn('nomor_agenda');
        });

        Schema::table('naskah_keluar', function (Blueprint $table) {
            $table->dropUnique(['nomor_agenda']);
            $table->dropColumn(['nomor_agenda', 'tgl_kirim']);
        });
    }

    private function isiAgendaLama(string $tabel, string $awalan, bool $hanyaSelesai = false): void
    {
        $query = DB::table($tabel)->orderBy('id');
        if ($hanyaSelesai) {
            $query->where('status_alur', 'selesai');
        }

        $baris = $query->get();
        $hitungTahun = [];

        foreach ($baris as $item) {
            $tahun = $item->created_at
                ? (int) date('Y', strtotime($item->created_at))
                : (int) date('Y');
            $hitungTahun[$tahun] = ($hitungTahun[$tahun] ?? 0) + 1;
            $nomor = sprintf('%s.%d.%04d', $awalan, $tahun, $hitungTahun[$tahun]);

            $data = ['nomor_agenda' => $nomor];
            if ($hanyaSelesai && Schema::hasColumn($tabel, 'tgl_kirim')) {
                $data['tgl_kirim'] = $item->updated_at
                    ? date('Y-m-d', strtotime($item->updated_at))
                    : date('Y-m-d');
            }

            DB::table($tabel)->where('id', $item->id)->update($data);
        }
    }
};
