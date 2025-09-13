<?php

namespace App\Exports;

use App\Models\Ujian;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class HasilUjianExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $ujian;

    public function __construct(Ujian $ujian)
    {
        // 1. Ambil data ujian dasar dan daftar pesertanya
        $ujian->load(['mapel', 'kelas.jurusan', 'peserta']);

        // 2. Kumpulkan semua ID siswa dari daftar peserta
        $siswaIds = $ujian->peserta->pluck('id_siswa');

        // 3. Ambil semua profil siswa yang dibutuhkan, beserta relasi kelas dan jurusannya
        $profilSiswaList = \App\Models\Siswa::with('kelas.jurusan')
                                ->whereIn('id', $siswaIds)
                                ->get()
                                ->keyBy('id');

        // 4. "Suntikkan" profil siswa yang benar ke setiap data peserta
        foreach ($ujian->peserta as $peserta) {
            if (isset($profilSiswaList[$peserta->id_siswa])) {
                $peserta->profil_siswa = $profilSiswaList[$peserta->id_siswa];
            } else {
                $peserta->profil_siswa = null;
            }
        }
        
        $this->ujian = $ujian;
    }

    /**
     * Menggunakan Blade View untuk merender konten Excel.
     */
    public function view(): View
    {
        return view('exports.hasil-ujian', [
            'ujian' => $this->ujian
        ]);
    }

    /**
     * Memberi nama pada sheet Excel.
     */
    public function title(): string
    {
        return $this->ujian->mapel->nama ?? 'Hasil Ujian';
    }
}