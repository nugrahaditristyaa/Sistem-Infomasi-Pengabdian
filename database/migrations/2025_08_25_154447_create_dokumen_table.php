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
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id('id_dokumen');
            $table->unsignedBigInteger('id_pengabdian');
            $table->unsignedBigInteger('id_jenis_dokumen');
            $table->string('nama_file');
            $table->timestamps();

            $table->foreign('id_pengabdian')->references('id_pengabdian')->on('pengabdian')->onDelete('cascade');
            $table->foreign('id_jenis_dokumen')->references('id_jenis_dokumen')->on('jenis_dokumen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dokumen');
    }
};
