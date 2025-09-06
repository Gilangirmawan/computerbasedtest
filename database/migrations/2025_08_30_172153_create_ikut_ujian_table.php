<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ikut_ujian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_ujian');   // relasi ke ujian
            $table->unsignedBigInteger('id_siswa');   // sebenarnya ambil dari users
            $table->integer('jml_benar')->default(0);
            $table->decimal('nilai', 5, 2)->default(0);
            $table->timestamp('tgl_selesai')->nullable();
            $table->enum('status', ['belum', 'selesai'])->default('belum');
            $table->timestamps();

            // relasi opsional
            $table->foreign('id_ujian')->references('id')->on('ujian')->onDelete('cascade');
            $table->foreign('id_siswa')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ikut_ujian');
    }
};
