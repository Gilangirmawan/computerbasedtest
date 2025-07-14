<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use Illuminate\Http\Request;

class MapelController extends Controller
{
    public function index()
    {
        $mapel = Mapel::all();
        return view('pages.mapel.index',[
            'mapel'=> $mapel,
        ]);
    }

    public function create()
    {
        return view('pages.mapel.create');
    }

    public function tambah(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        Mapel::create([
            'nama' => $request->input('nama'),
        ]);

        return redirect()->route('mapel.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $mapel = Mapel::findOrFail($id);
        return view('pages.mapel.edit',[
            'mapel'=> $mapel,
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all(); // ✅ gunakan ini
        // atau lebih aman:
        // $data = $request->only(['nama', 'nip']);

        $mapel = Mapel::findOrFail($id);
        $mapel->update($data);

        return redirect()->route('mapel.index')->with('success', 'Data berhasil diupdate.');
    }

    public function delete($id)
    {
        $mapel = Mapel::findOrFail($id);
        $mapel->delete();
        return redirect()->route('mapel.index')->with('success', 'Data guru berhasil dihapus');
    }
}
