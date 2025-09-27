<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            // tambahkan kolom path_file
            $table->string('path_file')->nullable()->after('nama_file');

            // aktifkan timestamps (created_at & updated_at)
            if (!Schema::hasColumn('dokumen', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            $table->dropColumn('path_file');
            $table->dropTimestamps();
        });
    }
};
