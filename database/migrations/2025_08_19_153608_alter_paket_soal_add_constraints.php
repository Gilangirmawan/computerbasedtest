<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Pastikan kolom ada & tipe cocok (BIGINT UNSIGNED)
        Schema::table('paket_soal', function (Blueprint $table) {
            // Jika tabel sudah punya kolom, bagian ini aman (tidak mengubah apa-apa).
            if (!Schema::hasColumn('paket_soal', 'id_ujian')) {
                $table->unsignedBigInteger('id_ujian');
            }
            if (!Schema::hasColumn('paket_soal', 'id_soal')) {
                $table->unsignedBigInteger('id_soal');
            }
        });

        Schema::table('paket_soal', function (Blueprint $table) {
            // Drop FK/PK lama kalau ada supaya tidak bentrok
            try { $table->dropForeign(['id_ujian']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['id_soal']); } catch (\Throwable $e) {}
            try { $table->dropPrimary(); } catch (\Throwable $e) {}

            // Tambahkan FK ke tabel SINGULAR yang kamu pakai
            $table->foreign('id_ujian')->references('id')->on('ujian')->cascadeOnDelete();
            $table->foreign('id_soal')->references('id')->on('soal')->cascadeOnDelete();

            // PK komposit untuk cegah duplikasi pasangan ujian-soal
            $table->primary(['id_ujian', 'id_soal']);
        });
    }

    public function down(): void
    {
        Schema::table('paket_soal', function (Blueprint $table) {
            try { $table->dropForeign(['id_ujian']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['id_soal']); } catch (\Throwable $e) {}
            try { $table->dropPrimary(); } catch (\Throwable $e) {}
        });
    }
};
