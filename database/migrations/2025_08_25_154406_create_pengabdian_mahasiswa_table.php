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
        Schema::create('pengabdian_mahasiswa', function (Blueprint $table) {
            $table->id('id_pengabdian_mahasiswa');
            $table->unsignedBigInteger('id_pengabdian');
            $table->string('nim');
            $table->timestamps();

            $table->foreign('id_pengabdian')->references('id_pengabdian')->on('pengabdian')->onDelete('cascade');
            $table->foreign('nim')->references('nim')->on('mahasiswa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengabdian_mahasiswa');
    }
};
