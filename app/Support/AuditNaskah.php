<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditNaskah
{
    public static function catat(
        string $jenisNaskah,
        int $naskahId,
        string $aksi,
        ?string $statusSebelum = null,
        ?string $statusSesudah = null,
        ?string $keterangan = null
    ): void {
        DB::table('audit_naskah')->insert([
            'jenis_naskah' => $jenisNaskah,
            'naskah_id' => $naskahId,
            'aksi' => $aksi,
            'pelaku_id' => Auth::id(),
            'status_sebelum' => $statusSebelum,
            'status_sesudah' => $statusSesudah,
            'keterangan' => $keterangan,
            'alamat_ip' => request()?->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
