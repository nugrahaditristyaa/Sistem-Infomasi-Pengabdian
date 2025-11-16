<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration attempts to safely remove the foreign key(s) that reference
     * the luaran_wajib table, drop the id_luaran_wajib column on pengabdian (if present),
     * and drop the luaran_wajib table.
     *
     * It includes runtime checks to avoid failures on databases that are already
     * partially modified (based on logs in the repo).
     */
    public function up(): void
    {
        $dbName = DB::getDatabaseName();

        // 1) Drop foreign key constraints on pengabdian that reference luaran_wajib
        if (Schema::hasTable('pengabdian')) {
            $constraints = DB::select(
                "SELECT constraint_name FROM information_schema.key_column_usage WHERE table_schema = ? AND table_name = 'pengabdian' AND referenced_table_name = 'luaran_wajib' AND referenced_column_name = 'id_luaran_wajib'",
                [$dbName]
            );

            foreach ($constraints as $constraint) {
                $name = $constraint->constraint_name;
                // Use raw statement if Laravel's dropForeign by name fails in older versions
                try {
                    Schema::table('pengabdian', function (Blueprint $table) use ($name) {
                        // dropForeign accepts the constraint name as string
                        $table->dropForeign($name);
                    });
                } catch (\Exception $e) {
                    // fallback: try raw SQL
                    DB::statement("ALTER TABLE `pengabdian` DROP FOREIGN KEY `" . $name . "`");
                }
            }

            // 2) Drop the column if it exists
            if (Schema::hasColumn('pengabdian', 'id_luaran_wajib')) {
                Schema::table('pengabdian', function (Blueprint $table) {
                    // make sure it's nullable before dropping in case of FK constraints
                    $table->dropColumn('id_luaran_wajib');
                });
            }
        }

        // 3) Drop the luaran_wajib table if it exists
        if (Schema::hasTable('luaran_wajib')) {
            Schema::dropIfExists('luaran_wajib');
        }
    }

    /**
     * Reverse the migrations.
     *
     * Recreates the luaran_wajib table and the column + FK on pengabdian.
     * Note: the recreated table will be empty (no seed data). If you need the
     * original data back, restore from backup before running the down() method.
     */
    public function down(): void
    {
        // Recreate table
        if (!Schema::hasTable('luaran_wajib')) {
            Schema::create('luaran_wajib', function (Blueprint $table) {
                $table->increments('id_luaran_wajib');
                $table->string('nama_luaran');
                $table->timestamps();
            });
        }

        // Add column back to pengabdian and FK
        if (Schema::hasTable('pengabdian') && !Schema::hasColumn('pengabdian', 'id_luaran_wajib')) {
            Schema::table('pengabdian', function (Blueprint $table) {
                $table->unsignedInteger('id_luaran_wajib')->nullable()->after('ketua_pengabdian');
                $table->foreign('id_luaran_wajib')->references('id_luaran_wajib')->on('luaran_wajib')->onDelete('set null');
            });
        }
    }
};
