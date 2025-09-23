<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
Use App\Models\Jurusan;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with('jurusan')->orderBy('kelas')->get();
        return view('pages.kelas.index', compact('kelas'));
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
            'kelas' => 'required|string|max:20',
            'jurusan_id' => 'required|exists:jurusan,kode_jurusan',
        ]);
        Kelas::create($request->all());
        return redirect()->route('kelas.index')->with('swal_success', 'Data kelas berhasil ditambahkan.');
    }

    // Tampilkan form edit kelas
    public function edit($id)
    {
        $kelas = Kelas::findOrFail($id);
        $jurusanList = Jurusan::orderBy('nama')->get();
        return view('pages.kelas.edit', compact('kelas', 'jurusanList'));
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        $request->validate([
            'kelas' => 'required|string|max:20',
            'jurusan_id' => 'required|exists:jurusan,kode_jurusan',
        ]);
        $kelas->update($request->all());
        return redirect()->route('kelas.index')->with('swal_success', 'Data kelas berhasil diperbarui.');
    }

    public function delete($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();
        return redirect()->route('kelas.index')->with('swal_success', 'Data kelas berhasil dihapus.');
    }
}
