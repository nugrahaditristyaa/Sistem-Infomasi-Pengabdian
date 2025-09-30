<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FixTimestampsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Memperbaiki data pengabdian yang memiliki created_at null
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Memperbaiki timestamp pengabdian yang null...');

        // Ambil semua pengabdian yang created_at nya null
        $pengabdianNull = DB::table('pengabdian')->whereNull('created_at')->get();

        $this->command->info("Ditemukan {$pengabdianNull->count()} pengabdian dengan created_at null");

        if ($pengabdianNull->count() > 0) {
            $baseDate = Carbon::create(2023, 1, 1); // Mulai dari tanggal dasar
            $updated = 0;

            foreach ($pengabdianNull as $index => $pengabdian) {
                // Generate tanggal yang berbeda untuk setiap record
                $createdAt = $baseDate->copy()->addDays($index)->addHours(rand(8, 17))->addMinutes(rand(0, 59));
                $updatedAt = $createdAt->copy()->addDays(rand(1, 30))->addHours(rand(1, 5));

                DB::table('pengabdian')
                    ->where('id_pengabdian', $pengabdian->id_pengabdian)
                    ->update([
                        'created_at' => $createdAt,
                        'updated_at' => $updatedAt
                    ]);

                $updated++;
            }

            $this->command->info("Berhasil memperbarui {$updated} record pengabdian");
        }

        // Juga perbaiki data terkait lainnya yang mungkin null
        $this->fixLuaranTimestamps();
        $this->fixDokumenTimestamps();

        $this->command->info('Semua timestamp berhasil diperbaiki!');
    }

    private function fixLuaranTimestamps()
    {
        $luaranNull = DB::table('luaran')->whereNull('created_at')->count();

        if ($luaranNull > 0) {
            $this->command->info("Memperbaiki {$luaranNull} luaran dengan created_at null");

            DB::table('luaran')
                ->whereNull('created_at')
                ->update([
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
        }
    }

    private function fixDokumenTimestamps()
    {
        $dokumenNull = DB::table('dokumen')->whereNull('created_at')->count();

        if ($dokumenNull > 0) {
            $this->command->info("Memperbaiki {$dokumenNull} dokumen dengan created_at null");

            DB::table('dokumen')
                ->whereNull('created_at')
                ->update([
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
        }
    }
}
