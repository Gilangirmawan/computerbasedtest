<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Jurusan;
Use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Imports\SoalImport;
use Maatwebsite\Excel\Facades\Excel;

class BankSoalController extends Controller
{
    // Menampilkan daftar soal
    public function index()
    {
        $soalList = Soal::with(['mapel','kelas.jurusan', 'guru'])
                        ->orderByDesc('tgl_input')
                        ->paginate(10);

        return view('pages.banksoal.index', compact('soalList'));
    }

    // Form tambah soal
    public function create()
    {
        $mapelList = Mapel::orderBy('nama')->get();
        $kelasList = Kelas::with('jurusan')->orderBy('kelas')->get();

        return view('pages.banksoal.create', compact('mapelList', 'kelasList'));
    }

    // Simpan soal baru
    public function tambah(Request $request)
    {
        $request->validate([
            'id_mapel' => 'required|exists:mapel,id',
            'kelas_id' => 'required|exists:kelas,id',
            'soal'     => 'required|string',
            'opsi_a'   => 'required|string',
            'opsi_b'   => 'required|string',
            'opsi_c'   => 'required|string',
            'opsi_d'   => 'required|string',
            'opsi_e'   => 'nullable|string',
            'jawaban'  => 'required|in:A,B,C,D,E',
            'file'     => 'nullable|file|max:4096',
        ]);

        // =================================================================
        // PERBAIKAN UTAMA DI SINI
        // 1. Ambil profil guru
        $guru = \App\Models\Guru::where('user_id', Auth::id())->first();

        // 2. Tambahkan pengecekan: jika profil guru tidak ada, hentikan proses
        if (!$guru) {
            return redirect()->back()
                ->withErrors(['msg' => 'Gagal menyimpan soal. Profil guru untuk pengguna ini tidak ditemukan.'])
                ->withInput();
        }
        // =================================================================

        $soal = new Soal();
        $soal->id_mapel = $request->id_mapel;
        $soal->kelas_id = $request->kelas_id;
        $soal->soal     = $request->soal;
        $soal->opsi_a   = $request->opsi_a;
        $soal->opsi_b   = $request->opsi_b;
        $soal->opsi_c   = $request->opsi_c;
        $soal->opsi_d   = $request->opsi_d;
        $soal->opsi_e   = $request->opsi_e;
        $soal->jawaban  = $request->jawaban;
        
        // 3. Simpan id guru yang sudah pasti ada
        $soal->id_guru  = $guru->id;

        // upload file (opsional)
        if ($request->hasFile('file')) {
            $f = $request->file('file');
            $nama = uniqid('soal_').'.'.$f->getClientOriginalExtension();
            $f->move(public_path('storage/soal'), $nama);
            $soal->file = 'soal/'.$nama;
            $soal->tipe_file = $f->getClientMimeType();
        }

        $soal->save();
        return redirect()->route('banksoal.index')->with('success', 'Soal berhasil ditambahkan.');
    }

    // Form edit soal
    public function edit($id)
    {
        $soal  = Soal::with(['mapel','kelas.jurusan'])->findOrFail($id); // <-- definisikan $soal
        $mapel = Mapel::all();
        $kelas = Kelas::with('jurusan')->get();

        return view('pages.banksoal.edit', compact('soal', 'mapel', 'kelas'));
    }

    // Update soal
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_mapel' => 'required|exists:mapel,id',
            'kelas_id' => 'required|exists:kelas,id',
            'soal'     => 'required|string',
            'opsi_a'   => 'required|string',
            'opsi_b'   => 'required|string',
            'opsi_c'   => 'required|string',
            'opsi_d'   => 'required|string',
            'opsi_e'   => 'nullable|string',
            'jawaban'  => 'required|in:A,B,C,D,E',
            'file'     => 'nullable|file|max:4096',
        ]);

        $soal = Soal::findOrFail($id);
        $soal->id_mapel = $request->id_mapel;
        $soal->kelas_id = $request->kelas_id; // << penting
        $soal->soal     = $request->soal;
        $soal->opsi_a   = $request->opsi_a;
        $soal->opsi_b   = $request->opsi_b;
        $soal->opsi_c   = $request->opsi_c;
        $soal->opsi_d   = $request->opsi_d;
        $soal->opsi_e   = $request->opsi_e;
        $soal->jawaban  = $request->jawaban;

        if ($request->hasFile('file')) {
            $f = $request->file('file');
            $nama = uniqid('soal_').'.'.$f->getClientOriginalExtension();
            $f->move(public_path('storage/soal'), $nama);
            $soal->file = 'soal/'.$nama;
            $soal->tipe_file = $f->getClientMimeType();
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

    public function importCreate()
    {
        $mapelList = Mapel::orderBy('nama')->get();
        $kelasList = Kelas::with('jurusan')->orderBy('kelas')->get();
        return view('pages.banksoal.import_soal', compact('mapelList', 'kelasList'));
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
            'id_mapel' => 'required|exists:mapel,id',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        try {
            $import = new SoalImport($request->id_mapel, $request->kelas_id);
            Excel::import($import, $request->file('file'));

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             $errors = [];
             foreach ($failures as $failure) {
                 $errors[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
             }
             return redirect()->route('banksoal.import.create')->with('import_errors', $errors);

        } catch (\Exception $e) {
            return redirect()->route('banksoal.import.create')->with('import_errors', ['Gagal mengimpor file: ' . $e->getMessage()]);
        }

        return redirect()->route('banksoal.index')->with('success', 'Soal berhasil diimpor!');
    }
}
