<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    use HasFactory;

    protected $table = 'jawaban';

    protected $fillable = [
        'id_ujian',
        'id_siswa',
        'id_soal',
        'jawaban',
        'is_benar',
    ];

    // Relasi ke Ujian
    public function ujian()
    {
        return $this->belongsTo(Ujian::class, 'id_ujian');
    }

    // Relasi ke User (Siswa)
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Relasi ke Soal
    public function soal()
    {
        return $this->belongsTo(Soal::class, 'id_soal');
    }
}
