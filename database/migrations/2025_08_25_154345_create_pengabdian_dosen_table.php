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
        Schema::create('pengabdian_dosen', function (Blueprint $table) {
            $table->id();

            // FK ke pengabdian
            $table->unsignedBigInteger('id_pengabdian');
            $table->foreign('id_pengabdian')
                ->references('id_pengabdian')->on('pengabdian')
                ->onDelete('cascade');

            // FK ke dosen (pakai nik karena PK dosen adalah string)
            $table->string('nik');
            $table->foreign('nik')
                ->references('nik')->on('dosen')
                ->onDelete('cascade');

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
        Schema::dropIfExists('pengabdian_dosen');
    }
};
