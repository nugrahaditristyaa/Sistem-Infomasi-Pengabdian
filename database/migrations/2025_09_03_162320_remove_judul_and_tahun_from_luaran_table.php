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
        Schema::table('luaran', function (Blueprint $table) {
             $table->dropColumn(['judul', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('luaran', function (Blueprint $table) {
            $table->string('judul')->nullable()->after('id_jenis_luaran');
            $table->year('tahun')->nullable()->after('judul');
        });
    }
};
