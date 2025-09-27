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
            // Perintah untuk mengubah nama kolom 'deskripsi' menjadi 'nama_luaran'
            $table->renameColumn('deskripsi', 'nama_luaran');
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
            // Perintah untuk mengembalikan nama kolom jika migrasi dibatalkan
            $table->renameColumn('nama_luaran', 'deskripsi');
        });
    }
};