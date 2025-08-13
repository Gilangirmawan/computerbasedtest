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
        Schema::create('paket_soal', function (Blueprint $table) {
            // Dua kolom saja sesuai permintaan
            $table->foreignId('id_ujian')
                  ->constrained('ujian')        // refer ke table 'ujian' pk 'id'
                  ->cascadeOnDelete();

            $table->foreignId('id_soal')
                  ->constrained('soal')         // refer ke table 'soal' pk 'id'
                  ->cascadeOnDelete();

            // Jadikan kombinasi keduanya sebagai primary key (atau minimal unique)
            $table->primary(['id_ujian', 'id_soal']);
            // Alternatif jika tidak mau primary: $table->unique(['id_ujian','id_soal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket_soal');
    }
};
