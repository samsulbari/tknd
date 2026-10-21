<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disposisi extends Model
{
    use HasFactory;

    // Mendefinisikan nama tabel fisik secara eksplisit sesuai cetak biru migration
    protected $table = 'disposisi';

    // Kolom yang diizinkan untuk diisi data secara massal dari form pimpinan
    protected $fillable = [
        'naskah_masuk_id',
        'sifat_instruksi',
        'guru_id_tujuan',
        'catatan_pimpinan',
        'laporan_balik_guru',
        'status_tugas',
    ];

    // ATURAN EMAS: Mengonversi data multi-checkbox yang disimpan di database
    // dari bentuk teks JSON mentah otomatis menjadi Array PHP saat dibaca oleh Laravel
    protected $casts = [
    'guru_id_tujuan' => 'array',
    ];

    /**
     * Hubungan Kebalikan (Inverse Relationship): 
     * Satu lembar disposisi dimiliki oleh satu surat masuk tertentu.
     */
    public function naskahMasuk()
    {
        return $this->belongsTo(NaskahMasuk::class, 'naskah_masuk_id');
    }
}
