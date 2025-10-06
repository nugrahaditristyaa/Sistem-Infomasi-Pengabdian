<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dosen;
use Illuminate\Support\Facades\DB;

class DosenSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Dosen::truncate();

        $csvFile = fopen(database_path("seeders/data/data_dosen.csv"), "r");

        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                $prodi = null;
                if (isset($data[6]) && $data[6] == '71') {
                    $prodi = 'Informatika';
                } elseif (isset($data[6]) && $data[6] == '72') {
                    $prodi = 'Sistem Informasi';
                }

                $nik = $data[5] ?? null;
                $nama = isset($data[8]) ? trim($data[8]) : null; // Ambil nama

                // ==========================================================
                //    TAMBAHKAN PENGECEKAN NAMA TIDAK KOSONG DI SINI
                // ==========================================================
                if ($prodi && !empty($nik) && !empty($nama)) {
                    Dosen::firstOrCreate(
                        ['nik' => $nik],
                        [
                            "nama"  => $nama,
                            "nidn"  => $data[9],
                            "prodi" => $prodi,
                            "email" => $nik . '@example.com',
                        ]
                    );
                }
            }
            $firstline = false;
        }

        fclose($csvFile);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}