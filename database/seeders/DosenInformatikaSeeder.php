<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DosenInformatikaSeeder extends Seeder
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

        // 2. Data dosen Program Studi Informatika dan Sistem Informasi
        $dosenInformatika = [
            // Dosen Teknik Informatika
            'Prof. Dr. Ir. Bambang Riyanto, M.Sc.',
            'Dr. Ahmad Suharto, S.Kom., M.T.',
            'Dr. Sari Wulandari, S.Kom., M.Kom.',
            'Dr. Budi Santoso, S.T., M.T.',
            'Dr. Indah Permatasari, S.Kom., M.Kom.',
            'Dr. Agus Setiawan, S.Kom., M.T.',
            'Dr. Rina Kartika Sari, S.Kom., M.Kom.',
            'Prof. Dr. Hendra Wijaya, S.T., M.T.',
            'Dr. Maya Sari Dewi, S.Kom., M.T.',
            'Andi Pratama, S.Kom., M.Kom.',
            'Dr. Dewi Lestari, S.T., M.T.',
            'Rudi Hermawan, S.Kom., M.T.',
            'Dr. Fitri Rahmawati, S.Kom., M.Kom.',
            'Bayu Setiawan, S.Kom., M.T.',
            'Dr. Yudi Prayitno, S.Kom., M.Kom.',
            'Arief Budiman, S.T., M.T.',
            'Dr. Dian Palupi Rini, S.Kom., M.Kom.',
            'Eko Prasetyo, S.Kom., M.T.',
            'Dr. Nurul Hidayah, S.T., M.T.',
            'Rizki Ananda, S.Kom., M.Kom.',
        ];

        $dosenSistemInformasi = [
            // Dosen Sistem Informasi
            'Prof. Dr. Joko Susilo, S.Kom., M.T.',
            'Dr. Lisa Anggraini, S.Kom., M.Kom.',
            'Dr. Siska Amelia, S.T., M.T.',
            'Dr. Bambang Purwanto, S.Kom., M.Kom.',
            'Dr. Retno Wulandari, S.T., M.T.',
            'Fajar Nugroho, S.Kom., M.Kom.',
            'Dr. Tri Wahyuni, S.Kom., M.T.',
            'Dedy Kurniawan, S.T., M.T.',
            'Dr. Novita Sari, S.Kom., M.Kom.',
            'Hendri Prasetya, S.Kom., M.T.',
            'Dr. Lia Kamaria, S.T., M.T.',
            'Wahyu Hidayat, S.Kom., M.Kom.',
            'Dr. Putu Wijaya, S.Kom., M.T.',
            'Santi Maharani, S.T., M.T.',
            'Dr. Adi Saputra, S.Kom., M.Kom.',
            'Nina Kusumawati, S.Kom., M.T.',
            'Dr. Rahmat Hidayat, S.T., M.T.',
            'Dwi Purnomo, S.Kom., M.Kom.',
            'Dr. Fitria Sari, S.Kom., M.T.',
            'Budi Raharjo, S.T., M.T.',
        ];

        // 3. Kombinasikan semua dosen dengan informasi prodi
        $allDosen = [];

        // Tambahkan dosen Teknik Informatika
        foreach ($dosenInformatika as $nama) {
            $allDosen[] = [
                'nama' => $nama,
                'prodi' => 'Teknik Informatika'
            ];
        }

        // Tambahkan dosen Sistem Informasi
        foreach ($dosenSistemInformasi as $nama) {
            $allDosen[] = [
                'nama' => $nama,
                'prodi' => 'Sistem Informasi'
            ];
        }

        // 4. Insert data dosen ke database
        $this->insertDosenData($allDosen);

        $this->command->info(count($allDosen) . ' data dosen berhasil di-seed untuk Prodi Informatika dan Sistem Informasi.');
    }

    /**
     * Insert data dosen ke database dengan data lengkap
     */
    private function insertDosenData($dosenList)
    {
        // Distribusi jabatan berdasarkan gelar
        $jabatanMapping = [
            'Prof. Dr.' => ['Profesor'],
            'Dr.' => ['Profesor', 'Lektor Kepala', 'Lektor'],
            'default' => ['Lektor', 'Asisten Ahli']
        ];

        // Bidang keahlian untuk Teknik Informatika
        $keahlianTeknikInformatika = [
            'Kecerdasan Buatan',
            'Machine Learning',
            'Deep Learning',
            'Computer Vision',
            'Natural Language Processing',
            'Jaringan Komputer',
            'Keamanan Siber',
            'Cloud Computing',
            'Internet of Things',
            'Mobile Development',
            'Web Development',
            'Software Engineering',
            'Algoritma dan Struktur Data',
            'Pemrograman',
            'Robotika'
        ];

        // Bidang keahlian untuk Sistem Informasi
        $keahlianSistemInformasi = [
            'Sistem Informasi Manajemen',
            'Basis Data',
            'Data Mining',
            'Data Science',
            'Business Intelligence',
            'Enterprise Resource Planning',
            'E-Commerce',
            'E-Business',
            'Audit Sistem Informasi',
            'Tata Kelola TI',
            'Manajemen Proyek TI',
            'Analisis dan Perancangan Sistem',
            'User Experience Design',
            'Human Computer Interaction',
            'Digital Marketing'
        ];

        $dosenDataForInsert = [];
        $usedNik = [];
        $usedNidn = [];
        $usedEmails = [];

        foreach ($dosenList as $dosen) {
            $nama = $dosen['nama'];
            $prodi = $dosen['prodi'];

            // Tentukan jabatan berdasarkan gelar
            $jabatan = $this->getJabatanByGelar($nama, $jabatanMapping);

            // Tentukan bidang keahlian berdasarkan prodi
            if ($prodi === 'Teknik Informatika') {
                $bidangKeahlian = $keahlianTeknikInformatika[array_rand($keahlianTeknikInformatika)];
            } else {
                $bidangKeahlian = $keahlianSistemInformasi[array_rand($keahlianSistemInformasi)];
            }

            // Generate NIK unik
            do {
                $nik = '33' . str_pad(mt_rand(1, 99999999999999), 14, '0', STR_PAD_LEFT);
            } while (in_array($nik, $usedNik));
            $usedNik[] = $nik;

            // Generate NIDN unik  
            do {
                $nidn = '00' . str_pad(mt_rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
            } while (in_array($nidn, $usedNidn));
            $usedNidn[] = $nidn;

            // Generate email unik
            $email = $this->generateUniqueEmail($nama, $usedEmails);
            $usedEmails[] = $email;

            $dosenDataForInsert[] = [
                'nama' => $nama,
                'nik' => $nik,
                'nidn' => $nidn,
                'jabatan' => $jabatan,
                'prodi' => $prodi,
                'bidang_keahlian' => $bidangKeahlian,
                'email' => $email,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert data ke database dalam batch
        if (!empty($dosenDataForInsert)) {
            $chunks = array_chunk($dosenDataForInsert, 20);
            foreach ($chunks as $chunk) {
                DB::table('dosen')->insert($chunk);
            }
        }
    }

    /**
     * Menentukan jabatan berdasarkan gelar dalam nama
     */
    private function getJabatanByGelar($nama, $jabatanMapping)
    {
        if (strpos($nama, 'Prof. Dr.') !== false) {
            return $jabatanMapping['Prof. Dr.'][array_rand($jabatanMapping['Prof. Dr.'])];
        } elseif (strpos($nama, 'Dr.') !== false) {
            return $jabatanMapping['Dr.'][array_rand($jabatanMapping['Dr.'])];
        } else {
            return $jabatanMapping['default'][array_rand($jabatanMapping['default'])];
        }
    }

    /**
     * Generate email unik dari nama dosen
     */
    private function generateUniqueEmail($nama, $usedEmails)
    {
        // Bersihkan nama dari gelar dan karakter khusus
        $cleanName = $nama;
        $cleanName = preg_replace('/Prof\.\s*Dr\.\s*Ir\.\s*/', '', $cleanName);
        $cleanName = preg_replace('/Prof\.\s*Dr\.\s*/', '', $cleanName);
        $cleanName = preg_replace('/Dr\.\s*/', '', $cleanName);
        $cleanName = preg_replace('/Ir\.\s*/', '', $cleanName);
        $cleanName = preg_replace('/,.*$/', '', $cleanName); // Hapus semua setelah koma (gelar)

        // Pisahkan kata dan ambil nama depan dan belakang
        $words = array_filter(explode(' ', trim($cleanName)));

        if (count($words) >= 2) {
            $firstName = Str::lower(preg_replace('/[^a-zA-Z]/', '', $words[0]));
            $lastName = Str::lower(preg_replace('/[^a-zA-Z]/', '', $words[1]));
            $baseEmail = $firstName . '.' . $lastName;
        } elseif (count($words) == 1) {
            $baseEmail = Str::lower(preg_replace('/[^a-zA-Z]/', '', $words[0]));
        } else {
            $baseEmail = 'dosen' . mt_rand(1000, 9999);
        }

        // Batasi panjang email
        $baseEmail = substr($baseEmail, 0, 25);
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
