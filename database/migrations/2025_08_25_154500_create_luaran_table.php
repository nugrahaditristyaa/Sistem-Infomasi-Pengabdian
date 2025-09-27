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
        Schema::create('luaran', function (Blueprint $table) {
            $table->id('id_luaran');
            $table->unsignedBigInteger('id_pengabdian');
            $table->unsignedBigInteger('id_kategori_spmi');
            $table->unsignedBigInteger('id_jenis_luaran');
            $table->string('judul');
            $table->year('tahun');
            $table->timestamps();

            $table->foreign('id_pengabdian')->references('id_pengabdian')->on('pengabdian')->onDelete('cascade');
            $table->foreign('id_kategori_spmi')->references('id_kategori_spmi')->on('kategori_spmi');
            $table->foreign('id_jenis_luaran')->references('id_jenis_luaran')->on('jenis_luaran');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('luaran');
    }
};
