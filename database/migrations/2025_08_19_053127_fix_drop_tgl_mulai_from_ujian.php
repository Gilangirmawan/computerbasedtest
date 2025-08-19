<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Jika masih ada tgl_mulai, pastikan waktu_mulai ada, backfill lalu drop tgl_mulai
        if (Schema::hasColumn('ujian','tgl_mulai')) {
            // pastikan kolom waktu_mulai ada (sementara nullable biar aman saat backfill)
            Schema::table('ujian', function (Blueprint $table) {
                if (!Schema::hasColumn('ujian','waktu_mulai')) {
                    $table->dateTime('waktu_mulai')->nullable();
                }
            });

            // salin nilai lama ke waktu_mulai jika masih null
            DB::statement('UPDATE ujian SET waktu_mulai = COALESCE(waktu_mulai, tgl_mulai)');

            // hapus tgl_mulai
            Schema::table('ujian', function (Blueprint $table) {
                $table->dropColumn('tgl_mulai');
            });
        }

        // (opsional) jadikan waktu_mulai NOT NULL via SQL mentah (tanpa DBAL)
        if (Schema::hasColumn('ujian','waktu_mulai')) {
            DB::statement('ALTER TABLE ujian MODIFY waktu_mulai DATETIME NOT NULL');
        }
    }

    public function down(): void
    {
        // Pulihkan tgl_mulai bila di-rollback
        Schema::table('ujian', function (Blueprint $table) {
            if (!Schema::hasColumn('ujian','tgl_mulai')) {
                $table->dateTime('tgl_mulai')->nullable();
            }
        });

        // isi balik dari waktu_mulai
        DB::statement('UPDATE ujian SET tgl_mulai = COALESCE(tgl_mulai, waktu_mulai)');

        // (opsional) bikin waktu_mulai nullable lagi agar aman
        try { DB::statement('ALTER TABLE ujian MODIFY waktu_mulai DATETIME NULL'); } catch (\Throwable $e) {}
    }
};
