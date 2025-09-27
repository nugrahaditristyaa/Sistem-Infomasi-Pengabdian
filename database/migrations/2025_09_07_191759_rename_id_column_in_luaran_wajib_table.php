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
            // Mengganti nama kolom primary key dari 'id' menjadi 'id_luaran_wajib'
            $table->renameColumn('id', 'id_luaran_wajib');
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
            // Logika untuk membatalkan (rollback), kembalikan nama kolom
            $table->renameColumn('id_luaran_wajib', 'id');
        });
    }
};