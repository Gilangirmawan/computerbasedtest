<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PaketSoal;


class Ujian extends Model
{
    protected $table = 'ujian';

    protected $fillable = ['nama_ujian','id_mapel','kelas_id','kode_jurusan','jumlah_soal','waktu','jenis','waktu_mulai','waktu_selesai','terlambat','token','id_guru'];
    protected $casts = ['waktu_mulai'=>'datetime','waktu_selesai'=>'datetime','terlambat'=>'datetime'];


    public function mapel()
    {
        // perbaiki: kolom FK-nya 'id_mapel' (bukan 'mapel_id')
        return $this->belongsTo(Mapel::class, 'id_mapel');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }

    public function soal()
    {
        return $this->belongsToMany(Soal::class, 'paket_soal', 'id_ujian', 'id_soal')
                    ->withPivot(['urutan', 'bobot'])
                    ->withTimestamps();
    }

    public function paketSoal()
    {
        return $this->hasMany(\App\Models\PaketSoal::class, 'id_ujian', 'id');
    }

    public function jawaban()
    {
        return $this->hasMany(Jawaban::class, 'id_ujian');
    }
}
