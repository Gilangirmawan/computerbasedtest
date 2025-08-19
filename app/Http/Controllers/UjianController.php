<?php

namespace App\Http\Controllers;

use App\Models\Ujian;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UjianController extends Controller
{
    public function index()
    {
        // eager load sampai jurusan agar tampil di tabel
        $ujianList = Ujian::with(['mapel', 'kelas.jurusan'])->latest()->get();
        return view('pages.ujian.index', compact('ujianList'));
    }

    public function create()
    {
        $mapelList = Mapel::all();
        $kelasList = Kelas::with('jurusan')->get();

        return view('pages.ujian.create', compact('mapelList', 'kelasList'));
    }

    // Ganti dari 'tambah' -> 'store'
    public function tambah(Request $request)
    {
        $data = $this->validated($request);

        $mulai     = Carbon::createFromFormat('Y-m-d\TH:i', $data['waktu_mulai']);
        $durasi    = (int) $data['waktu'];
        $terlambat = isset($data['terlambat']) && $data['terlambat']
            ? Carbon::createFromFormat('Y-m-d\TH:i', $data['terlambat'])
            : null;

        $ujian = new Ujian();
        $ujian->nama_ujian   = $data['nama_ujian'];
        $ujian->id_mapel     = $data['id_mapel'];
        $ujian->kelas_id     = $data['kelas_id'];
        $ujian->kode_jurusan = $data['kode_jurusan'];
        $ujian->jumlah_soal  = (int) $data['jumlah_soal'];
        $ujian->waktu        = $durasi;
        $ujian->jenis        = $data['jenis'];
        $ujian->waktu_mulai   = $mulai;
        $ujian->waktu_selesai = $mulai->copy()->addMinutes($durasi);
        $ujian->terlambat     = $terlambat;
        $ujian->token         = $data['token'];

        // PILIH SALAH SATU (sesuai FK di tabel ujian):
        // Jika ujian.id_guru -> users.id:
        // $ujian->id_guru = Auth::id();

        // Jika ujian.id_guru -> guru.id:
        if ($guruId = Guru::where('user_id', Auth::id())->value('id')) {
            $ujian->id_guru = $guruId;
        }

        $ujian->save();

        return redirect()->route('ujian.index')->with('success', 'Ujian berhasil ditambahkan');
    }

    public function edit($id)
    {
        $ujian     = Ujian::findOrFail($id);
        $mapelList = Mapel::all();
        $kelasList = Kelas::with('jurusan')->get();

        return view('pages.ujian.edit', compact('ujian', 'mapelList', 'kelasList'));
    }

    public function update(Request $request, $id)
    {
        $ujian = Ujian::findOrFail($id);
        $data  = $this->validated($request);

        $mulai     = Carbon::createFromFormat('Y-m-d\TH:i', $data['waktu_mulai']);
        $durasi    = (int) $data['waktu'];
        $terlambat = isset($data['terlambat']) && $data['terlambat']
            ? Carbon::createFromFormat('Y-m-d\TH:i', $data['terlambat'])
            : null;

        $ujian->nama_ujian   = $data['nama_ujian'];
        $ujian->id_mapel     = $data['id_mapel'];
        $ujian->kelas_id     = $data['kelas_id'];
        $ujian->kode_jurusan = $data['kode_jurusan'];
        $ujian->jumlah_soal  = (int) $data['jumlah_soal'];
        $ujian->waktu        = $durasi;
        $ujian->jenis        = $data['jenis'];
        $ujian->waktu_mulai   = $mulai;
        $ujian->waktu_selesai = $mulai->copy()->addMinutes($durasi);
        $ujian->terlambat     = $terlambat;
        $ujian->token         = $data['token'];

        // PILIH SALAH SATU (sesuai FK):
        // $ujian->id_guru = Auth::id(); // jika refer ke users.id
        if ($guruId = Guru::where('user_id', Auth::id())->value('id')) {
            $ujian->id_guru = $guruId;   // jika refer ke guru.id
        }

        $ujian->save();

        return redirect()->route('ujian.index')->with('success', 'Ujian berhasil diperbarui');
    }

    // Ganti dari 'delete' -> 'destroy'
    public function delete($id)
    {
        $ujian = Ujian::findOrFail($id);
        $ujian->delete();

        return redirect()->route('ujian.index')->with('success', 'Ujian berhasil dihapus');
    }

    // --- DRY: satu tempat untuk rules ---
    private function validated(Request $request): array
    {
        return $request->validate([
            'nama_ujian'   => 'required|string|max:200',
            'id_mapel'     => 'required|exists:mapel,id',
            'kelas_id'     => 'required|exists:kelas,id',
            'kode_jurusan' => 'required|string|max:20',
            'jumlah_soal'  => 'required|integer|min:1',
            'waktu'        => 'required|integer|min:1',      // menit
            'jenis'        => 'required|in:acak,set',
            'waktu_mulai'  => 'required|date_format:Y-m-d\TH:i',
            'terlambat'    => 'nullable|date_format:Y-m-d\TH:i',
            'token'        => 'required|string|max:5',
        ]);
    }
}
