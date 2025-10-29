<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Kelas;
use App\Models\Jurusan;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SiswaImport;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        // Ambil input untuk filter
        $kelasId = $request->input('kelas_id');
        $jurusanId = $request->input('jurusan_id');
        $status = $request->input('status');

        // Buat query dasar untuk model Siswa
        $query = Siswa::with(['user', 'kelas', 'jurusan'])->latest();

        // Terapkan filter jika ada
        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }
        if ($jurusanId) {
            $query->where('jurusan_id', $jurusanId);
        }
        if ($status) {
            $query->whereHas('user', function ($subQuery) use ($status) {
                $subQuery->where('status', $status);
            });
        }

        // Jalankan query dengan paginasi
        $siswa = $query->paginate(5);

        // Ambil semua data kelas dan jurusan untuk ditampilkan di dropdown filter
        $kelasList = \App\Models\Kelas::orderBy('kelas')->get();
        $jurusanList = \App\Models\Jurusan::orderBy('nama')->get();

        return view('pages.siswa.index', compact('siswa', 'kelasList', 'jurusanList'));
    }
    public function create(Request $request)
    {
        $kelasList = Kelas::all(); // ambil semua data kelas
        $jurusanList = Jurusan::all(); // ambil semua data jurusan
        $fotopath = null;
        if ($request->hasFile('foto')) {
            $fotopath = $request->file('foto')->store('foto', 'public');
        }
        return view('pages.siswa.create', compact('kelasList', 'jurusanList'));
    }

    public function tambah(Request $request)
    {
        
       $request->validate([
        'nama' => 'required|string',
        'nis' => 'required|string|unique:siswa,nis',
        'username' => 'required|string|unique:users,username',
        'password' => 'required|string|min:6',
        // 'kelas' => 'required|string',
        'kelas_id' => 'required|exists:kelas,id',
        'jurusan_id' => 'required|exists:jurusan,kode_jurusan',
        'jenis_kelamin' => 'required|in:L,P',
        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

            // Cek apakah NIS sudah ada di siswa (cadangan validasi manual, bisa dihapus jika yakin)
            if (Siswa::where('nis', $request->nis)->exists()) {
                return back()->withErrors(['nis' => 'NIS sudah terdaftar.'])->withInput();
            }
            $user = User::create([
            'name' => $request->nama,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'status' => 'submitted',
            'role_id' => 3, // siswa
        ]);
        // Handle upload foto jika ada
            // $namaFoto = null;
            // if ($request->hasFile('foto')) {
            //     $foto = $request->file('foto');
            //     $namaFoto = uniqid() . '.' . $foto->getClientOriginalExtension();
            //     $foto->move(public_path('foto_siswa'), $namaFoto); 
            // }
                // Simpan ke tabel siswa
            Siswa::create([
                'nama' => $request->nama,
                'nis' => $request->nis,
                'username' => $request->username,
                'kelas_id' => $request->kelas_id,
                'jurusan_id' => $request->jurusan_id,
                'jenis_kelamin' => $request->jenis_kelamin,
                'password' => $user->password,
                'user_id' => $user->id, 
                // 'foto' => $namaFoto,
            ]);


        // Siswa::create($validatedData);

        return redirect()->route('siswa.index')->with('swal_success', 'Data siswa berhasil ditambahkan');
    }

    public function edit($id)
    {
        $kelasList = Kelas::all(); // ambil semua data kelas
        $jurusanList = Jurusan::all(); // ambil semua data jurusan
        $siswa = Siswa::findOrFail($id);
        return view('pages.siswa.edit',[
            'siswa'=> $siswa,
            'kelasList' => $kelasList,
            'jurusanList' => $jurusanList,
        ]);
    }

    public function update(Request $request, $id)
    {
        // dd($request->input('status'));
        $siswa = Siswa::findOrFail($id);
        $user = User::findOrFail($siswa->user_id);
        
        $request->validate([
            'nama' => 'required|string',
            'nis' => 'required|string',
            'username' => 'required|string|unique:users,username,'.$user->id,
            'jenis_kelamin' => 'required|in:L,P',
            'kelas_id' => 'required|exists:kelas,id',
            'jurusan_id' => 'required|exists:jurusan,kode_jurusan',
            'status' => 'required|string|in:submitted,approved,rejected'
        ]);

        $siswa = Siswa::findOrFail($id);
        $siswa->nama = $request->input('nama');
        $siswa->nis = $request->input('nis');
        $siswa->kelas_id = $request->input('kelas_id');
        $siswa->username = $request->input('username');
        $siswa->jurusan_id = $request->input('jurusan_id');
        $siswa->jenis_kelamin = $request->input('jenis_kelamin');
        $siswa->save();

        //update status siswa
        $siswa->status = $request->input('status');
        // $siswa->save();

        // Update status di tabel users
        $user->name = $request->input('nama');
        $user->username= $request->input('username');
        $user->status = $request->input('status');
        $user->save();

        return redirect()->route('siswa.index')->with('swal_success', 'Data berhasil diupdate.');
    }

    public function delete($id)
    {
        $siswa = Siswa::findOrFail($id);

        // Hapus user yang berelasi jika ada
        if ($siswa->user) {
            $siswa->user->delete();
        }

        // Hapus siswa
        $siswa->delete();

        return redirect()->route('siswa.index')->with('swal_success', 'Data siswa dan user berhasil dihapus.');
    }

    public function importCreate()
    {
        return view('pages.siswa.import');
    }

    /**
     * Menjalankan proses import dari file Excel.
     */
    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        try {
            Excel::import(new SiswaImport, $request->file('file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             $errors = [];
             foreach ($failures as $failure) {
                 $errors[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
             }
             return redirect()->route('siswa.import.create')->with('import_errors', $errors);
        } catch (\Exception $e) {
            return redirect()->route('siswa.import.create')->with('import_errors', ['Gagal mengimpor file: ' . $e->getMessage()]);
        }

        return redirect()->route('siswa.index')->with('swal_success', 'Data siswa berhasil diimpor!');
    }
}
