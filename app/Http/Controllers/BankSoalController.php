<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use App\Models\Mapel;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankSoalController extends Controller
{
    // Menampilkan daftar soal
    public function index()
    {
        $banksoal = Soal::with(['mapel', 'kelas'])->get();
        return view('pages.banksoal.index', compact('banksoal'));
    }

    // Form tambah soal
    public function create()
    {
        $mapel = Mapel::all();
        $kelas = Kelas::all();
        return view('pages.banksoal.create', compact('mapel', 'kelas'));
    }

    // Simpan soal baru
    public function tambah(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
            'pilihan_a' => 'required|string',
            'pilihan_b' => 'required|string',
            'pilihan_c' => 'required|string',
            'pilihan_d' => 'required|string',
            'jawaban_benar' => 'required|in:A,B,C,D',
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapel,id',
        ]);

        $data = $request->only([
            'pertanyaan', 'pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d',
            'jawaban_benar', 'kelas_id', 'mapel_id'
        ]);
        $data['user_id'] = Auth::id();

        // Upload gambar jika ada
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $namaFile = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('gambar_soal'), $namaFile);
            $data['gambar'] = $namaFile;
        }

        Soal::create($data);

        return redirect()->route('pages.banksoal.index')->with('success', 'Soal berhasil ditambahkan');
    }

    // Form edit soal
    public function edit($id)
    {
        $soal = Soal::findOrFail($id);
        $mapel = Mapel::all();
        $kelas = Kelas::all();

        return view('pages.banksoal.edit', compact('soal', 'mapel', 'kelas'));
    }

    // Update soal
    public function update(Request $request, $id)
    {
        $request->validate([
            'pertanyaan' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
            'pilihan_a' => 'required|string',
            'pilihan_b' => 'required|string',
            'pilihan_c' => 'required|string',
            'pilihan_d' => 'required|string',
            'jawaban_benar' => 'required|in:A,B,C,D',
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapel,id',
        ]);

        $soal = Soal::findOrFail($id);
        $soal->fill($request->only([
            'pertanyaan', 'pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d',
            'jawaban_benar', 'kelas_id', 'mapel_id'
        ]));

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $namaFile = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('gambar_soal'), $namaFile);
            $soal->gambar = $namaFile;
        }

        $soal->save();

        return redirect()->route('pages.banksoal.index')->with('success', 'Soal berhasil diperbarui');
    }

    // Hapus soal
    public function delete($id)
    {
        $soal = Soal::findOrFail($id);

        if ($soal->gambar && file_exists(public_path('gambar_soal/' . $soal->gambar))) {
            unlink(public_path('gambar_soal/' . $soal->gambar));
        }

        $soal->delete();

        return redirect()->route('pages.banksoal.index')->with('success', 'Soal berhasil dihapus');
    }
}
