<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Jurusan;
Use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankSoalController extends Controller
{
    // Menampilkan daftar soal
    public function index()
    {
        // Ambil data soal yang dimiliki oleh guru yang sedang login
        $guru = \App\Models\Guru::where('user_id', Auth::id())->first();

        $soalList = Soal::where('id_guru', $guru->id)->get();

        return view('pages.banksoal.index', compact('soalList'));
    }

    // Form tambah soal
    public function create()
    {
        $kelasList = Kelas::with('jurusan')->get(); // relasi jurusan jika diperlukan
        $jurusanList = Jurusan::all();
        $mapelList = Mapel::all();
        return view('pages.banksoal.create', compact('kelasList', 'jurusanList', 'mapelList'));
    }

    // Simpan soal baru
    public function tambah(Request $request)
    {
            $request->validate([
                'id_mapel' => 'required|exists:mapel,id',
                'kelas' => 'required',
                'soal' => 'required|string',
                'opsi_a' => 'required|string',
                'opsi_b' => 'required|string',
                'opsi_c' => 'nullable|string',
                'opsi_d' => 'nullable|string',
                'opsi_e' => 'nullable|string',
                'jawaban' => 'required|string',
                'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            ]);

                $guru = Guru::where('user_id', auth::id())->first();
                if (!$guru) {
                    return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
                }

                $soal = new Soal();
                $soal->id_guru = $guru->id;
                $soal->id_mapel = $request->id_mapel;
                $soal->kelas = $request->kelas;
                $soal->soal = $request->soal;
                $soal->opsi_a = $request->opsi_a;
                $soal->opsi_b = $request->opsi_b;
                $soal->opsi_c = $request->opsi_c;
                $soal->opsi_d = $request->opsi_d;
                $soal->opsi_e = $request->opsi_e;
                $soal->jawaban = $request->jawaban;
                $soal->tgl_input = now();

            if ($request->hasFile('file')) {
                $filename = time() . '_' . $request->file('file')->getClientOriginalName();
                $request->file('file')->storeAs('soal', $filename, 'public');
                $soal->file = $filename;
                $soal->tipe_file = $request->file('file')->getClientOriginalExtension();
            }

                $soal->save();

                return redirect()->route('banksoal.index')->with('success', 'Soal berhasil disimpan.');
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
