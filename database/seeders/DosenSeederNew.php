<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class DosenSeederNew extends Seeder
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

        // 2. Tentukan path ke file Excel
        $path = database_path('seeders/csv/Detail_PKM_&_Dana_FTI_2025_08_08.xlsx');

        if (file_exists($path)) {
            $this->seedFromExcel($path);
        } else {
            $this->command->warn("File Excel tidak ditemukan: $path");
            $this->command->info("Menggunakan data dummy sebagai gantinya...");
            $this->seedDummyData();
        }
    }

    /**
     * Seed data dari file Excel
     */
    private function seedFromExcel($path)
    {
        try {
            // Baca file Excel
            $collection = Excel::toCollection(null, $path);
            $rows = $collection[0]; // Ambil sheet pertama

            $uniqueDosenNames = [];

            // Lewati baris header dan proses data
            foreach ($rows->slice(1) as $row) {
                // Ambil nama dari kolom ketua (indeks 1) dan anggota (indeks 2-5)
                for ($i = 1; $i <= 5; $i++) {
                    $dosenString = trim($row[$i] ?? '');

                    if (!empty($dosenString)) {
                        // Pisahkan jika ada multiple dosen dalam satu kolom
                        $dosenNames = $this->splitDosenNames($dosenString);

                        foreach ($dosenNames as $nama) {
                            $cleanName = $this->cleanDosenName($nama);
                            if (!empty($cleanName) && strlen($cleanName) > 2) {
                                $uniqueDosenNames[$cleanName] = true;
                            }
                        }
                    }
                }
            }

            // Convert ke array dan insert ke database
            $dosenList = array_keys($uniqueDosenNames);
            $this->insertDosenData($dosenList);

            $this->command->info(count($dosenList) . ' data dosen unik berhasil di-seed dari Excel.');
        } catch (\Exception $e) {
            $this->command->error("Error membaca Excel: " . $e->getMessage());
            $this->command->info("Menggunakan data dummy sebagai gantinya...");
            $this->seedDummyData();
        }
    }

    /**
     * Memisahkan nama dosen yang digabung dalam satu kolom
     */
    private function splitDosenNames($dosenString)
    {
        // Berbagai cara pemisahan nama dosen
        $separators = [
            ';',
            ',',
            '&',
            ' dan ',
            ' and ',
            '|',
            ' - ',
            ' / ',
            '\n',
            '\r\n'
        ];

        $names = [$dosenString]; // Mulai dengan string asli

        // Pisahkan berdasarkan separator
        foreach ($separators as $separator) {
            $tempNames = [];
            foreach ($names as $name) {
                $split = explode($separator, $name);
                $tempNames = array_merge($tempNames, $split);
            }
            $names = $tempNames;
        }

        // Bersihkan dan filter nama
        $cleanNames = [];
        foreach ($names as $name) {
            $clean = trim($name);
            if (!empty($clean)) {
                $cleanNames[] = $clean;
            }
        }

        return $cleanNames;
    }

    /**
     * Membersihkan nama dosen dari karakter tidak diinginkan
     */
    private function cleanDosenName($name)
    {
        // Hapus karakter khusus dan angka yang tidak perlu
        $name = trim($name);
        $name = preg_replace('/^\d+\.?\s*/', '', $name); // Hapus nomor urut di awal
        $name = preg_replace('/\s+/', ' ', $name); // Normalize whitespace
        $name = preg_replace('/[^\p{L}\s\.,\-]/u', '', $name); // Hanya huruf, spasi, titik, koma, dash

        return trim($name);
    }

    /**
     * Insert data dosen ke database
     */
    private function insertDosenData($dosenNames)
    {
        $jabatan = [
            'Profesor',
            'Lektor Kepala',
            'Lektor',
            'Asisten Ahli'
        ];

        $prodi = [
            'Teknik Informatika',
            'Sistem Informasi',
            'Manajemen Informatika',
            'Ilmu Komputer',
            'Teknik Elektro',
            'Matematika'
        ];

        $keahlian = [
            'Kecerdasan Buatan',
            'Jaringan Komputer',
            'Pengembangan Web',
            'Basis Data',
            'Keamanan Siber',
            'Machine Learning',
            'Data Science',
            'Mobile Development',
            'Cloud Computing',
            'Internet of Things',
            'Blockchain',
            'Computer Vision',
            'Natural Language Processing',
            'Software Engineering'
        ];

        $dosenDataForInsert = [];
        $usedNik = [];
        $usedNidn = [];

        foreach ($dosenNames as $nama) {
            // Generate NIK unik
            do {
                $nik = '35' . str_pad(mt_rand(1, 99999999999999), 14, '0', STR_PAD_LEFT);
            } while (in_array($nik, $usedNik));
            $usedNik[] = $nik;

            // Generate NIDN unik
            do {
                $nidn = '00' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
            } while (in_array($nidn, $usedNidn));
            $usedNidn[] = $nidn;

            // Generate email dari nama
            $emailName = Str::lower(str_replace([' ', '.', ',', '-'], '', $nama));
            $emailName = preg_replace('/[^a-z]/', '', $emailName); // Hanya huruf
            $email = substr($emailName, 0, 20) . '@ukdw.ac.id';

            $dosenDataForInsert[] = [
                'nama' => $nama,
                'nik' => $nik,
                'nidn' => $nidn,
                'jabatan' => $jabatan[array_rand($jabatan)],
                'prodi' => $prodi[array_rand($prodi)],
                'bidang_keahlian' => $keahlian[array_rand($keahlian)],
                'email' => $email,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert dalam batch untuk performa lebih baik
        if (!empty($dosenDataForInsert)) {
            // Insert per batch 50 data untuk menghindari memory issues
            $chunks = array_chunk($dosenDataForInsert, 50);
            foreach ($chunks as $chunk) {
                DB::table('dosen')->insert($chunk);
            }
        }
    }

    /**
     * Seed data dummy jika Excel tidak tersedia
     */
    private function seedDummyData()
    {
        $dosenNames = [
            'Dr. Ahmad Suharto, M.Kom.',
            'Prof. Dr. Sari Wulandari, S.T., M.T.',
            'Dr. Budi Santoso, S.Kom., M.T.',
            'Indah Permatasari, S.Kom., M.Kom.',
            'Dr. Agus Setiawan, S.T., M.T.',
            'Dr. Rina Kartika, S.Kom., M.Kom.',
            'Prof. Dr. Hendra Wijaya, S.T., M.T.',
            'Dr. Maya Sari, S.Kom., M.T.',
            'Andi Pratama, S.Kom., M.Kom.',
            'Dr. Dewi Lestari, S.T., M.T.',
            'Rudi Hermawan, S.Kom., M.T.',
            'Dr. Fitri Rahmawati, S.Kom., M.Kom.',
            'Prof. Dr. Joko Susilo, S.T., M.T.',
            'Dr. Lisa Anggraini, S.Kom., M.T.',
            'Bayu Setiawan, S.Kom., M.Kom.'
        ];

        $this->insertDosenData($dosenNames);
        $this->command->info(count($dosenNames) . ' data dosen dummy berhasil di-seed.');
    }
}
