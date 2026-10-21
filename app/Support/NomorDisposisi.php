<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class NomorDisposisi
{
    public static function berikutnya(): string
    {
        $tahun = now()->year;
        $awalan = "D.{$tahun}.";

        $terakhir = DB::table('disposisi')
            ->where('nomor_disposisi', 'like', $awalan . '%')
            ->orderByDesc('nomor_disposisi')
            ->lockForUpdate()
            ->value('nomor_disposisi');

        $nomor = 1;

        if (is_string($terakhir) && preg_match('/\.(\d{4})$/', $terakhir, $cocok)) {
            $nomor = ((int) $cocok[1]) + 1;
        }

        return sprintf('D.%d.%04d', $tahun, $nomor);
    }
}