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
        Schema::table('luaran_wajib', function (Blueprint $table) {
            // ==========================================================
            // === PERBAIKAN FINAL: Ganti nama kolom PK yang salah ===
            // ==========================================================
            // Kita ganti nama primary key dari nama lama ('id_kategori_spmi') ke nama standar ('id')
            $table->renameColumn('id_kategori_spmi', 'id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('luaran_wajib', function (Blueprint $table) {
            // Kembalikan nama kolom ke kondisi semula saat rollback
            $table->renameColumn('id', 'id_kategori_spmi');
        });
    }
};