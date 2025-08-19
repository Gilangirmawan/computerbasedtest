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
        $table->unsignedBigInteger('mapel_id')->after('id');

        // Foreign key ke tabel mapel
        $table->foreign('mapel_id')->references('id')->on('mapel')->onDelete('cascade');
    });
}

    public function down(): void
    {
        Schema::table('ujian', function (Blueprint $table) {
            $table->dropForeign(['mapel_id']);
            $table->dropColumn('mapel_id');
        });
    }
};
