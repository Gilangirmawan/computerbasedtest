<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
Use App\Models\Jurusan;

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
        $jurusanList = Jurusan::all(); // Ambil semua jurusan

        return view('pages.kelas.create', compact('jurusanList'));

    }

    // Simpan kelas baru
    public function tambah(Request $request)
    {
        $request->validate([
        'kelas' => 'required|string',
        'jurusan_id' => 'required|exists:jurusan,kode_jurusan'
    ]);
// dd($request->all());

    Kelas::create([
        'kelas' => $request->kelas,
        'jurusan_id' => $request->jurusan_id,
    ]);
    $jurusanList = Jurusan::all();

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    // Tampilkan form edit kelas
    public function edit($id)
    {
        $jurusanList = Jurusan::all();
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
