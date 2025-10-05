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
        Schema::table('pengabdian', function (Blueprint $table) {
            // Menambahkan satu kolom terpenting untuk KPI Capaian Luaran 80%
            $table->unsignedInteger('jumlah_luaran_direncanakan')->default(0)->comment('Total luaran yang dijanjikan di proposal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengabdian', function (Blueprint $table) {
            // Menghapus kolom jika migrasi dibatalkan
            $table->dropColumn('jumlah_luaran_direncanakan');
        });
    }
};