<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NaskahMasuk extends Model
{
    use HasFactory;

    // Mendefinisikan nama tabel secara eksplisit sesuai migration
    protected $table = 'naskah_masuk';

    // Proteksi kolom yang boleh diisi secara massal oleh form frontend
    protected $fillable = [
        'nomor_agenda',
        'no_surat',
        'asal_pengirim',
        'tgl_surat',
        'tgl_terima',
        'perihal',
        'kode_klasifikasi',
        'file_pdf',
        'status_disposisi',
    ];

    /**
     * Relasi One-to-Many: Satu naskah masuk bisa memiliki lembar disposisi terkait.
     */
    public function disposisi()
    {
        return $this->hasMany(Disposisi::class, 'naskah_masuk_id');
    }
}
