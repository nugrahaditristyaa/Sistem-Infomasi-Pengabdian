<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KpiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('kpi')->insert([
            // KPI Utama PGB
            [
                'kode' => 'PGB.I.1.1',
                'indikator' => 'Tercapainya minimal 80% (delapan puluh persen) kegitan PkM memenuhi luaran sesuai dengan proposal dan kesepakatan dengan pemberi dana, pada akhir pelaksanaan pengabdian.',
                'target' => 80,
                'satuan' => '%',
            ],
            [
                'kode' => 'PGB.I.5.6',
                'indikator' => 'Terjadi peningkatan jumlah pelaksana PkM minimal sebesar 10% per tahun.',
                'target' => 10,
                'satuan' => '%',
            ],
            [
                'kode' => 'PGB.I.7.4',
                'indikator' => 'Minimal 30% proposal PkM mendapat pendanaan eksternal dari mitra dalam dan luar negeri setiap tahun.',
                'target' => 30,
                'satuan' => '%',
            ],
            [
                'kode' => 'PGB.I.7.9',
                'indikator' => 'Terjadinya peningkatan jumlah proposal yang diterima sebesar 10% setiap 3 tahun.',
                'target' => 10,
                'satuan' => '%',
            ],
            
            // KPI Tambahan (IKT)
            [
                'kode' => 'IKT.I.5.g',
                'indikator' => 'Minimum 5% dari jumlah penelitian atau PkM digunakan dalam proses pembelajaran',
                'target' => 5,
                'satuan' => '%',
            ],
            [
                'kode' => 'IKT.I.5.h',
                'indikator' => 'Minimum 70% abdimas yg dilakukan dosen prodi dalam bidang INFOKOM',
                'target' => 70,
                'satuan' => '%',
            ],
            [
                'kode' => 'IKT.I.5.i',
                'indikator' => 'Minimum Prodi memiliki 1 HKI PkM setiap tahun',
                'target' => 1,
                'satuan' => 'HKI',
            ],
            [
                'kode' => 'IKT.I.5.j',
                'indikator' => 'Minimum 70% abdimas melibatkan minimal 1 mahasiswa',
                'target' => 70,
                'satuan' => '%',
            ],
        ]);
    }
}
