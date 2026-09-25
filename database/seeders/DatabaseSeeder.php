<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with users only (Fresh Clean Slate)
     */
    public function run(): void
    {
        // 1. NONAKTIFKAN PROTEKSI DATABASE
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement("SET sql_mode = '';");

        // 2. REKAYASA TOTAL TABEL DISPOSISI (Hancurkan dan buat ulang dengan kolom presisi)
        Schema::dropIfExists('disposisi');
        Schema::create('disposisi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('naskah_masuk_id');
            $table->string('sifat_instruksi')->default('tindak_lanjuti');
            $table->text('guru_id_tujuan'); // Mendukung tipe text JSON untuk multi-guru/jabatan
            $table->text('catatan_pimpinan')->nullable();
            $table->text('laporan_balik_guru')->nullable();
            $table->string('status_tugas')->default('proses');
            $table->timestamps();
        });

        // 3. PEMBERSIHAN DATA LAMA PADA TABEL UTAMA
        User::truncate();
        DB::table('naskah_masuk')->truncate();
        DB::table('naskah_keluar')->truncate();

        // 4. GENERATE DATA DUMMY AKUN USER (HANYA AKUN YANG DIBUAT UNTUK LOGIN)
        $kepsek = User::create([
            'name' => 'Dr. H. Ahmad Pimpinan, M.Pd.',
            'email' => 'kepsek@sman1.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'kepala_sekolah',
        ]);

        $katu = User::create([
            'name' => 'Bapak Budi Santoso, S.Sos. (Ka. TU)',
            'email' => 'katu@sman1.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'kepala_tu',
        ]);

        User::create([
            'name' => 'Operator Layanan Operasional',
            'email' => 'layanan@sman1.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'guru',
        ]);

        User::create([
            'name' => 'Pengadministrasi Perkantoran',
            'email' => 'admin@sman1.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'guru',
        ]);

        User::create([
            'name' => 'Staf Tata Usaha',
            'email' => 'staf.tu@sman1.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'guru',
        ]);

        User::create([
            'name' => 'Operator Sekolah (Dapodik/Sistem)',
            'email' => 'operator@sman1.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'guru',
        ]);

        User::create([
            'name' => 'Wakil Kepala Sekolah Bidang Kurikulum',
            'email' => 'waka.kurikulum@sman1.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'guru',
        ]);

        User::create([
            'name' => 'Wakil Kepala Sekolah Bidang Kesiswaan',
            'email' => 'waka.kesiswaan@sman1.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'guru',
        ]);

        User::create([
            'name' => 'Wakil Kepala Sekolah Bidang Sarana Prasarana',
            'email' => 'waka.sarpras@sman1.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'guru',
        ]);

        User::create([
            'name' => 'Wakil Kepala Sekolah Bidang Humas',
            'email' => 'waka.humas@sman1.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'guru',
        ]);

        User::create([
            'name' => 'Pembina OSIS & Ekstrakurikuler',
            'email' => 'pembina.osis@sman1.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'guru',
        ]);

        User::create([
            'name' => 'Kepala Perpustakaan Sekolah',
            'email' => 'ka.perpustakaan@sman1.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'guru',
        ]);

        User::create([
            'name' => 'Kepala Laboratorium Komputer & Multimedia',
            'email' => 'ka.lab@sman1.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'guru',
        ]);

        User::create([
            'name' => 'Koordinator Bimbingan Konseling (BK)',
            'email' => 'koor.bk@sman1.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'guru',
        ]);

        // 5. AKTIFKAN KEMBALI PROTEKSI DATABASE
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
