<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
