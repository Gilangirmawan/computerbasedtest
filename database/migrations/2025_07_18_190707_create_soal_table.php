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
                Schema::create('soal', function (Blueprint $table) {
            $table->id(); // primary key

            $table->unsignedBigInteger('id_guru'); // FK ke users
            $table->unsignedBigInteger('id_mapel'); // FK ke mapel
            $table->string('kelas', 50); // bisa juga FK ke tabel kelas jika ada

            $table->string('file', 150)->nullable(); // nama file (gambar, audio, dll)
            $table->string('tipe_file', 50)->nullable(); // contoh: 'gambar', 'audio'

            $table->longText('soal'); // isi soal

            $table->longText('opsi_a');
            $table->longText('opsi_b');
            $table->longText('opsi_c');
            $table->longText('opsi_d');
            $table->longText('opsi_e')->nullable();

            $table->string('jawaban', 5); // misal: 'a', 'c'

            $table->timestamp('tgl_input')->useCurrent();

            // Relasi (optional jika tabel guru/mapel/kode guru tersedia)
            $table->foreign('id_guru')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_mapel')->references('id')->on('mapel')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soal');
    }
};
