<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PaketSoal extends Pivot
{
    protected $table = 'paket_soal';

    public function ujian()
    {
        return $this->belongsTo(\App\Models\Ujian::class, 'id_ujian');
    }

    public function soal()
    {
        return $this->belongsTo(\App\Models\Soal::class, 'id_soal');
    }
}
