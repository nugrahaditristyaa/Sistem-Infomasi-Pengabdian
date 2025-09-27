<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mahasiswa')->insert([
            [
                'nim' => '2021001',
                'nama' => 'Rizki Pratama',
                'prodi' => 'Teknik Informatika'
            ],
            [
                'nim' => '2021002',
                'nama' => 'Anisa Putri',
                'prodi' => 'Sistem Informasi'
            ],
            [
                'nim' => '2021003',
                'nama' => 'Doni Kusuma',
                'prodi' => 'Teknik Informatika'
            ],
            [
                'nim' => '2021004',
                'nama' => 'Sari Indah',
                'prodi' => 'Sistem Informasi'
            ],
            [
                'nim' => '2021005',
                'nama' => 'Budi Setiawan',
                'prodi' => 'Teknik Informatika'
            ],
            [
                'nim' => '2021006',
                'nama' => 'Maya Sari',
                'prodi' => 'Sistem Informasi'
            ],
            [
                'nim' => '2021007',
                'nama' => 'Agus Prasetyo',
                'prodi' => 'Teknik Informatika'
            ],
            [
                'nim' => '2021008',
                'nama' => 'Nina Safitri',
                'prodi' => 'Sistem Informasi'
            ]
        ]);
    }
}








