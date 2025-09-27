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
        Schema::create('detail_hki', function (Blueprint $table) {
            $table->id('id_detail_hki');
            $table->unsignedBigInteger('id_luaran');
            $table->string('no_pendaftaran');
            $table->date('tgl_permohonan');
            $table->string('judul_ciptaan');
            $table->string('pemegang_hak_cipta');
            $table->string('jenis_ciptaan');
            $table->timestamps();

            $table->foreign('id_luaran')->references('id_luaran')->on('luaran')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_hki');
    }
};
