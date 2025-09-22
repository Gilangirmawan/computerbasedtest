<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;   
use App\Models\Jurusan;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    public function model(array $row)
    {
        // Cari ID kelas berdasarkan nama kelas dari file Excel
        $kelas = Kelas::where('kelas', $row['kelas'])->first();

        // Cari ID jurusan berdasarkan kode jurusan dari file Excel
        $jurusan = Jurusan::where('kode_jurusan', $row['jurusan'])->first();
        
        // 1. Buat akun login baru di tabel 'users'
        $user = User::create([
            'name'     => $row['nama'],
            'username' => $row['username'],
            'password' => Hash::make($row['password']),
            'role_id'  => 3, // 3 = Siswa
            'status'   => 'submitted',
        ]);

        // 2. Buat profil siswa baru di tabel 'siswa'
        return new Siswa([
            'nis'           => $row['nis'],
            'nama'          => $row['nama'],
            'username'      => $row['username'],
            'password'      => Hash::make($row['password']),
            'jenis_kelamin' => $row['jenis_kelamin'],
            'kelas_id'      => $kelas ? $kelas->id : null, // Gunakan ID yang ditemukan
            'jurusan_id'    => $row['jurusan'], //$jurusan->id : null, Gunakan ID yang ditemukan
            'user_id'       => $user->id,
        ]);
    }

    public function rules(): array
    {
        // Aturan validasi untuk setiap baris di Excel
        return [
            'nis' => 'required|unique:siswa,nis',
            'nama' => 'required|string',
            'username' => 'required|unique:users,username',
            'password' => 'required',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas' => 'required|exists:kelas,kelas',
            'jurusan' => 'required|exists:jurusan,kode_jurusan',
        ];
    }
}