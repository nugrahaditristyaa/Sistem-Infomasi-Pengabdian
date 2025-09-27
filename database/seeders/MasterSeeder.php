<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('jenis_luaran')->insert([
            ['nama_jenis_luaran' => 'Laporan Akhir'],
            ['nama_jenis_luaran' => 'Jurnal Nasional'],
            ['nama_jenis_luaran' => 'Jurnal Internasional'],
            ['nama_jenis_luaran' => 'Buku'],
            ['nama_jenis_luaran' => 'HKI'],
            ['nama_jenis_luaran' => 'Lainnya'],
        ]);

        DB::table('kategori_spmi')->insert([
            ['kode_spmi' => 'SPMI-PSM', 'deskripsi' => 'Penyelesaian masalah masyarakat'],
            ['kode_spmi' => 'SPMI-TTG', 'deskripsi' => 'Teknologi tepat guna'],
            ['kode_spmi' => 'SPMI-IPTEK', 'deskripsi' => 'Bahan pengembangan iptek'],
            ['kode_spmi' => 'SPMI-PUB', 'deskripsi' => 'Publikasi/diseminasi'],
            ['kode_spmi' => 'SPMI-BA', 'deskripsi' => 'Bahan ajar/modul pelatihan'],
        ]);

        DB::table('jenis_dokumen')->insert([
            ['nama_jenis_dokumen' => 'Proposal'],
            ['nama_jenis_dokumen' => 'Laporan Akhir'],
            ['nama_jenis_dokumen' => 'Surat Tugas'],
            ['nama_jenis_dokumen' => 'Surat Permohonan'],
            ['nama_jenis_dokumen' => 'Ucapan Terima Kasih'],
            ['nama_jenis_dokumen' => 'MoU/MoA'],
            ['nama_jenis_dokumen' => 'Bukti Luaran'],
            ['nama_jenis_dokumen' => 'HKI'],
        ]);

        // Target KPI (bisa diedit lewat UI nanti)
        DB::table('kpi')->insert([
            ['kode' => 'KPI-LUARAN-80', 'nama_indikator' => '≥80% PkM memenuhi luaran SPMI', 'target' => 80, 'satuan' => '%'],
            ['kode' => 'KPI-GROW-10', 'nama_indikator' => 'Pelaksana PkM naik ≥10%/tahun', 'target' => 10, 'satuan' => '%'],
            ['kode' => 'KPI-DANA-EXT-30', 'nama_indikator' => '≥30% dana eksternal', 'target' => 30, 'satuan' => '%'],
            ['kode' => 'KPI-DANA-INT-70', 'nama_indikator' => '≥70% dana internal', 'target' => 70, 'satuan' => '%'],
            ['kode' => 'KPI-HKI-1', 'nama_indikator' => '≥1 HKI per prodi per tahun', 'target' => 1, 'satuan' => 'buah'],
            ['kode' => 'KPI-MHS-70', 'nama_indikator' => '≥70% PkM libatkan ≥1 mahasiswa', 'target' => 70, 'satuan' => '%'],
            ['kode' => 'KPI-INFOKOM-70', 'nama_indikator' => '≥70% PkM bidang INFOKOM', 'target' => 70, 'satuan' => '%'],
        ]);
    }
}
