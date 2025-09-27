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
        Schema::create('sumber_dana', function (Blueprint $table) {
            $table->id('id_sumber_dana');
            $table->unsignedBigInteger('id_pengabdian');
            $table->string('jenis');
            $table->string('nama_sumber');
            $table->double('jumlah_dana');
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
        Schema::dropIfExists('sumber_dana');
    }
};
