<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('paket_soal', function (Blueprint $table) {
            // FK ke tabel induk (singular)
            $table->unsignedBigInteger('id_ujian');
            $table->unsignedBigInteger('id_soal');

            // Meta yang berguna
            $table->unsignedInteger('urutan')->nullable();       // posisi soal dalam ujian
            $table->decimal('bobot', 5, 2)->default(1.00);       // skor per soal (opsional)
            $table->timestamps();                                // jejak waktu

            // Constraint utama
            $table->primary(['id_ujian', 'id_soal']);            // cegah duplikasi pasangan

            // Foreign keys
            $table->foreign('id_ujian')->references('id')->on('ujian')->cascadeOnDelete();
            $table->foreign('id_soal')->references('id')->on('soal')->cascadeOnDelete();

            // Index bantu
            $table->index(['id_ujian', 'urutan']);               // ambil berurutan lebih cepat
            $table->index('id_soal');

            // (Opsional) Satu nomor urut per ujian; multiple NULL tetap boleh
            // $table->unique(['id_ujian', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_soal');
    }
};
