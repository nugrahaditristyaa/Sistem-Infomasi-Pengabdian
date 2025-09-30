<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DosenFromTextSeeder extends Seeder
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

        // 2. Contoh data dari Excel yang digabung dalam satu kolom
        // Format: data seperti yang mungkin ada di Excel Anda
        $excelData = [
            'Dr. Ahmad Suharto, S.Kom., M.T.; Prof. Dr. Sari Wulandari, S.T., M.T.',
            'Dr. Budi Santoso & Indah Permatasari, S.Kom., M.Kom.',
            'Dr. Agus Setiawan, S.T., M.T., Dr. Rina Kartika, S.Kom., M.Kom.',
            'Prof. Dr. Hendra Wijaya | Dr. Maya Sari | Andi Pratama, S.Kom.',
            'Dr. Dewi Lestari dan Rudi Hermawan, S.Kom., M.T.',
            'Dr. Fitri Rahmawati / Prof. Dr. Joko Susilo',
            'Dr. Lisa Anggraini, S.Kom., M.T. - Bayu Setiawan, S.Kom., M.Kom.',
            'Dr. Yudi Prayitno, M.Kom.; Dr. Siska Amelia, S.T., M.T.; Eko Prasetyo',
            'Prof. Dr. Bambang Riyanto & Dr. Nurul Hidayah, S.Kom., M.Kom.',
            'Dr. Dian Palupi, S.T., M.T., Arief Budiman, S.Kom., M.T.',
        ];

        $uniqueDosenNames = [];

        // Proses setiap baris data
        foreach ($excelData as $dosenString) {
            $dosenNames = $this->splitDosenNames($dosenString);

            foreach ($dosenNames as $nama) {
                $cleanName = $this->cleanDosenName($nama);
                if (!empty($cleanName) && strlen($cleanName) > 3) {
                    $uniqueDosenNames[$cleanName] = true;
                }
            }
        }

        // Convert ke array dan insert ke database
        $dosenList = array_keys($uniqueDosenNames);
        $this->insertDosenData($dosenList);

        $this->command->info(count($dosenList) . ' data dosen unik berhasil di-seed dari data text.');
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
            '\r\n',
            ' , ',
            ', '
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

        // Hapus kata-kata yang bukan nama (opsional)
        $excludeWords = ['dan', 'and', 'dengan', 'serta', '&', 'atau', 'or'];
        foreach ($excludeWords as $word) {
            $name = preg_replace('/\b' . preg_quote($word, '/') . '\b/i', '', $name);
        }

        // Hapus gelar dan title yang standalone
        $titles = ['S.Kom.', 'M.T.', 'M.Kom.', 'S.T.', 'Dr.', 'Prof.'];
        foreach ($titles as $title) {
            if (trim($name) === $title) {
                return ''; // Skip jika hanya gelar
            }
        }

        $name = preg_replace('/\s+/', ' ', $name); // Normalize whitespace lagi

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
            'Software Engineering',
            'Sistem Terdistribusi',
            'Human Computer Interaction'
        ];

        $dosenDataForInsert = [];
        $usedNik = [];
        $usedNidn = [];
        $usedEmails = [];

        foreach ($dosenNames as $nama) {
            // Skip nama yang terlalu pendek atau tidak valid
            if (strlen($nama) < 5 || preg_match('/^[A-Z]+\.?$/', $nama)) {
                continue;
            }

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

            // Generate email unik dari nama
            $email = $this->generateUniqueEmail($nama, $usedEmails);
            $usedEmails[] = $email;

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

        // Insert data ke database
        if (!empty($dosenDataForInsert)) {
            DB::table('dosen')->insert($dosenDataForInsert);
        }
    }

    /**
     * Generate email unik dari nama dosen
     */
    private function generateUniqueEmail($nama, $usedEmails)
    {
        // Bersihkan nama untuk email
        $cleanName = preg_replace('/[^a-zA-Z\s]/', '', $nama); // Hapus gelar dan karakter khusus
        $words = array_filter(explode(' ', trim($cleanName))); // Pisahkan kata dan hapus yang kosong

        // Strategi 1: firstname.lastname
        if (count($words) >= 2) {
            $firstName = Str::lower($words[0]);
            $lastName = Str::lower($words[1]);
            $baseEmail = $firstName . '.' . $lastName;
        } else if (count($words) == 1) {
            $baseEmail = Str::lower($words[0]);
        } else {
            $baseEmail = 'dosen' . mt_rand(1000, 9999);
        }

        // Bersihkan email dari karakter tidak valid
        $baseEmail = preg_replace('/[^a-z.]/', '', $baseEmail);
        $baseEmail = substr($baseEmail, 0, 20); // Batasi panjang

        $email = $baseEmail . '@ukdw.ac.id';
        $counter = 1;

        // Tambahkan angka jika email sudah ada
        while (in_array($email, $usedEmails)) {
            $email = $baseEmail . $counter . '@ukdw.ac.id';
            $counter++;
        }

        return $email;
    }
}
