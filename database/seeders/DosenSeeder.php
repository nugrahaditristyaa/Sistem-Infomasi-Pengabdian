<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Mengosongkan tabel dengan aman
        Schema::disableForeignKeyConstraints();
        DB::table('dosen')->truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Data dosen dummy untuk testing
        $dosenData = [
            [
                'nama' => 'Dr. Ahmad Suharto',
                'nik' => '3507123456789012',
                'nidn' => '0012345678',
                'jabatan' => 'Profesor',
                'prodi' => 'Teknik Informatika',
                'bidang_keahlian' => 'Kecerdasan Buatan',
                'email' => 'ahmad.suharto@kampus.ac.id',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Dr. Sari Wulandari',
                'nik' => '3507123456789013',
                'nidn' => '0012345679',
                'jabatan' => 'Lektor Kepala',
                'prodi' => 'Sistem Informasi',
                'bidang_keahlian' => 'Basis Data',
                'email' => 'sari.wulandari@kampus.ac.id',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'M. Budi Santoso, S.Kom., M.T.',
                'nik' => '3507123456789014',
                'nidn' => '0012345680',
                'jabatan' => 'Lektor',
                'prodi' => 'Teknik Informatika',
                'bidang_keahlian' => 'Jaringan Komputer',
                'email' => 'budi.santoso@kampus.ac.id',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Indah Permatasari, S.Kom., M.Kom.',
                'nik' => '3507123456789015',
                'nidn' => '0012345681',
                'jabatan' => 'Asisten Ahli',
                'prodi' => 'Sistem Informasi',
                'bidang_keahlian' => 'Pengembangan Web',
                'email' => 'indah.permatasari@kampus.ac.id',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Agus Setiawan, S.Kom., M.T.',
                'nik' => '3507123456789016',
                'nidn' => '0012345682',
                'jabatan' => 'Lektor',
                'prodi' => 'Teknik Informatika',
                'bidang_keahlian' => 'Keamanan Siber',
                'email' => 'agus.setiawan@kampus.ac.id',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Generate lebih banyak data dosen dummy
        $jabatan = ['Lektor', 'Asisten Ahli', 'Profesor', 'Lektor Kepala'];
        $prodi = ['Teknik Informatika', 'Sistem Informasi', 'Manajemen Informatika', 'Ilmu Komputer'];
        $keahlian = ['Kecerdasan Buatan', 'Jaringan Komputer', 'Pengembangan Web', 'Basis Data', 'Keamanan Siber', 'Machine Learning', 'Data Science', 'Mobile Development'];

        for ($i = 6; $i <= 20; $i++) {
            $nama = 'Dosen ' . $i;
            $dosenData[] = [
                'nama' => $nama,
                'nik' => '35' . str_pad(mt_rand(1, 999999999999999), 15, '0', STR_PAD_LEFT),
                'nidn' => '00' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT),
                'jabatan' => $jabatan[array_rand($jabatan)],
                'prodi' => $prodi[array_rand($prodi)],
                'bidang_keahlian' => $keahlian[array_rand($keahlian)],
                'email' => Str::lower(str_replace([' ', '.'], '', $nama)) . '@kampus.ac.id',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 3. Masukkan semua data dosen ke database
        DB::table('dosen')->insert($dosenData);

        $this->command->info(count($dosenData) . ' data dosen berhasil di-seed.');
    }
}
