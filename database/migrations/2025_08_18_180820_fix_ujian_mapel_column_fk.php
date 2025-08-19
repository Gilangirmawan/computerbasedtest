<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $db = DB::getDatabaseName();
        $hasMapelId = Schema::hasColumn('ujian', 'mapel_id');
        $hasIdMapel = Schema::hasColumn('ujian', 'id_mapel');

        // CASE A: Kedua kolom ada -> satukan ke id_mapel, drop FK/Index mapel_id, lalu drop kolom mapel_id
        if ($hasMapelId && $hasIdMapel) {
            // backfill id_mapel dari mapel_id jika masih null
            DB::statement('UPDATE `ujian` SET `id_mapel` = COALESCE(`id_mapel`, `mapel_id`)');

            // cari & drop FK mapel_id
            $fkName = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', $db)
                ->where('TABLE_NAME', 'ujian')
                ->where('COLUMN_NAME', 'mapel_id')
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->value('CONSTRAINT_NAME');
            if ($fkName) {
                DB::statement("ALTER TABLE `ujian` DROP FOREIGN KEY `$fkName`");
            }

            // cari & drop INDEX mapel_id (hindari PRIMARY)
            $idxName = DB::table('information_schema.STATISTICS')
                ->where('TABLE_SCHEMA', $db)
                ->where('TABLE_NAME', 'ujian')
                ->where('COLUMN_NAME', 'mapel_id')
                ->where('INDEX_NAME', '<>', 'PRIMARY')
                ->value('INDEX_NAME');
            if ($idxName) {
                DB::statement("ALTER TABLE `ujian` DROP INDEX `$idxName`");
            }

            // drop kolom mapel_id
            Schema::table('ujian', function (Blueprint $table) {
                $table->dropColumn('mapel_id');
            });

            // pastikan FK untuk id_mapel ada
            $fkIdMapel = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', $db)
                ->where('TABLE_NAME', 'ujian')
                ->where('COLUMN_NAME', 'id_mapel')
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->value('CONSTRAINT_NAME');
            if (!$fkIdMapel) {
                Schema::table('ujian', function (Blueprint $table) {
                    $table->foreign('id_mapel')->references('id')->on('mapel')->onDelete('cascade');
                });
            }

        // CASE B: Hanya mapel_id -> rename ke id_mapel (drop FK/Index dulu), lalu tambah FK baru
        } elseif ($hasMapelId && !$hasIdMapel) {
            $fkName = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', $db)
                ->where('TABLE_NAME', 'ujian')
                ->where('COLUMN_NAME', 'mapel_id')
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->value('CONSTRAINT_NAME');
            if ($fkName) {
                DB::statement("ALTER TABLE `ujian` DROP FOREIGN KEY `$fkName`");
            }

            $idxName = DB::table('information_schema.STATISTICS')
                ->where('TABLE_SCHEMA', $db)
                ->where('TABLE_NAME', 'ujian')
                ->where('COLUMN_NAME', 'mapel_id')
                ->where('INDEX_NAME', '<>', 'PRIMARY')
                ->value('INDEX_NAME');
            if ($idxName) {
                DB::statement("ALTER TABLE `ujian` DROP INDEX `$idxName`");
            }

            Schema::table('ujian', function (Blueprint $table) {
                $table->renameColumn('mapel_id', 'id_mapel');
            });

            Schema::table('ujian', function (Blueprint $table) {
                $table->foreign('id_mapel')->references('id')->on('mapel')->onDelete('cascade');
            });

        // CASE C: Hanya id_mapel -> pastikan FK ada
        } elseif (!$hasMapelId && $hasIdMapel) {
            $fkIdMapel = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', $db)
                ->where('TABLE_NAME', 'ujian')
                ->where('COLUMN_NAME', 'id_mapel')
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->value('CONSTRAINT_NAME');
            if (!$fkIdMapel) {
                Schema::table('ujian', function (Blueprint $table) {
                    $table->foreign('id_mapel')->references('id')->on('mapel')->onDelete('cascade');
                });
            }

        // CASE D: Tidak ada keduanya (jarang) -> buat id_mapel + FK
        } else {
            Schema::table('ujian', function (Blueprint $table) {
                $table->unsignedBigInteger('id_mapel')->nullable();
            });
            Schema::table('ujian', function (Blueprint $table) {
                $table->foreign('id_mapel')->references('id')->on('mapel')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        $db = DB::getDatabaseName();
        $hasMapelId = Schema::hasColumn('ujian', 'mapel_id');
        $hasIdMapel = Schema::hasColumn('ujian', 'id_mapel');

        // rollback minimal: jika hanya ada id_mapel, rename balik ke mapel_id
        if (!$hasMapelId && $hasIdMapel) {
            // drop FK id_mapel jika ada
            $fkIdMapel = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', $db)
                ->where('TABLE_NAME', 'ujian')
                ->where('COLUMN_NAME', 'id_mapel')
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->value('CONSTRAINT_NAME');
            if ($fkIdMapel) {
                DB::statement("ALTER TABLE `ujian` DROP FOREIGN KEY `$fkIdMapel`");
            }

            Schema::table('ujian', function (Blueprint $table) {
                $table->renameColumn('id_mapel', 'mapel_id');
            });

            Schema::table('ujian', function (Blueprint $table) {
                $table->foreign('mapel_id')->references('id')->on('mapel')->onDelete('cascade');
            });
        }
    }
};
