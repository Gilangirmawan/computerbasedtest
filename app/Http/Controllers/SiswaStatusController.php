<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\User;

class SiswaStatusController extends Controller
{
    public function approve($id)
    {
        $siswa = Siswa::findOrFail($id);
        $user = User::findOrFail($siswa->user_id);

        $user->status = 'approved';
        $user->save();

        return redirect()->back()->with('success', 'Status siswa berhasil disetujui.');
    }

    public function reject($id)
    {
        $siswa = Siswa::findOrFail($id);
        $user = User::findOrFail($siswa->user_id);

        $user->status = 'rejected';
        $user->save();

        return redirect()->back()->with('success', 'Status siswa berhasil ditolak.');
    }
}
