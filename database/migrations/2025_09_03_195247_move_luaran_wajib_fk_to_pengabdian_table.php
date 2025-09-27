<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Karena kolom tidak lagi dibuat di tabel 'luaran',
        // tugas migrasi ini sekarang hanya menambahkannya ke tabel 'pengabdian'.
        if (!Schema::hasColumn('pengabdian', 'id_luaran_wajib')) {
            Schema::table('pengabdian', function (Blueprint $table) {
                $table->unsignedBigInteger('id_luaran_wajib')->nullable()->after('judul_pengabdian');
                
                // ==========================================================
                // === PERBAIKAN: Referensi ke kolom 'id' yang benar ===
                // ==========================================================
                $table->foreign('id_luaran_wajib')
                      ->references('id') // Mengacu pada primary key standar ('id')
                      ->on('luaran_wajib')
                      ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Logika untuk membatalkan (rollback) migrasi
        if (Schema::hasColumn('pengabdian', 'id_luaran_wajib')) {
            Schema::table('pengabdian', function (Blueprint $table) {
                $table->dropForeign(['id_luaran_wajib']);
                $table->dropColumn('id_luaran_wajib');
            });
        }
    }
};