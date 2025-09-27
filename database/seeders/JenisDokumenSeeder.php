<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JenisDokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Nonaktifkan pengecekan foreign key untuk sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 2. Kosongkan tabel
        DB::table('jenis_dokumen')->truncate();

        $now = Carbon::now();
        $jenisDokumen = [
            ['id_jenis_dokumen' => 1, 'nama_jenis_dokumen' => 'Surat Tugas Dosen', 'created_at' => $now, 'updated_at' => $now],
            // Pastikan ID 2 adalah Dokumen HKI agar sesuai dengan Controller
            ['id_jenis_dokumen' => 2, 'nama_jenis_dokumen' => 'Dokumen HKI', 'created_at' => $now, 'updated_at' => $now],
            ['id_jenis_dokumen' => 3, 'nama_jenis_dokumen' => 'Surat Permohonan', 'created_at' => $now, 'updated_at' => $now],
            ['id_jenis_dokumen' => 4, 'nama_jenis_dokumen' => 'Surat Ucapan Terima Kasih', 'created_at' => $now, 'updated_at' => $now],
            ['id_jenis_dokumen' => 5, 'nama_jenis_dokumen' => 'MoU/MoA/Dokumen Kerja Sama Kegiatan', 'created_at' => $now, 'updated_at' => $now],
            // Anda bisa menambahkan Laporan Akhir jika diperlukan
            ['id_jenis_dokumen' => 6, 'nama_jenis_dokumen' => 'Laporan Akhir', 'created_at' => $now, 'updated_at' => $now],
        ];

        // 3. Masukkan data yang baru
        DB::table('jenis_dokumen')->insert($jenisDokumen);

        // 4. Aktifkan kembali pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

