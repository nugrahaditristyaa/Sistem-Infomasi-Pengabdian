<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JenisLuaranSeeder extends Seeder
{
    /**
     * Menjalankan proses seeding database.
     *
     * @return void
     */
    public function run()
    {
        // Mengambil waktu saat ini untuk timestamp
        $now = Carbon::now();

        // Data jenis luaran berdasarkan value dari checkbox di view
        $jenisLuaran = [
            ['nama_jenis_luaran' => 'Laporan Akhir', 'created_at' => $now, 'updated_at' => $now],
            ['nama_jenis_luaran' => 'HKI', 'created_at' => $now, 'updated_at' => $now],
            ['nama_jenis_luaran' => 'Jurnal Internasional', 'created_at' => $now, 'updated_at' => $now],
            ['nama_jenis_luaran' => 'Jurnal Nasional', 'created_at' => $now, 'updated_at' => $now],
            ['nama_jenis_luaran' => 'Buku', 'created_at' => $now, 'updated_at' => $now],
            ['nama_jenis_luaran' => 'Lainnya', 'created_at' => $now, 'updated_at' => $now],
        ];

        // Memasukkan data ke dalam tabel 'jenis_luaran'
        // Disarankan untuk menjalankan ini pada database yang bersih untuk menghindari duplikasi
        DB::table('jenis_luaran')->insert($jenisLuaran);
    }
}