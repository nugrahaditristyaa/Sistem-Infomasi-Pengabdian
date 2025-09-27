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
        Schema::create('anggota_hki', function (Blueprint $table) {
            $table->id('id_anggota_hki');
            $table->unsignedBigInteger('id_detail_hki');
            $table->string('nik');
            $table->string('peran');
            $table->timestamps();

            $table->foreign('id_detail_hki')->references('id_detail_hki')->on('detail_hki')->onDelete('cascade');
            $table->foreign('nik')->references('nik')->on('dosen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('anggota_hki');
    }
};
