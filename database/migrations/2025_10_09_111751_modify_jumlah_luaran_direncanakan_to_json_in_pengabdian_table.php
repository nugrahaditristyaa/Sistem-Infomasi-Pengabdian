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
            // Mengubah kolom jumlah_luaran_direncanakan dari integer ke JSON
            $table->json('jumlah_luaran_direncanakan')->nullable()->change();
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
            // Mengembalikan ke integer
            $table->unsignedInteger('jumlah_luaran_direncanakan')->default(0)->change();
        });
    }
};
