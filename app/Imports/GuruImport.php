<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;

class GuruImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    public function model(array $row)
    {
        // 1. Buat akun login baru di tabel 'users'
        $user = User::create([
            'name'     => $row['nama'],
            'username' => $row['nip'],
            'password' => Hash::make($row['nip']), // Password default = NIP
            'role_id'  => 2, // 2 = Guru
            'status'   => 'approved',
        ]);

        // 2. Buat profil guru baru di tabel 'guru' dan hubungkan dengan user_id
        return new Guru([
            'nip'     => $row['nip'],
            'nama'    => $row['nama'],
            'user_id' => $user->id,
        ]);
    }

    public function rules(): array
    {
        // Aturan validasi untuk setiap baris di Excel
        return [
            'nip'  => 'required|unique:guru,nip|unique:users,username',
            'nama' => 'required|string',
        ];
    }
}