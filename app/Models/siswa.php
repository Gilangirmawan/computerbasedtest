<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class siswa extends Model
{
    // use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'nis', 'nama', 'username', 'password',
        'jenis_kelamin', 'kelas', 'foto','status','user_id'
    ];

    protected $hidden = ['password'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function jurusan()
    {
         return $this->belongsTo(Jurusan::class,'jurusan_id', 'kode_jurusan'); // jika pakai kode_jurusan
    }
}
