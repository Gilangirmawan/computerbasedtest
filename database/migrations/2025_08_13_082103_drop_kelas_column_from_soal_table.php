<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan tidak ada yang NULL sebelum drop
        if (DB::table('soal')->whereNull('kelas_id')->exists()) {
            throw new \RuntimeException("Masih ada soal.kelas_id = NULL. Lengkapi dulu sebelum drop kolom 'kelas'.");
        }

        Schema::table('soal', function (Blueprint $table) {
            // Hapus kolom lama
            $table->dropColumn('kelas');
        });

        // (Opsional) jadikan kelas_id NOT NULL
        // Perlu doctrine/dbal untuk change() di sebagian environment:
        // composer require doctrine/dbal --dev
        Schema::table('soal', function (Blueprint $table) {
            $table->foreignId('kelas_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            $table->string('kelas')->after('id_mapel')->nullable();
        });
    }
};
