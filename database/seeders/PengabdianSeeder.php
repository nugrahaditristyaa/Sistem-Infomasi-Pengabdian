<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengabdian;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengabdianSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Kosongkan tabel-tabel yang akan diisi oleh seeder ini
        Pengabdian::truncate();
        DB::table('pengabdian_dosen')->truncate();
        DB::table('pengabdian_mahasiswa')->truncate();
        DB::table('sumber_dana')->truncate();
        DB::table('mitra')->truncate();

        // Ambil semua data dosen dari database dan urutkan dari nama terpanjang
        $sortedDosen = Dosen::all()->sortByDesc(function ($dosen) {
            return strlen($dosen->nama);
        });

        $csvFile = fopen(database_path("seeders/data/data_pengabdian.csv"), "r");

        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                $pengabdian = Pengabdian::create([
                    "tanggal_pengabdian" => Carbon::parse($data[2])->format('Y-m-d'),
                    "judul_pengabdian"   => $data[3],
                    "id_luaran_wajib"    => 1, // Sesuaikan jika perlu
                ]);

                // Proses Dosen (Ketua & Anggota) dengan logika pencocokan
                if (!empty($data[8])) {
                    $stringDosenDariCsv = $data[8];
                    $dosenDitemukan = [];

                    foreach ($sortedDosen as $dosen) {
                        if (str_contains($stringDosenDariCsv, $dosen->nama)) {
                            $dosenDitemukan[] = $dosen;
                            $stringDosenDariCsv = str_replace($dosen->nama, '', $stringDosenDariCsv);
                        }
                    }
                    
                    foreach ($dosenDitemukan as $index => $dosen) {
                        $status = ($index == 0) ? 'ketua' : 'anggota';
                        $pengabdian->dosen()->attach($dosen->nik, ['status_anggota' => $status]);

                        if ($status == 'ketua') {
                            $pengabdian->ketua_pengabdian = $dosen->nik;
                            $pengabdian->save();
                        }
                    }
                }

                // Proses Mahasiswa
                if (!empty($data[10])) {
                    $mahasiswaNames = explode(',', $data[10]);
                    $mahasiswaNims = explode(',', $data[11]);
                    
                    foreach ($mahasiswaNames as $index => $namaMahasiswa) {
                        $namaMahasiswa = trim($namaMahasiswa);
                        $nim = isset($mahasiswaNims[$index]) ? trim($mahasiswaNims[$index]) : null;
                        
                        if (empty($namaMahasiswa) || empty($nim)) continue;

                        $mahasiswa = Mahasiswa::firstOrCreate(
                            ['nim' => $nim],
                            ['nama' => $namaMahasiswa, 'prodi' => trim($data[12]) ?? null]
                        );
                        $pengabdian->mahasiswa()->attach($mahasiswa->nim);
                    }
                }

                // Proses Sumber Dana
                $pengabdian->sumberDana()->create([
                    'jenis'       => $data[7],
                    'nama_sumber' => $data[5],
                    'jumlah_dana' => (int) preg_replace('/[^0-9]/', '', $data[4])
                ]);

                // Proses Mitra
                if (!empty($data[13])) {
                     $pengabdian->mitra()->create([
                        'nama_mitra' => $data[13]
                    ]);
                }
            }
            $firstline = false;
        }

        fclose($csvFile);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}