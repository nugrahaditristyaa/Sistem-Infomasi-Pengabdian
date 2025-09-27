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
        Schema::create('mitra', function (Blueprint $table) {
            $table->id('id_mitra');
            $table->unsignedBigInteger('id_pengabdian');
            $table->string('nama_mitra');
            $table->string('lokasi_mitra')->nullable();
            $table->timestamps();

            $table->foreign('id_pengabdian')->references('id_pengabdian')->on('pengabdian')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mitra');
    }
};
