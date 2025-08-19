<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ujian', function (Blueprint $table) {
            if (Schema::hasColumn('ujian','judul') && !Schema::hasColumn('ujian','nama_ujian')) {
                $table->renameColumn('judul','nama_ujian');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ujian', function (Blueprint $table) {
            if (Schema::hasColumn('ujian','nama_ujian') && !Schema::hasColumn('ujian','judul')) {
                $table->renameColumn('nama_ujian','judul');
            }
        });
    }
};
