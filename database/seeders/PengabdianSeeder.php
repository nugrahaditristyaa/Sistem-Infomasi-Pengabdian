<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengabdianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pengabdian')->insert([
            [
                'id_pengabdian' => 1,
                'judul_pengabdian' => 'Pengembangan Sistem Informasi Desa Cerdas untuk Desa Sukamaju',
                'tanggal_pengabdian' => '2024-03-15'
            ],
            [
                'id_pengabdian' => 2,
                'judul_pengabdian' => 'Pelatihan Literasi Digital untuk Guru SD di Kecamatan Semarang Utara',
                'tanggal_pengabdian' => '2024-04-20'
            ],
            [
                'id_pengabdian' => 3,
                'judul_pengabdian' => 'Implementasi Teknologi IoT untuk Monitoring Kualitas Air di Sungai Banjir Kanal',
                'tanggal_pengabdian' => '2024-05-10'
            ],
            [
                'id_pengabdian' => 4,
                'judul_pengabdian' => 'Pengembangan Aplikasi Mobile untuk UMKM di Kota Semarang',
                'tanggal_pengabdian' => '2024-06-05'
            ],
            [
                'id_pengabdian' => 5,
                'judul_pengabdian' => 'Pelatihan Keamanan Siber untuk Aparatur Desa',
                'tanggal_pengabdian' => '2024-07-12'
            ]
        ]);

        // Pengabdian Dosen
        DB::table('pengabdian_dosen')->insert([
            ['id_pengabdian' => 1, 'nik' => '198501012010012001', 'status_anggota' => 'Ketua'],
            ['id_pengabdian' => 1, 'nik' => '198502152010012002', 'status_anggota' => 'Anggota'],
            ['id_pengabdian' => 2, 'nik' => '198603202010012003', 'status_anggota' => 'Ketua'],
            ['id_pengabdian' => 2, 'nik' => '198704102010012004', 'status_anggota' => 'Anggota'],
            ['id_pengabdian' => 3, 'nik' => '198805052010012005', 'status_anggota' => 'Ketua'],
            ['id_pengabdian' => 3, 'nik' => '198501012010012001', 'status_anggota' => 'Anggota'],
            ['id_pengabdian' => 4, 'nik' => '198502152010012002', 'status_anggota' => 'Ketua'],
            ['id_pengabdian' => 5, 'nik' => '198603202010012003', 'status_anggota' => 'Ketua']
        ]);

        // Pengabdian Mahasiswa
        DB::table('pengabdian_mahasiswa')->insert([
            ['id_pengabdian' => 1, 'nim' => '2021001'],
            ['id_pengabdian' => 1, 'nim' => '2021002'],
            ['id_pengabdian' => 2, 'nim' => '2021003'],
            ['id_pengabdian' => 2, 'nim' => '2021004'],
            ['id_pengabdian' => 3, 'nim' => '2021005'],
            ['id_pengabdian' => 4, 'nim' => '2021006'],
            ['id_pengabdian' => 5, 'nim' => '2021007']
        ]);

        // Mitra
        DB::table('mitra')->insert([
            ['id_pengabdian' => 1, 'nama_mitra' => 'Pemerintah Desa Sukamaju', 'lokasi_mitra' => 'Desa Sukamaju, Semarang'],
            ['id_pengabdian' => 2, 'nama_mitra' => 'Dinas Pendidikan Kota Semarang', 'lokasi_mitra' => 'Kota Semarang'],
            ['id_pengabdian' => 3, 'nama_mitra' => 'Dinas Lingkungan Hidup Kota Semarang', 'lokasi_mitra' => 'Kota Semarang'],
            ['id_pengabdian' => 4, 'nama_mitra' => 'Dinas Koperasi dan UMKM Kota Semarang', 'lokasi_mitra' => 'Kota Semarang'],
            ['id_pengabdian' => 5, 'nama_mitra' => 'Pemerintah Kecamatan Semarang Tengah', 'lokasi_mitra' => 'Kecamatan Semarang Tengah']
        ]);

        // Sumber Dana
        DB::table('sumber_dana')->insert([
            ['id_pengabdian' => 1, 'jenis' => 'Internal', 'nama_sumber' => 'Dana DIPA UKDW', 'jumlah_dana' => 15000000],
            ['id_pengabdian' => 2, 'jenis' => 'Eksternal', 'nama_sumber' => 'Kemendikbudristek', 'jumlah_dana' => 25000000],
            ['id_pengabdian' => 3, 'jenis' => 'Internal', 'nama_sumber' => 'Dana DIPA UKDW', 'jumlah_dana' => 12000000],
            ['id_pengabdian' => 4, 'jenis' => 'Eksternal', 'nama_sumber' => 'Kemenristekdikti', 'jumlah_dana' => 30000000],
            ['id_pengabdian' => 5, 'jenis' => 'Internal', 'nama_sumber' => 'Dana DIPA UKDW', 'jumlah_dana' => 8000000]
        ]);

        // Dokumen
        DB::table('dokumen')->insert([
            ['id_pengabdian' => 1, 'id_jenis_dokumen' => 1, 'nama_file' => 'proposal_desa_cerdas.pdf'],
            ['id_pengabdian' => 1, 'id_jenis_dokumen' => 2, 'nama_file' => 'laporan_akhir_desa_cerdas.pdf'],
            ['id_pengabdian' => 2, 'id_jenis_dokumen' => 1, 'nama_file' => 'proposal_literasi_digital.pdf'],
            ['id_pengabdian' => 2, 'id_jenis_dokumen' => 3, 'nama_file' => 'surat_tugas_literasi.pdf'],
            ['id_pengabdian' => 3, 'id_jenis_dokumen' => 1, 'nama_file' => 'proposal_iot_monitoring.pdf']
        ]);

        // Luaran
        DB::table('luaran')->insert([
            ['id_pengabdian' => 1, 'id_kategori_spmi' => 1, 'id_jenis_luaran' => 1, 'judul' => 'Laporan Akhir Pengembangan Sistem Informasi Desa Cerdas', 'tahun' => 2024],
            ['id_pengabdian' => 1, 'id_kategori_spmi' => 2, 'id_jenis_luaran' => 2, 'judul' => 'Jurnal Nasional: Sistem Informasi Desa Cerdas', 'tahun' => 2024],
            ['id_pengabdian' => 2, 'id_kategori_spmi' => 4, 'id_jenis_luaran' => 1, 'judul' => 'Laporan Akhir Pelatihan Literasi Digital', 'tahun' => 2024],
            ['id_pengabdian' => 3, 'id_kategori_spmi' => 2, 'id_jenis_luaran' => 5, 'judul' => 'HKI: Sistem Monitoring Kualitas Air IoT', 'tahun' => 2024],
            ['id_pengabdian' => 4, 'id_kategori_spmi' => 1, 'id_jenis_luaran' => 1, 'judul' => 'Laporan Akhir Pengembangan Aplikasi Mobile UMKM', 'tahun' => 2024]
        ]);

        // Detail HKI
        DB::table('detail_hki')->insert([
            [
                'id_luaran' => 4,
                'no_pendaftaran' => 'EC002024123456',
                'tgl_permohonan' => '2024-12-01',
                'judul_ciptaan' => 'Sistem Monitoring Kualitas Air Berbasis IoT',
                'pemegang_hak_cipta' => 'Universitas Kristen Duta Wacana',
                'jenis_ciptaan' => 'Program Komputer'
            ]
        ]);

        // Anggota HKI
        DB::table('anggota_hki')->insert([
            ['id_detail_hki' => 1, 'nik' => '198805052010012005', 'peran' => 'Pencipta Utama'],
            ['id_detail_hki' => 1, 'nik' => '198501012010012001', 'peran' => 'Pencipta Pendamping']
        ]);

        // Monitoring KPI
        DB::table('monitoring_kpi')->insert([
            ['id_kpi' => 1, 'id_pengabdian' => 1, 'tahun' => 2024, 'nilai_capai' => 85, 'status' => 'Tercapai'],
            ['id_kpi' => 1, 'id_pengabdian' => 2, 'tahun' => 2024, 'nilai_capai' => 90, 'status' => 'Tercapai'],
            ['id_kpi' => 2, 'id_pengabdian' => 1, 'tahun' => 2024, 'nilai_capai' => 15, 'status' => 'Tercapai'],
            ['id_kpi' => 3, 'id_pengabdian' => 2, 'tahun' => 2024, 'nilai_capai' => 35, 'status' => 'Tercapai'],
            ['id_kpi' => 3, 'id_pengabdian' => 4, 'tahun' => 2024, 'nilai_capai' => 40, 'status' => 'Tercapai'],
            ['id_kpi' => 5, 'id_pengabdian' => 3, 'tahun' => 2024, 'nilai_capai' => 1, 'status' => 'Tercapai'],
            ['id_kpi' => 6, 'id_pengabdian' => 1, 'tahun' => 2024, 'nilai_capai' => 100, 'status' => 'Tercapai'],
            ['id_kpi' => 6, 'id_pengabdian' => 2, 'tahun' => 2024, 'nilai_capai' => 100, 'status' => 'Tercapai'],
            ['id_kpi' => 7, 'id_pengabdian' => 1, 'tahun' => 2024, 'nilai_capai' => 80, 'status' => 'Tercapai'],
            ['id_kpi' => 7, 'id_pengabdian' => 4, 'tahun' => 2024, 'nilai_capai' => 85, 'status' => 'Tercapai']
        ]);
    }
}








