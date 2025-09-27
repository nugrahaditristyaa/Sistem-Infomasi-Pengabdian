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
        Schema::table('pengabdian_dosen', function (Blueprint $table) {
            // Tambahkan kolom status_anggota setelah kolom 'nik'
            $table->string('status_anggota')->after('nik');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengabdian_dosen', function (Blueprint $table) {
            // Perintah untuk menghapus kolom jika migrasi di-rollback
            $table->dropColumn('status_anggota');
        });
    }
};