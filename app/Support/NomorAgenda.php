<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class NomorAgenda
{
    /**
     * Nomor urut agenda per tahun: M.2026.0001 (masuk) atau K.2026.0001 (keluar).
     */
    public static function berikutnya(string $tabel, string $awalan): string
    {
        $tahun = (int) date('Y');
        $pola = $awalan.'.'.$tahun.'.';

        $terakhir = DB::table($tabel)
            ->where('nomor_agenda', 'like', $pola.'%')
            ->orderByDesc('nomor_agenda')
            ->lockForUpdate()
            ->value('nomor_agenda');

        $nomor = 1;
        if (is_string($terakhir) && preg_match('/\.(\d{4})$/', $terakhir, $cocok)) {
            $nomor = ((int) $cocok[1]) + 1;
        }

        return sprintf('%s.%d.%04d', $awalan, $tahun, $nomor);
    }

    public static function userDitugaskan(?object $disposisi, int $userId): bool
    {
        if (! $disposisi || empty($disposisi->guru_id_tujuan)) {
            return false;
        }

        $penerima = json_decode($disposisi->guru_id_tujuan, true);
        if (! is_array($penerima)) {
            return false;
        }

        $penerima = array_map('intval', $penerima);

        return in_array($userId, $penerima, true);
    }
}
