<?php

namespace App\Imports;

use App\Models\Soal;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;

class SoalImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    private $id_mapel;
    private $kelas_id;
    private $guru_id;

    public function __construct(int $id_mapel, int $kelas_id)
    {
        $this->id_mapel = $id_mapel;
        $this->kelas_id = $kelas_id;
        
        // Cari profil guru berdasarkan user yang login
        $guru = \App\Models\Guru::where('user_id', Auth::id())->first();
        $this->guru_id = $guru ? $guru->id : null;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Hanya proses jika profil guru ditemukan
        if (!$this->guru_id) {
            return null;
        }

        return new Soal([
            'soal'      => $row['soal'],
            'opsi_a'    => $row['opsi_a'],
            'opsi_b'    => $row['opsi_b'],
            'opsi_c'    => $row['opsi_c'],
            'opsi_d'    => $row['opsi_d'],
            'opsi_e'    => $row['opsi_e'] ?? null,
            'jawaban'   => strtoupper($row['jawaban']),
            'id_guru'   => $this->guru_id,
            'id_mapel'  => $this->id_mapel,
            'kelas'  => $this->kelas_id,
            'tgl_input' => now(),
        ]);
    }

    /**
     * Menentukan aturan validasi untuk setiap baris di Excel.
     */
    public function rules(): array
    {
        return [
            'soal' => 'required|string',
            'opsi_a' => 'required|string',
            'opsi_b' => 'required|string',
            'opsi_c' => 'required|string',
            'opsi_d' => 'required|string',
            'opsi_e' => 'required|string',
            'jawaban' => 'required|in:A,B,C,D,E',
        ];
    }
}