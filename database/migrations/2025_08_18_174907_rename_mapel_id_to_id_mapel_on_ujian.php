<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // CASE 1: kedua kolom ada → satukan & hapus mapel_id
        if (Schema::hasColumn('ujian', 'mapel_id') && Schema::hasColumn('ujian', 'id_mapel')) {

            // Backfill: isi id_mapel yang masih NULL dengan mapel_id
            DB::statement('UPDATE ujian SET id_mapel = COALESCE(id_mapel, mapel_id)');

            // Lepas FK/index lama ke mapel_id (nama constraint bisa berbeda, jadi pakai try/catch)
            Schema::table('ujian', function (Blueprint $table) {
                try { $table->dropForeign(['mapel_id']); } catch (\Throwable $e) {}
                try { $table->dropIndex(['mapel_id']); } catch (\Throwable $e) {}
            });

            // Hapus kolom mapel_id
            Schema::table('ujian', function (Blueprint $table) {
                $table->dropColumn('mapel_id');
            });

            // Pastikan FK ke id_mapel ada
            Schema::table('ujian', function (Blueprint $table) {
                try { $table->foreign('id_mapel')->references('id')->on('mapel')->onDelete('cascade'); } catch (\Throwable $e) {}
            });

        // CASE 2: hanya mapel_id ada → rename ke id_mapel
        } elseif (Schema::hasColumn('ujian', 'mapel_id') && !Schema::hasColumn('ujian', 'id_mapel')) {

            Schema::table('ujian', function (Blueprint $table) {
                try { $table->dropForeign(['mapel_id']); } catch (\Throwable $e) {}
            });

            Schema::table('ujian', function (Blueprint $table) {
                $table->renameColumn('mapel_id', 'id_mapel');
            });

            Schema::table('ujian', function (Blueprint $table) {
                try { $table->foreign('id_mapel')->references('id')->on('mapel')->onDelete('cascade'); } catch (\Throwable $e) {}
            });

        // CASE 3: hanya id_mapel ada → pastikan FK ada
        } else {
            if (Schema::hasColumn('ujian', 'id_mapel')) {
                Schema::table('ujian', function (Blueprint $table) {
                    try { $table->foreign('id_mapel')->references('id')->on('mapel')->onDelete('cascade'); } catch (\Throwable $e) {}
                });
            }
        }
    }

    public function down(): void
    {
        // Balikkan seminimal mungkin: jika id_mapel ada dan mapel_id belum ada, rename kembali
        if (Schema::hasColumn('ujian', 'id_mapel') && !Schema::hasColumn('ujian', 'mapel_id')) {
            Schema::table('ujian', function (Blueprint $table) {
                try { $table->dropForeign(['id_mapel']); } catch (\Throwable $e) {}
            });

            Schema::table('ujian', function (Blueprint $table) {
                $table->renameColumn('id_mapel', 'mapel_id');
            });

            Schema::table('ujian', function (Blueprint $table) {
                try { $table->foreign('mapel_id')->references('id')->on('mapel')->onDelete('cascade'); } catch (\Throwable $e) {}
            });
        }
    }
};
