<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IkutUjian extends Model
{
    use HasFactory;

    protected $table = 'ikut_ujian';

    protected $fillable = [
        'id_ujian',
        'id_siswa',
        'jml_benar',
        'nilai',
        'tgl_selesai',
        'status',
    ];

    // relasi ke User
    public function siswa()
    {
        return $this->belongsTo(User::class, 'id_siswa');
    }

    // relasi ke Ujian
    public function ujian()
    {
        return $this->belongsTo(Ujian::class, 'id_ujian');
    }
    
    public function jawaban()
    {
        // Sebuah riwayat ujian memiliki banyak jawaban.
        // Kita hubungkan berdasarkan 'id_ujian' dan 'id_siswa'
        return $this->hasMany(Jawaban::class, 'id_siswa', 'id_siswa')
                    ->where('id_ujian', $this->id_ujian);
    }
}