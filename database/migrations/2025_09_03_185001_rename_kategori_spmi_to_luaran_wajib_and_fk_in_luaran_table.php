<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <-- PERBAIKAN: Menambahkan baris ini

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Mengganti nama tabel dari 'kategori_spmi' menjadi 'luaran_wajib'
        Schema::rename('kategori_spmi', 'luaran_wajib');

        // Menambahkan foreign key ke tabel luaran (SEHARUSNYA TIDAK DILAKUKAN DI SINI)
        Schema::table('luaran', function (Blueprint $table) {
            // ==========================================================
            // === BAGIAN INI YANG HARUS DIJADIKAN KOMENTAR ===
            // ==========================================================
            // Baris ini yang menyebabkan error berulang kali, karena kolom ini seharusnya
            // langsung ditambahkan ke tabel 'pengabdian', bukan 'luaran'.
            // $table->foreignId('id_luaran_wajib')->nullable()->constrained('luaran_wajib')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Logika untuk membatalkan (rollback) migrasi
        Schema::table('luaran', function (Blueprint $table) {
            // Cek jika constraint ada sebelum menghapus untuk keamanan
            $foreignKeys = collect(DB::select(DB::raw('SHOW CREATE TABLE luaran')))->first()->{'Create Table'};
            if (str_contains($foreignKeys, 'luaran_id_luaran_wajib_foreign')) {
                $table->dropForeign(['id_luaran_wajib']);
                $table->dropColumn('id_luaran_wajib');
            }
        });

        Schema::rename('luaran_wajib', 'kategori_spmi');
    }
};