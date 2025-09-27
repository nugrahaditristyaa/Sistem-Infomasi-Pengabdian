<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LuaranWajibSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Hapus data lama untuk menghindari duplikasi saat seeder dijalankan ulang
        DB::table('luaran_wajib')->delete();

        $now = Carbon::now();
        $luaranWajib = [
            ['nama_luaran' => 'Penyelesaian masalah masyarakat dengan keahlian sivitas', 'created_at' => $now, 'updated_at' => $now],
            ['nama_luaran' => 'Teknologi tepat guna', 'created_at' => $now, 'updated_at' => $now],
            ['nama_luaran' => 'Bahan pengembangan iptek', 'created_at' => $now, 'updated_at' => $now],
            ['nama_luaran' => 'Publikasi/diseminasi hasil', 'created_at' => $now, 'updated_at' => $now],
            ['nama_luaran' => 'Bahan ajar atau modul pelatihan', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('luaran_wajib')->insert($luaranWajib);
    }
}

