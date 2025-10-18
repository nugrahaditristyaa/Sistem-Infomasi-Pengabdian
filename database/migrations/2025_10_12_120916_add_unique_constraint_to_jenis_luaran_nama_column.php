<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jenis_luaran', function (Blueprint $table) {
            // Menetapkan batasan UNIQUE pada kolom nama_jenis_luaran
            $table->unique('nama_jenis_luaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_luaran', function (Blueprint $table) {
            // Menghapus batasan unique jika migrasi dirollback
            $table->dropUnique(['nama_jenis_luaran']);
        });
    }
};