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
        'perihal',
        'pokok_isi',
        'kode_klasifikasi',
        'sifat',
        'file_draft_mentah',
        'file_pdf_final',
        'status_alur',
        'no_surat_resmi',
        'nomor_agenda',
        'tgl_kirim',
    ];
}