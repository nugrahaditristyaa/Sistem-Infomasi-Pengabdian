<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HkiDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Melengkapi data HKI untuk pengabdian yang memiliki luaran jenis HKI
     *
     * @return void
     */
    public function run()
    {
        // Data luaran HKI yang perlu dilengkapi
        $hkiLuaranData = [
            [
                'id_luaran' => 75,
                'id_pengabdian' => 68,
                'judul_pengabdian' => 'Pengembangan Sistem E-Commerce Terintegrasi untuk Produk Lokal UMKM',
                'ketua_pengabdian' => '3309220560774465'
            ],
            [
                'id_luaran' => 82,
                'id_pengabdian' => 71,
                'judul_pengabdian' => 'Implementasi Aplikasi Mobile untuk Layanan Kesehatan Desa',
                'ketua_pengabdian' => '3315739107503323'
            ],
            [
                'id_luaran' => 91,
                'id_pengabdian' => 76,
                'judul_pengabdian' => 'Implementasi Artificial Intelligence untuk Prediksi Cuaca dalam Pertanian',
                'ketua_pengabdian' => '3359053194521451'
            ],
            [
                'id_luaran' => 96,
                'id_pengabdian' => 79,
                'judul_pengabdian' => 'Sistem Monitoring Kesehatan Real-time Berbasis Wearable Technology',
                'ketua_pengabdian' => '3372632676745389'
            ],
            [
                'id_luaran' => 103,
                'id_pengabdian' => 83,
                'judul_pengabdian' => 'Pengembangan Digital Twin untuk Optimasi Proses Manufaktur UMKM',
                'ketua_pengabdian' => '3384814582224790'
            ]
        ];

        // Detail HKI data
        $detailHkiData = [];
        $anggotaHkiData = [];

        foreach ($hkiLuaranData as $index => $hkiData) {
            // Generate nomor pendaftaran HKI (format: EC00YYYYNNNNNN)
            $tahun = $hkiData['id_pengabdian'] <= 75 ? 2024 : 2025;
            $nomorUrut = str_pad($index + 1, 6, '0', STR_PAD_LEFT);
            $noPendaftaran = "EC00{$tahun}{$nomorUrut}";

            // Generate tanggal permohonan (random dalam tahun yang sesuai)
            $startDate = Carbon::create($tahun, 1, 1);
            $endDate = Carbon::create($tahun, 12, 31);
            $tglPermohonan = $startDate->addDays(rand(0, $startDate->diffInDays($endDate)))->format('Y-m-d');

            // Tentukan judul ciptaan berdasarkan judul pengabdian
            $judulCiptaan = match ($hkiData['id_pengabdian']) {
                68 => 'Sistem E-Commerce Terintegrasi untuk Produk Lokal UMKM',
                71 => 'Aplikasi Mobile Layanan Kesehatan Desa (HealthCare Village)',
                76 => 'Sistem Prediksi Cuaca Berbasis Artificial Intelligence untuk Pertanian',
                79 => 'Aplikasi Monitoring Kesehatan Real-time dengan Wearable Technology',
                83 => 'Sistem Digital Twin untuk Optimasi Proses Manufaktur UMKM'
            };

            // Insert detail HKI
            $detailHkiId = DB::table('detail_hki')->insertGetId([
                'id_luaran' => $hkiData['id_luaran'],
                'no_pendaftaran' => $noPendaftaran,
                'tgl_permohonan' => $tglPermohonan,
                'judul_ciptaan' => $judulCiptaan,
                'pemegang_hak_cipta' => 'Universitas Kristen Duta Wacana',
                'jenis_ciptaan' => 'Program Komputer',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            echo "Detail HKI ID {$detailHkiId} berhasil ditambahkan untuk luaran ID {$hkiData['id_luaran']}\n";

            // Ambil anggota tim pengabdian untuk dijadikan anggota HKI
            $anggotaTim = DB::table('pengabdian_dosen')
                ->where('id_pengabdian', $hkiData['id_pengabdian'])
                ->orderBy('status_anggota', 'desc') // Ketua dulu, baru anggota
                ->get(['nik', 'status_anggota']);

            // Insert anggota HKI
            foreach ($anggotaTim as $anggotaIndex => $anggota) {
                $peranHki = match ($anggota->status_anggota) {
                    'Ketua' => 'Pencipta Utama',
                    'Anggota' => $anggotaIndex == 1 ? 'Pencipta Pendamping' : 'Pencipta Pendamping ' . ($anggotaIndex)
                };

                DB::table('anggota_hki')->insert([
                    'id_detail_hki' => $detailHkiId,
                    'nik' => $anggota->nik,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        echo "\n=== RINGKASAN DATA HKI YANG DITAMBAHKAN ===\n";
        echo "Total Detail HKI: " . count($hkiLuaranData) . "\n";
        echo "Pengabdian dengan HKI:\n";

        foreach ($hkiLuaranData as $hki) {
            $tahun = $hki['id_pengabdian'] <= 75 ? 2024 : 2025;
            echo "- ID {$hki['id_pengabdian']} ({$tahun}): " . substr($hki['judul_pengabdian'], 0, 50) . "...\n";
        }

        echo "\nData HKI berhasil dilengkapi!\n";
        echo "Sekarang data HKI dapat ditampilkan di halaman admin.\n";
    }
}
