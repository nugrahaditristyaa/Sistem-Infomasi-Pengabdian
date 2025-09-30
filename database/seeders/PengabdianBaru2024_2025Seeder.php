<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengabdianBaru2024_2025Seeder extends Seeder
{
    /**
     * Run the database seeds.
     * Data pengabdian untuk tahun 2024 dan 2025
     *
     * @return void
     */
    public function run()
    {
        // Ambil ID terakhir untuk menentukan ID berikutnya
        $lastId = DB::table('pengabdian')->max('id_pengabdian') ?? 0;

        // Data pengabdian untuk insert (tanpa ID, biar auto increment)
        $pengabdianData = [
            // Pengabdian 2024
            [
                'ketua_pengabdian' => '3301295889234985', // Budi Raharjo
                'judul_pengabdian' => 'Implementasi Smart Village System untuk Digitalisasi Administrasi Desa',
                'tanggal_pengabdian' => '2024-01-15'
            ],
            [
                'ketua_pengabdian' => '3305572107650930', // Eko Prasetyo
                'judul_pengabdian' => 'Pelatihan Coding Bootcamp untuk Pemuda Desa dalam Era Digital',
                'tanggal_pengabdian' => '2024-02-20'
            ],
            [
                'ketua_pengabdian' => '3309220560774465', // Dr. Putu Wijaya
                'judul_pengabdian' => 'Pengembangan Sistem E-Commerce Terintegrasi untuk Produk Lokal UMKM',
                'tanggal_pengabdian' => '2024-03-10'
            ],
            [
                'ketua_pengabdian' => '3312464340318183', // Dr. Lia Kamaria
                'judul_pengabdian' => 'Workshop Internet of Things untuk Monitoring Pertanian Cerdas',
                'tanggal_pengabdian' => '2024-04-25'
            ],
            [
                'ketua_pengabdian' => '3314946768962700', // Nina Kusumawati
                'judul_pengabdian' => 'Pelatihan Keamanan Siber dan Digital Literacy untuk Guru',
                'tanggal_pengabdian' => '2024-05-18'
            ],
            [
                'ketua_pengabdian' => '3315739107503323', // Dr. Dewi Lestari
                'judul_pengabdian' => 'Implementasi Aplikasi Mobile untuk Layanan Kesehatan Desa',
                'tanggal_pengabdian' => '2024-06-12'
            ],
            [
                'ketua_pengabdian' => '3316119619596649', // Dr. Sari Wulandari
                'judul_pengabdian' => 'Sistem Informasi Manajemen Keuangan untuk Koperasi Desa',
                'tanggal_pengabdian' => '2024-07-08'
            ],
            [
                'ketua_pengabdian' => '3320171535847454', // Dr. Rahmat Hidayat
                'judul_pengabdian' => 'Pelatihan Teknologi Blockchain untuk Transparansi Pemerintahan Desa',
                'tanggal_pengabdian' => '2024-08-20'
            ],
            [
                'ketua_pengabdian' => '3327638348108618', // Dr. Ahmad Suharto
                'judul_pengabdian' => 'Pengembangan Platform E-Learning untuk Pendidikan Vokasi Desa',
                'tanggal_pengabdian' => '2024-09-15'
            ],
            [
                'ketua_pengabdian' => '3350154612596512', // Prof. Dr. Hendra Wijaya
                'judul_pengabdian' => 'Smart Waste Management System untuk Pengelolaan Sampah Desa',
                'tanggal_pengabdian' => '2024-10-30'
            ],

            // Pengabdian 2025
            [
                'ketua_pengabdian' => '3359053194521451', // Santi Maharani
                'judul_pengabdian' => 'Implementasi Artificial Intelligence untuk Prediksi Cuaca dalam Pertanian',
                'tanggal_pengabdian' => '2025-01-20'
            ],
            [
                'ketua_pengabdian' => '3362433463934673', // Rizki Ananda
                'judul_pengabdian' => 'Pengembangan Chatbot Multilingual untuk Customer Service UMKM',
                'tanggal_pengabdian' => '2025-02-14'
            ],
            [
                'ketua_pengabdian' => '3367466754033739', // Dr. Fitri Rahmawati
                'judul_pengabdian' => 'Workshop Machine Learning untuk Analisis Data Bisnis UMKM',
                'tanggal_pengabdian' => '2025-03-18'
            ],
            [
                'ketua_pengabdian' => '3372632676745389', // Prof. Dr. Joko Susilo
                'judul_pengabdian' => 'Sistem Monitoring Kesehatan Real-time Berbasis Wearable Technology',
                'tanggal_pengabdian' => '2025-04-22'
            ],
            [
                'ketua_pengabdian' => '3374746117300898', // Rudi Hermawan
                'judul_pengabdian' => 'Pelatihan Cloud Computing dan DevOps untuk Developer Lokal',
                'tanggal_pengabdian' => '2025-05-16'
            ],
            [
                'ketua_pengabdian' => '3377010381848788', // Fajar Nugroho
                'judul_pengabdian' => 'Implementasi Augmented Reality untuk Promosi Wisata Desa',
                'tanggal_pengabdian' => '2025-06-10'
            ],
            [
                'ketua_pengabdian' => '3382483291642301', // Dedy Kurniawan
                'judul_pengabdian' => 'Smart Traffic Management System untuk Kota Kecil',
                'tanggal_pengabdian' => '2025-07-24'
            ],
            [
                'ketua_pengabdian' => '3384814582224790', // Dr. Maya Sari Dewi
                'judul_pengabdian' => 'Pengembangan Digital Twin untuk Optimasi Proses Manufaktur UMKM',
                'tanggal_pengabdian' => '2025-08-19'
            ],
            [
                'ketua_pengabdian' => '3301932287194672', // Dr. Budi Santoso
                'judul_pengabdian' => 'Workshop Big Data Analytics untuk Pengambilan Keputusan Bisnis',
                'tanggal_pengabdian' => '2025-09-12'
            ],
            [
                'ketua_pengabdian' => '3377660731480056', // Dr. Adi Saputra
                'judul_pengabdian' => 'Sistem Otomasi Greenhouse Berbasis IoT untuk Pertanian Modern',
                'tanggal_pengabdian' => '2025-10-28'
            ]
        ];

        // Insert pengabdian dan ambil ID yang baru dibuat
        $insertedIds = [];
        foreach ($pengabdianData as $data) {
            $id = DB::table('pengabdian')->insertGetId($data);
            $insertedIds[] = $id;
        }

        // Sekarang kita bisa menggunakan ID yang benar untuk relasi
        $startId = $insertedIds[0];
        $endId = end($insertedIds);

        echo "Pengabdian berhasil ditambahkan dengan ID: {$startId} - {$endId}\n";

        // Pengabdian Dosen - Menambahkan anggota untuk setiap pengabdian
        $pengabdianDosenData = [];

        // Mapping ketua dan anggota
        $teamMapping = [
            0 => ['ketua' => '3301295889234985', 'anggota' => ['3327553344954898', '3341234539733970']],
            1 => ['ketua' => '3305572107650930', 'anggota' => ['3329893868439357', '3334916829564468']],
            2 => ['ketua' => '3309220560774465', 'anggota' => ['3341427383435468', '3347050661695715']],
            3 => ['ketua' => '3312464340318183', 'anggota' => ['3351700760065891', '3352456378986063']],
            4 => ['ketua' => '3314946768962700', 'anggota' => ['3362843448996565', '3367725819004675']],
            5 => ['ketua' => '3315739107503323', 'anggota' => ['3369291935079773', '3382994111813061']],
            6 => ['ketua' => '3316119619596649', 'anggota' => ['3309928905614555', '3323268348724687']],
            7 => ['ketua' => '3320171535847454', 'anggota' => ['3316494107330788', '3317629146761377']],
            8 => ['ketua' => '3327638348108618', 'anggota' => ['3327553344954898', '3359053194521451']],
            9 => ['ketua' => '3350154612596512', 'anggota' => ['3362433463934673', '3367466754033739']],
            10 => ['ketua' => '3359053194521451', 'anggota' => ['3301295889234985', '3305572107650930']],
            11 => ['ketua' => '3362433463934673', 'anggota' => ['3309220560774465', '3312464340318183']],
            12 => ['ketua' => '3367466754033739', 'anggota' => ['3314946768962700', '3315739107503323']],
            13 => ['ketua' => '3372632676745389', 'anggota' => ['3316119619596649', '3320171535847454']],
            14 => ['ketua' => '3374746117300898', 'anggota' => ['3327638348108618', '3350154612596512']],
            15 => ['ketua' => '3377010381848788', 'anggota' => ['3327553344954898', '3329893868439357']],
            16 => ['ketua' => '3382483291642301', 'anggota' => ['3334916829564468', '3341234539733970']],
            17 => ['ketua' => '3384814582224790', 'anggota' => ['3341427383435468', '3347050661695715']],
            18 => ['ketua' => '3301932287194672', 'anggota' => ['3351700760065891', '3352456378986063']],
            19 => ['ketua' => '3377660731480056', 'anggota' => ['3362843448996565', '3367725819004675']],
        ];

        foreach ($insertedIds as $index => $pengabdianId) {
            $team = $teamMapping[$index];

            // Ketua
            $pengabdianDosenData[] = [
                'id_pengabdian' => $pengabdianId,
                'nik' => $team['ketua'],
                'status_anggota' => 'Ketua'
            ];

            // Anggota
            foreach ($team['anggota'] as $anggotaNik) {
                $pengabdianDosenData[] = [
                    'id_pengabdian' => $pengabdianId,
                    'nik' => $anggotaNik,
                    'status_anggota' => 'Anggota'
                ];
            }
        }

        DB::table('pengabdian_dosen')->insert($pengabdianDosenData);

        // Pengabdian Mahasiswa
        $pengabdianMahasiswaData = [];
        $mahasiswaNIMs = ['2021001', '2021002', '2021003', '2021004', '2021005', '2021006', '2021007', '2021008'];

        foreach ($insertedIds as $pengabdianId) {
            // Setiap pengabdian melibatkan 2-3 mahasiswa
            $nimCount = rand(2, 3);
            $selectedNIMs = array_slice($mahasiswaNIMs, 0, $nimCount);

            foreach ($selectedNIMs as $nim) {
                $pengabdianMahasiswaData[] = [
                    'id_pengabdian' => $pengabdianId,
                    'nim' => $nim
                ];
            }

            // Rotate array untuk variasi
            $first = array_shift($mahasiswaNIMs);
            $mahasiswaNIMs[] = $first;
        }

        DB::table('pengabdian_mahasiswa')->insert($pengabdianMahasiswaData);

        // Mitra
        $mitraNames = [
            ['nama_mitra' => 'Dinas Komunikasi dan Informatika Kota Semarang', 'lokasi_mitra' => 'Kota Semarang'],
            ['nama_mitra' => 'Karang Taruna Desa Ngaliyan', 'lokasi_mitra' => 'Desa Ngaliyan, Semarang'],
            ['nama_mitra' => 'Koperasi Serba Usaha Makmur Jaya', 'lokasi_mitra' => 'Kecamatan Tembalang'],
            ['nama_mitra' => 'Kelompok Tani Sumber Rezeki', 'lokasi_mitra' => 'Desa Meteseh, Semarang'],
            ['nama_mitra' => 'SMA Negeri 1 Semarang', 'lokasi_mitra' => 'Kota Semarang'],
            ['nama_mitra' => 'Puskesmas Rowosari', 'lokasi_mitra' => 'Kecamatan Tembalang'],
            ['nama_mitra' => 'Koperasi Sejahtera Mandiri', 'lokasi_mitra' => 'Kecamatan Banyumanik'],
            ['nama_mitra' => 'Dinas Pemberdayaan Masyarakat Desa', 'lokasi_mitra' => 'Kabupaten Semarang'],
            ['nama_mitra' => 'SMK Teknologi Informatika Semarang', 'lokasi_mitra' => 'Kota Semarang'],
            ['nama_mitra' => 'Dinas Lingkungan Hidup Kota Semarang', 'lokasi_mitra' => 'Kota Semarang'],
            ['nama_mitra' => 'Balai Penyuluhan Pertanian Kecamatan Ungaran', 'lokasi_mitra' => 'Kabupaten Semarang'],
            ['nama_mitra' => 'Asosiasi UMKM Digital Jawa Tengah', 'lokasi_mitra' => 'Provinsi Jawa Tengah'],
            ['nama_mitra' => 'Komunitas Data Science Indonesia Chapter Semarang', 'lokasi_mitra' => 'Kota Semarang'],
            ['nama_mitra' => 'RS Dr. Kariadi Semarang', 'lokasi_mitra' => 'Kota Semarang'],
            ['nama_mitra' => 'Techno Park Universitas Diponegoro', 'lokasi_mitra' => 'Tembalang, Semarang'],
            ['nama_mitra' => 'Dinas Pariwisata Kabupaten Semarang', 'lokasi_mitra' => 'Kabupaten Semarang'],
            ['nama_mitra' => 'Dinas Perhubungan Kota Semarang', 'lokasi_mitra' => 'Kota Semarang'],
            ['nama_mitra' => 'Kawasan Industri Candi Semarang', 'lokasi_mitra' => 'Kecamatan Candi'],
            ['nama_mitra' => 'Forum Komunikasi Pengusaha Jawa Tengah', 'lokasi_mitra' => 'Provinsi Jawa Tengah'],
            ['nama_mitra' => 'Balai Penelitian Tanaman Sayuran', 'lokasi_mitra' => 'Kabupaten Semarang']
        ];

        $mitraData = [];
        foreach ($insertedIds as $index => $pengabdianId) {
            $mitraData[] = array_merge(
                ['id_pengabdian' => $pengabdianId],
                $mitraNames[$index]
            );
        }

        DB::table('mitra')->insert($mitraData);

        // Sumber Dana
        $sumberDanaData = [];
        foreach ($insertedIds as $pengabdianId) {
            $isEksternal = rand(0, 1);
            $jumlahDana = rand(10000000, 50000000);

            $sumberDanaData[] = [
                'id_pengabdian' => $pengabdianId,
                'jenis' => $isEksternal ? 'Eksternal' : 'Internal',
                'nama_sumber' => $isEksternal ?
                    (rand(0, 1) ? 'Kemendikbudristek' : 'Kemenristekdikti') :
                    'Dana DIPA UKDW',
                'jumlah_dana' => $jumlahDana
            ];
        }

        DB::table('sumber_dana')->insert($sumberDanaData);

        // Dokumen
        $dokumenData = [];
        foreach ($insertedIds as $index => $pengabdianId) {
            // Proposal
            $dokumenData[] = [
                'id_pengabdian' => $pengabdianId,
                'id_jenis_dokumen' => 1,
                'nama_file' => 'proposal_pengabdian_' . $pengabdianId . '.pdf'
            ];

            // Laporan Akhir untuk 15 pengabdian pertama
            if ($index < 15) {
                $dokumenData[] = [
                    'id_pengabdian' => $pengabdianId,
                    'id_jenis_dokumen' => 2,
                    'nama_file' => 'laporan_akhir_pengabdian_' . $pengabdianId . '.pdf'
                ];
            }
        }

        DB::table('dokumen')->insert($dokumenData);

        // Luaran
        $luaranData = [];
        foreach ($insertedIds as $index => $pengabdianId) {
            // Laporan Akhir
            $luaranData[] = [
                'id_pengabdian' => $pengabdianId,
                'id_jenis_luaran' => 1
            ];

            // Publikasi Jurnal (acak)
            if (rand(0, 1)) {
                $luaranData[] = [
                    'id_pengabdian' => $pengabdianId,
                    'id_jenis_luaran' => 2
                ];
            }

            // HKI untuk beberapa pengabdian
            if (in_array($index, [2, 5, 10, 13, 17])) {
                $luaranData[] = [
                    'id_pengabdian' => $pengabdianId,
                    'id_jenis_luaran' => 5
                ];
            }
        }

        DB::table('luaran')->insert($luaranData);

        echo "Seeder Pengabdian 2024-2025 berhasil dijalankan!\n";
        echo "Total pengabdian ditambahkan: 20 (10 untuk 2024, 10 untuk 2025)\n";
        echo "Pengabdian ID: {$startId} - {$endId}\n";
    }
}
