<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Pastikan kolom 'nama_ujian' ada (sementara nullable supaya aman saat backfill)
        Schema::table('ujian', function (Blueprint $table) {
            if (!Schema::hasColumn('ujian', 'nama_ujian')) {
                $table->string('nama_ujian')->nullable();
            }
        });

        // 2) Jika 'judul' masih ada, salin nilainya ke 'nama_ujian' bila 'nama_ujian' masih null/kosong
        if (Schema::hasColumn('ujian', 'judul')) {
            DB::statement("UPDATE ujian SET nama_ujian = COALESCE(NULLIF(nama_ujian, ''), judul) WHERE nama_ujian IS NULL OR nama_ujian = ''");
        }

        // 3) Backfill terakhir biar tidak ada NULL tersisa
        DB::statement("UPDATE ujian SET nama_ujian = 'Tanpa Judul' WHERE nama_ujian IS NULL OR nama_ujian = ''");

        // 4) Jadikan 'nama_ujian' NOT NULL
        Schema::table('ujian', function (Blueprint $table) {
            $table->string('nama_ujian')->nullable(false)->change();
        });

        // 5) Hapus kolom 'judul' kalau masih ada
        if (Schema::hasColumn('ujian', 'judul')) {
            Schema::table('ujian', function (Blueprint $table) {
                $table->dropColumn('judul');
            });
        }
    }

    public function down(): void
    {
        // Kembalikan 'judul' (nullable), isi dari 'nama_ujian', lalu hapus 'nama_ujian' bila ingin rollback
        Schema::table('ujian', function (Blueprint $table) {
            if (!Schema::hasColumn('ujian', 'judul')) {
                $table->string('judul')->nullable();
            }
        });

        DB::statement("UPDATE ujian SET judul = COALESCE(NULLIF(judul, ''), nama_ujian) WHERE judul IS NULL OR judul = ''");

        Schema::table('ujian', function (Blueprint $table) {
            if (Schema::hasColumn('ujian', 'nama_ujian')) {
                $table->dropColumn('nama_ujian');
            }
        });
    }
};
