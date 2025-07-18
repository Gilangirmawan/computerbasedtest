<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $table = 'soal';

    protected $fillable = [
        'id_guru',
        'id_mapel',
        'kelas',
        'file',
        'tipe_file',
        'soal',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'opsi_e',
        'jawaban',
        'tgl_input',
    ];

    public $timestamps = false; // karena kita pakai `tgl_input`, bukan `created_at`

    // Relasi ke user (guru)
    public function guru()
    {
        return $this->belongsTo(User::class, 'id_guru');
    }

    // Relasi ke mata pelajaran
    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'id_mapel');
    }

    //Jika kamu punya model Kelas, bisa gunakan ini:
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
