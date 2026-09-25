<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NaskahKeluar extends Model
{
    use HasFactory;

    protected $table = 'naskah_keluar';

    protected $fillable = [
        'tujuan_surat',
        'perihal',     // PASTIKAN INI ADA
        'pokok_isi',    // PASTIKAN INI JUGA ADA
        'kode_klasifikasi',
        'sifat',
        'file_draft_mentah',
        'status_alur',
        'no_surat_resmi',
    ];

}
