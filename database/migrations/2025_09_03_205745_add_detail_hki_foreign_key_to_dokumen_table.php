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
        Schema::table('dokumen', function (Blueprint $table) {
            // Tambahkan kolom foreign key baru.
            // Dibuat nullable karena tidak semua baris di tabel 'dokumen' akan berhubungan dengan HKI.
            $table->unsignedBigInteger('id_detail_hki')->nullable()->after('id_pengabdian');

            // Definisikan constraint (aturan) foreign key.
            $table->foreign('id_detail_hki')
                  ->references('id_detail_hki')
                  ->on('detail_hki')
                  ->onDelete('cascade'); // Jika detail hki dihapus, dokumen terkait juga akan ikut terhapus.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dokumen', function (Blueprint $table) {
            // Hapus constraint dan kolomnya jika migrasi di-rollback.
            $table->dropForeign(['id_detail_hki']);
            $table->dropColumn('id_detail_hki');
        });
    }
};

