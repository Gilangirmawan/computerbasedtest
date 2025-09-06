<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jawaban', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_ujian');   // relasi ke tabel ujian
            $table->unsignedBigInteger('id_siswa');    // relasi ke tabel user/siswa
            $table->unsignedBigInteger('id_soal');    // relasi ke tabel soal
            $table->string('jawaban', 5)->nullable(); // jawaban siswa (A/B/C/D/E)
            $table->boolean('is_benar')->nullable();  // hasil koreksi (opsional)
            $table->timestamps();

            // Relasi
            $table->foreign('id_ujian')->references('id')->on('ujian')->onDelete('cascade');
            $table->foreign('id_siswa')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('id_soal')->references('id')->on('soal')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban');
    }
};
