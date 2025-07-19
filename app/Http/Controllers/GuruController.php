<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\GuruRequest;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index()
    {
        $guru = Guru::all();
        return view('pages.guru.index',[
            'guru'=> $guru,
        ]);
    }

    public function create()
    {
        return view('pages.guru.create');
    }

    public function tambah(Request $request)
    {

       $request->validate([
        'nip' => 'required|unique:guru,nip|unique:users,username',
        'nama' => 'required',
    ]);

    
    // Simpan juga ke tabel users (untuk login)
    $user = new User();
    $user->username = $request->nip;
    $user->name = $request->nama;
    $user->password = Hash::make($request->nip); // default password = nip
    $user->role_id = 2; // role untuk guru
    $user->status = 'approved';
    $user->save();
    
    // Simpan ke tabel guru
    $guru = new Guru();
    $guru->nip = $request->nip;
    $guru->nama = $request->nama;
    $guru->user_id = $user->id;
    $guru->save();
    
    return redirect()->route('guru.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $guru = Guru::findOrFail($id);
        return view('pages.guru.edit',[
            'guru'=> $guru,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|max:20|unique:guru,nip,' . $id,
        ]);

            // Ambil data guru
        $guru = Guru::findOrFail($id);
        $guru->nama = $request->nama;
        $guru->nip = $request->nip;
        $guru->save();

        // ✅ Update juga data users yang berelasi
        if ($guru->user_id) {
            $user = \App\Models\User::find($guru->user_id);
            if ($user) {
                $user->name = $request->nama;
                $user->username = $request->nip; // jika username pakai nip
                $user->save();
            }
        }

        return redirect()->route('guru.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function delete($id)
    {
        $guru = Guru::findOrFail($id);
        $guru->delete();
        return redirect()->route('guru.index')->with('success', 'Data guru berhasil dihapus');
    }
}
