<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::all();
        return view('pages.kelas.index',[
            'kelas'=> $kelas,
        ]);
    }

    // Tampilkan form tambah kelas
    public function create()
    {
        return view('pages.kelas.create');
    }

    // Simpan kelas baru
    public function tambah(Request $request)
    {
        $request->validate([
            'kelas' => 'required|string|max:100',
        ]);

        Kelas::create([
            'kelas' => $request->input('kelas'),
        ]);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    // Tampilkan form edit kelas
    public function edit($id)
    {
        $kelas = Kelas::findOrFail($id);
        return view('pages.kelas.edit',[
            'kelas'=> $kelas,
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all(); // ✅ gunakan ini
        // atau lebih aman:
        // $data = $request->only(['nama', 'nip']);

        $kelas = Kelas::findOrFail($id);
        $kelas->update($data);

        return redirect()->route('kelas.index')->with('success', 'Data berhasil diupdate.');
    }

    public function delete($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();
        return redirect()->route('kelas.index')->with('success', 'Data Kelas berhasil dihapus');
    }
}
