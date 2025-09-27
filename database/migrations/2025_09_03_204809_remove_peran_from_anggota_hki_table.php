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
        // Menghapus kolom 'peran' dari tabel 'anggota_hki'
        Schema::table('anggota_hki', function (Blueprint $table) {
            $table->dropColumn('peran');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Menambahkan kembali kolom 'peran' jika migrasi di-rollback
        Schema::table('anggota_hki', function (Blueprint $table) {
            $table->string('peran')->after('nik');
        });
    }
};
