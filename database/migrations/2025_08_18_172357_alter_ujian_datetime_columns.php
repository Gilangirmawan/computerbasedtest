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
        Schema::table('ujian', function (Blueprint $table) {
             // pastikan kolom tanggal bertipe DATETIME
            $table->dateTime('tgl_mulai')->change();
            $table->dateTime('terlambat')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ujian', function (Blueprint $table) {
            // rollback ke tipe sebelumnya (sesuaikan dengan kondisi awal Anda)
            // jika sebelumnya integer:
            $table->integer('tgl_mulai')->nullable()->change();
            $table->integer('terlambat')->nullable()->change();
        });
    }
};
