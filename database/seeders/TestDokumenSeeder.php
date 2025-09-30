<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\JenisDokumen;
use App\Models\Pengabdian;

class TestDokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Menambahkan beberapa dokumen untuk test kelengkapan
     */
    public function run()
    {
        $this->command->info('Menambahkan dokumen test untuk kelengkapan...');

        // Get required document types
        $requiredDocs = [
            'Surat Tugas Dosen',
            'Surat Permohonan',
            'Surat Ucapan Terima Kasih',
            'MoU/MoA/Dokumen Kerja Sama Kegiatan',
            'Laporan Akhir'
        ];

        $jenisDokumen = JenisDokumen::whereIn('nama_jenis_dokumen', $requiredDocs)
            ->get()
            ->keyBy('nama_jenis_dokumen');

        // Get first 3 pengabdian untuk test
        $pengabdianList = Pengabdian::take(3)->get();

        foreach ($pengabdianList as $index => $pengabdian) {
            $this->command->info("Menambahkan dokumen untuk Pengabdian ID: {$pengabdian->id_pengabdian}");

            // Pengabdian pertama: hanya Laporan Akhir (tidak lengkap)
            if ($index === 0) {
                $this->addDocument($pengabdian->id_pengabdian, $jenisDokumen['Laporan Akhir']->id_jenis_dokumen, 'Laporan Akhir');
            }
            // Pengabdian kedua: 3 dokumen (tidak lengkap)  
            elseif ($index === 1) {
                $this->addDocument($pengabdian->id_pengabdian, $jenisDokumen['Laporan Akhir']->id_jenis_dokumen, 'Laporan Akhir');
                $this->addDocument($pengabdian->id_pengabdian, $jenisDokumen['Surat Tugas Dosen']->id_jenis_dokumen, 'Surat Tugas Dosen');
                $this->addDocument($pengabdian->id_pengabdian, $jenisDokumen['Surat Permohonan']->id_jenis_dokumen, 'Surat Permohonan');
            }
            // Pengabdian ketiga: semua 5 dokumen (lengkap)
            elseif ($index === 2) {
                foreach ($requiredDocs as $docName) {
                    if (isset($jenisDokumen[$docName])) {
                        $this->addDocument($pengabdian->id_pengabdian, $jenisDokumen[$docName]->id_jenis_dokumen, $docName);
                    }
                }
            }
        }

        $this->command->info('Dokumen test berhasil ditambahkan!');
        $this->command->info('- Pengabdian 1: Hanya Laporan Akhir (1/5 dokumen)');
        $this->command->info('- Pengabdian 2: 3 dokumen (3/5 dokumen)');
        $this->command->info('- Pengabdian 3: Semua dokumen (5/5 dokumen)');
    }

    private function addDocument($pengabdianId, $jenisDokumenId, $namaFile)
    {
        DB::table('dokumen')->insert([
            'id_pengabdian' => $pengabdianId,
            'id_jenis_dokumen' => $jenisDokumenId,
            'nama_file' => $namaFile . '_' . $pengabdianId . '.pdf',
            'url_file' => '/storage/dokumen/' . strtolower(str_replace(' ', '_', $namaFile)) . '_' . $pengabdianId . '.pdf',
            'ukuran_file' => rand(500000, 2000000), // 500KB - 2MB
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
