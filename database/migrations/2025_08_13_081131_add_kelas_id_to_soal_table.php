<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1) Tambah kolom kelas_id (nullable dulu supaya aman saat backfill)
        Schema::table('soal', function (Blueprint $table) {
            $table->foreignId('kelas_id')
                  ->nullable()
                  ->after('id_mapel')
                  ->constrained('kelas')
                  ->cascadeOnDelete();
        });

        // 2) Backfill: isi kelas_id berdasarkan nilai teks pada kolom 'kelas'
        //    Catatan: ini akan berhasil otomatis jika untuk setiap nilai 'soal.kelas'
        //    hanya ada SATU baris di tabel kelas dengan nama yang sama.
        DB::transaction(function () {
            // Ambil daftar kelas dan kelompokkan per nama 'kelas'
            $kelasByName = DB::table('kelas')
                ->select('id', 'kelas', 'jurusan_id')
                ->get()
                ->groupBy('kelas');

            // Isi kelas_id per 200 baris
            DB::table('soal')->orderBy('id')->chunkById(200, function ($rows) use ($kelasByName) {
                foreach ($rows as $row) {
                    $choices = $kelasByName[$row->kelas] ?? collect();

                    if ($choices->count() === 1) {
                        $kelasId = $choices->first()->id;
                        DB::table('soal')->where('id', $row->id)->update(['kelas_id' => $kelasId]);
                    } elseif ($choices->count() > 1) {
                        // Ambigu: ada beberapa baris kelas dengan nama sama (mis. "XI" utk banyak jurusan)
                        // Biarkan null agar Anda bisa betulkan manual.
                        Log::warning("[SOAL] Ambigu mapping kelas '{$row->kelas}' untuk soal id={$row->id}. Kelas_id dibiarkan NULL.");
                    } else {
                        // Tidak ditemukan padanan nama 'kelas'
                        Log::warning("[SOAL] Tidak ditemukan kelas '{$row->kelas}' untuk soal id={$row->id}. Kelas_id dibiarkan NULL.");
                    }
                }
            });
        });

        // 3) (Opsional) Anda bisa langsung menghapus kolom 'kelas' setelah memastikan semua kelas_id tidak NULL.
        //    Lebih aman: lakukan di migration terpisah setelah verifikasi manual.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan seperti semula: hapus FK & kolom kelas_id
        Schema::table('soal', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');
        });
    }
};
