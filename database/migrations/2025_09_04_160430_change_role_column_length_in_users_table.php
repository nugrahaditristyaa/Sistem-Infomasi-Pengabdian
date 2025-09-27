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
        // Mengubah kolom 'role' agar bisa menampung hingga 50 karakter
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Mengembalikan ke state semula jika diperlukan (opsional)
        Schema::table('users', function (Blueprint $table) {
            // Asumsi sebelumnya adalah VARCHAR(20), sesuaikan jika berbeda
            $table->string('role', 20)->change();
        });
    }
};