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
        Schema::create('ujian', function (Blueprint $table) {
            $table->id();
            $table->string('id_guru', 10); 
            $table->unsignedBigInteger('id_mapel');
            $table->unsignedBigInteger('kelas_id');   // relasi ke tabel kelas
            $table->string('kode_jurusan', 20);       // relasi ke jurusan
            $table->string('nama_ujian', 200);
            $table->integer('jumlah_soal');
            $table->integer('waktu');
            $table->enum('jenis', ['acak', 'set']);
            $table->dateTime('tgl_mulai');
            $table->integer('terlambat')->nullable();
            $table->string('token', 5);
            $table->timestamps();

            $table->foreign('id_mapel')->references('id')->on('mapel')->onDelete('cascade');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
            $table->foreign('kode_jurusan')->references('kode_jurusan')->on('jurusan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujian');
    }
};
