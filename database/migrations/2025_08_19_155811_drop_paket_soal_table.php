<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Jalankan: hapus tabel paket_soal
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('paket_soal');
        Schema::enableForeignKeyConstraints();
    }

    // Rollback: buat lagi tabel paket_soal (versi minimal)
    public function down(): void
    {
        Schema::create('paket_soal', function (Blueprint $table) {
            $table->unsignedBigInteger('id_ujian');
            $table->unsignedBigInteger('id_soal');

            // PK komposit agar tidak ada duplikasi pasangan
            $table->primary(['id_ujian', 'id_soal']);

            // FK ke tabel SINGULAR yang kamu pakai
            $table->foreign('id_ujian')->references('id')->on('ujian')->cascadeOnDelete();
            $table->foreign('id_soal')->references('id')->on('soal')->cascadeOnDelete();
        });
    }
};
