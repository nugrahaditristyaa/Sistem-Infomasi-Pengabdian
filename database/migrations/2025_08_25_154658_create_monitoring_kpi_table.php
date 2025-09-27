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
        Schema::create('monitoring_kpi', function (Blueprint $table) {
            $table->id('id_monitoring');
            $table->unsignedBigInteger('id_kpi');
            $table->unsignedBigInteger('id_pengabdian');
            $table->year('tahun');
            $table->integer('nilai_capai');
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('id_kpi')->references('id_kpi')->on('kpi')->onDelete('cascade');
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
        Schema::dropIfExists('monitoring_kpi');
    }
};
