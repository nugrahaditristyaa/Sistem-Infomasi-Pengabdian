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

        $csvFile = fopen(database_path("seeders/data/data_dosen_fti.csv"), "r");

        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ";")) !== FALSE) { // Menggunakan pemisah titik koma (;)
            if (!$firstline) {
                // Mapping prodi berdasarkan kode
                $prodi = null;
                if (isset($data[1]) && $data[1] == '71') {
                    $prodi = 'Informatika';
                } elseif (isset($data[1]) && $data[1] == '72') {
                    $prodi = 'Sistem Informasi';
                }

                $nik = $data[0] ?? null;
                $nama = isset($data[3]) ? trim($data[3]) : null;

                if ($prodi && !empty($nik) && !empty($nama)) {
                    Dosen::firstOrCreate(
                        ['nik' => $nik],
                        [
                            "nama"  => $nama,
                            "nidn"  => $data[4],
                            "prodi" => $prodi,
                            "email" => str_replace(' ', '.', strtolower($nama)) . '@example.com',
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