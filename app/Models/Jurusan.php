<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    // Nama tabel di database
    protected $table = 'jurusan';

    // Primary key bukan 'id'
    protected $primaryKey = 'kode_jurusan';

    // Karena primary key berupa string dan bukan auto-increment
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang bisa diisi (mass-assignment)
    protected $fillable = ['kode_jurusan', 'nama'];

    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }
}
