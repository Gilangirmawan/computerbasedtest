<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin CBT',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'status' => 'approved',
            'role_id' => 1,
        ]);

        // User::create([
        //     'name' => 'guru',
        //     'username' => 'guru',
        //     'password' => 'password',
        //     'status' => 'approved',
        //     'role_id' => 2,
        // ]);

        // User::create([
        //     'name' => 'siswa',
        //     'username' => 'siswa',
        //     'password' => 'password',
        //     'status' => 'approved',
        //     'role_id' => 3,
        // ]);
    }
}
