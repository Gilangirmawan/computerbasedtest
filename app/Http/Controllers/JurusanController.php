<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index()
    {
        $jurusan = Jurusan::all();
        return view('pages.jurusan.index',[
            'jurusan'=> $jurusan,
        ]);
    }

    public function create()
    {
        return view('pages.jurusan.create');
    }

    public function tambah(Request $request)
    {
        $request->validate([
            'kode_jurusan' => 'required|unique:jurusan,kode_jurusan',
            'nama' => 'required|string|max:100',
        ]);

        Jurusan::create($request->only(['kode_jurusan', 'nama']));

        return redirect()->route('jurusan.index')->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        return view('pages.jurusan.edit',[
            'jurusan'=> $jurusan,
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all(); // ✅ gunakan ini
        // atau lebih aman:
        // $data = $request->only(['nama', 'nip']);

        $jurusan = Jurusan::findOrFail($id);
        $jurusan->update($data);

        return redirect()->route('jurusan.index')->with('success', 'Data berhasil diupdate.');
    }

    public function delete($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        $jurusan->delete();
        return redirect()->route('jurusan.index')->with('success', 'Data jurusan berhasil dihapus');
    }
}
