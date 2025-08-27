<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
     protected $table = 'mapel';

    protected $fillable = ['nama'];

    public function soal()
    {
        return $this->hasMany(Soal::class, 'id_mapel');
    }

    public function ujian()
    {
        return $this->hasMany(Ujian::class, 'id_mapel');
    }
}
