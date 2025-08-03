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
        $soal = Soal::with('kelas.jurusan')->first();
// dd($soal->kelas->jurusan->nama ?? 'Jurusan tidak ditemukan');
        // Ambil data soal yang dimiliki oleh guru yang sedang login
        $guru = Guru::where('user_id', Auth::id())->first();

        $soalList = Soal::with(['kelas.jurusan', 'mapel'])->get();

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
        $mapel = Mapel::all(); // Ini nama variabel harus $mapel, bukan $mapelList
        $kelas = Kelas::all();

        return view('pages.banksoal.edit', compact('soal', 'mapel', 'kelas'));
    }

    // Update soal
    public function update(Request $request, $id)
    {
        $request->validate([
            'soal' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
            'opsi_a' => 'required|string',
            'opsi_b' => 'required|string',
            'opsi_c' => 'required|string',
            'opsi_d' => 'required|string',
            'jawaban' => 'required|in:A,B,C,D,E',
            'kelas' => 'required',
            'id_mapel' => 'required|exists:mapel,id',
        ]);

        $soal = Soal::findOrFail($id);
        $soal->soal = $request->soal;
        $soal->opsi_a = $request->opsi_a;
        $soal->opsi_b = $request->opsi_b;
        $soal->opsi_c = $request->opsi_c;
        $soal->opsi_d = $request->opsi_d;
        $soal->opsi_e = $request->opsi_e;
        $soal->jawaban = $request->jawaban;
        $soal->kelas = $request->kelas;
        $soal->id_mapel = $request->id_mapel;

        // Simpan file jika ada
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $namaFile = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/soal'), $namaFile);
            $soal->file = 'soal/' . $namaFile;
            $soal->tipe_file = $file->getClientMimeType();
        }

        $soal->save();

        return redirect()->route('banksoal.index')->with('success', 'Soal berhasil diperbarui.');
    }

    // Hapus soal
    public function delete($id)
    {
        $soal = Soal::findOrFail($id);

        if ($soal->gambar && file_exists(public_path('gambar_soal/' . $soal->gambar))) {
            unlink(public_path('gambar_soal/' . $soal->gambar));
        }

        $soal->delete();

        return redirect()->route('banksoal.index')->with('success', 'Soal berhasil dihapus');
    }
}
