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
        Schema::create('pengabdian', function (Blueprint $table) {
            $table->id('id_pengabdian');
            $table->string('ketua_pengabdian')->nullable();
            $table->foreign('ketua_pengabdian')->references('nik')->on('dosen')->onDelete('cascade');
            $table->string('judul_pengabdian');
            $table->date('tanggal_pengabdian');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengabdian');
    }
};
