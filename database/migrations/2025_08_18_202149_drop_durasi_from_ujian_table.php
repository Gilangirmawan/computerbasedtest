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
        if (Schema::hasColumn('ujian', 'durasi')) {
            Schema::table('ujian', function (Blueprint $table) {
                $table->dropColumn('durasi');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ujian', function (Blueprint $table) {
            // Pulihkan kolom bila di-rollback (boleh nullable agar aman)
            if (!Schema::hasColumn('ujian', 'durasi')) {
                $table->integer('durasi')->nullable();
            }
        });
    }
};
