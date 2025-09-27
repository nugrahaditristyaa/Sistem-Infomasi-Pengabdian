<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // <-- TAMBAHKAN INI
use Illuminate\Support\Str;

class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Matikan sementara pengecekan foreign key
        Schema::disableForeignKeyConstraints();

        // 2. Kosongkan tabel dosen
        DB::table('dosen')->truncate();

        // 3. Aktifkan kembali pengecekan foreign key
        Schema::enableForeignKeyConstraints();

        $dosen = [
            ['nama' => 'Lionel Messi'],
            ['nama' => 'Cristiano Ronaldo'],
            ['nama' => 'Neymar Jr'],
            ['nama' => 'Kylian Mbappé'],
            ['nama' => 'Kevin De Bruyne'],
            ['nama' => 'Robert Lewandowski'],
            ['nama' => 'Luka Modrić'],
            ['nama' => 'Sadio Mané'],
            ['nama' => 'Mohamed Salah'],
            ['nama' => 'Virgil van Dijk'],
            ['nama' => 'Zlatan Ibrahimović'],
            ['nama' => 'Andrés Iniesta'],
            ['nama' => 'Sergio Ramos'],
            ['nama' => 'Erling Haaland'],
            ['nama' => 'Karim Benzema'],
        ];

        $jabatan = ['Lektor', 'Asisten Ahli', 'Profesor', 'Lektor Kepala'];
        $prodi = ['Teknik Informatika', 'Sistem Informasi', 'Manajemen Informatika', 'Ilmu Komputer'];
        $keahlian = ['Kecerdasan Buatan', 'Jaringan Komputer', 'Pengembangan Web', 'Basis Data', 'Keamanan Siber'];

        foreach ($dosen as &$data) {
            $data['nik'] = '35' . mt_rand(10000000000000, 99999999999999);
            $data['nidn'] = '00' . mt_rand(10000000, 99999999);
            $data['jabatan'] = $jabatan[array_rand($jabatan)];
            $data['prodi'] = $prodi[array_rand($prodi)];
            $data['bidang_keahlian'] = $keahlian[array_rand($keahlian)];
            $data['email'] = Str::lower(str_replace(' ', '.', $data['nama'])) . '@kampus.ac.id';
            $data['created_at'] = now();
            $data['updated_at'] = now();
        }

        // 4. Masukkan data dosen yang baru
        DB::table('dosen')->insert($dosen);
    }
}