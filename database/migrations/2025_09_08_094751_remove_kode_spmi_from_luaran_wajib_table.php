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
            // Perintah untuk menghapus kolom 'kode_spmi'
            $table->dropColumn('kode_spmi');
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
            // Perintah untuk mengembalikan kolom jika migrasi dibatalkan
            $table->string('kode_spmi')->nullable()->after('id_luaran_wajib');
        });
    }
};