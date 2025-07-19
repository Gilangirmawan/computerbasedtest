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
        Schema::table('siswa', function (Blueprint $table) {
    if (!Schema::hasColumn('siswa', 'kelas_id')) {
        $table->unsignedBigInteger('kelas_id')->nullable()->after('id');
    }
    if (!Schema::hasColumn('siswa', 'jurusan_id')) {
        $table->unsignedBigInteger('jurusan_id')->nullable()->after('kelas_id');
    }
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            //
        });
    }
};
