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
        // Menghapus kolom foreign key id_kategori_spmi dari tabel luaran
        if (Schema::hasColumn('luaran', 'id_kategori_spmi')) {
            Schema::table('luaran', function (Blueprint $table) {
                // Pertama, hapus foreign key constraint-nya
                // Laravel biasanya menamainya: namatabel_namakolom_foreign
                $table->dropForeign(['id_kategori_spmi']);

                // Kedua, hapus kolomnya
                $table->dropColumn('id_kategori_spmi');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Jika di-rollback, buat kembali kolom dan foreign key-nya
        if (!Schema::hasColumn('luaran', 'id_kategori_spmi')) {
            Schema::table('luaran', function (Blueprint $table) {
                // Tipe data disesuaikan dengan asumsi tabel kategori_spmi
                // Jika error, sesuaikan tipe data di sini
                $table->unsignedBigInteger('id_kategori_spmi')->nullable();
                $table->foreign('id_kategori_spmi')
                      ->references('id_kategori_spmi')
                      ->on('kategori_spmi') // Asumsi nama tabel lama
                      ->onDelete('set null');
            });
        }
    }
};