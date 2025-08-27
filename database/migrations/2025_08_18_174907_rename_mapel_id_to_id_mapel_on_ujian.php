<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Kalau FK dgn nama ini sudah ada -> SKIP
        if ($this->fkExists('ujian', 'ujian_id_mapel_foreign')) {
            return; // no-op, biar migrate tidak gagal
        }

        // 2) Kalau ada nama lama, drop dulu (aman kalau tidak ada)
        Schema::table('ujian', function (Blueprint $table) {
            try { $table->dropForeign('ujian_mapel_id_foreign'); } catch (\Throwable $e) {}
            try { $table->dropForeign('ujian_id_mapel_foreign'); } catch (\Throwable $e) {}
        });

        // 3) Pastikan kolom sudah bernama id_mapel
        if (Schema::hasColumn('ujian', 'mapel_id') && !Schema::hasColumn('ujian', 'id_mapel')) {
            // butuh doctrine/dbal jika renameColumn error
            Schema::table('ujian', function (Blueprint $table) {
                $table->renameColumn('mapel_id', 'id_mapel');
            });
        }

        // 4) Tambah FK (nama sama seperti yang diharapkan sistemmu)
        Schema::table('ujian', function (Blueprint $table) {
            $table->foreign('id_mapel', 'ujian_id_mapel_foreign')
                  ->references('id')->on('mapel')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ujian', function (Blueprint $table) {
            try { $table->dropForeign('ujian_id_mapel_foreign'); } catch (\Throwable $e) {}
        });

        // (opsional) balikkan rename kolom
        if (Schema::hasColumn('ujian', 'id_mapel') && !Schema::hasColumn('ujian', 'mapel_id')) {
            try {
                Schema::table('ujian', function (Blueprint $table) {
                    $table->renameColumn('id_mapel', 'mapel_id');
                });
            } catch (\Throwable $e) {}
        }
    }

    private function fkExists(string $table, string $constraint): bool
    {
        return DB::table('information_schema.REFERENTIAL_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::raw('DATABASE()'))
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->exists();
    }
};
