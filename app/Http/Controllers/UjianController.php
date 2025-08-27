<?php

namespace App\Http\Controllers;

use App\Models\Ujian;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Soal;

class UjianController extends Controller
{
    public function index()
    {
        // eager load sampai jurusan agar tampil di tabel
        // $ujianList = Ujian::with(['mapel', 'kelas.jurusan'])->latest()->get();

        $ujianList = Ujian::with(['mapel','kelas.jurusan','soal'])
                    ->withCount('soal')
                    ->get();

        return view('pages.ujian.index', compact('ujianList'));
    }

    public function create()
    {
        $mapelList = Mapel::all();
        $kelasList = Kelas::with('jurusan')->get();

        $mapelList = Mapel::all();
        $kelasList = Kelas::with('jurusan')->get();

        // pool awal (opsional): batasi 100
        $soalPool  = \App\Models\Soal::with(['mapel','kelas.jurusan'])
                    ->latest('id')->take(100)->get();

        return view('pages.ujian.create', compact('mapelList', 'kelasList', 'soalPool'));
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
        $ujian->waktu_selesai = $mulai->copy()->addMinutes($durasi); // tambahkan ini
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
        // --- Bekukan paket soal untuk ujian ini ---

        $soalIds = collect($request->input('soal_ids', []))->map(fn($v)=>(int)$v);
        $soalIds = collect($request->input('soal_ids', []))->map(fn($v)=>(int)$v);

        if ($request->jenis === 'acak' || $soalIds->isEmpty()) {
            // Ambil acak sesuai mapel & kelas
            $soalIds = Soal::where('id_mapel', $ujian->id_mapel)
                ->where('kelas_id', $ujian->kelas_id)
                ->inRandomOrder()
                ->limit((int)$ujian->jumlah_soal)
                ->pluck('id');
        }

        // siapkan payload pivot: urutan + bobot default 1
        $attach = [];
        foreach ($soalIds as $i => $id) {
            $attach[$id] = ['urutan' => $i+1, 'bobot' => 1];
        }
        $ujian->soal()->sync($attach);   // bekukan paket  

        return redirect()->route('ujian.index')->with('success', 'Ujian berhasil ditambahkan');
    }

    public function edit($id)
    {
        $ujian     = Ujian::findOrFail($id);
        $mapelList = Mapel::all();
        $kelasList = Kelas::with('jurusan')->get();
        $ujian = Ujian::with('soal')->findOrFail($id);
        $mapelList = Mapel::all();
        $kelasList = Kelas::with('jurusan')->get();
        $soalPool  = \App\Models\Soal::with(['mapel','kelas.jurusan'])
                    ->latest('id')->take(150)->get();

        return view('pages.ujian.edit', compact('ujian', 'mapelList', 'kelasList', 'soalPool'));
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

        if ($request->boolean('refresh_paket')) {
            $soalIds = collect($request->input('soal_ids', []))->map(fn($v)=>(int)$v);

            if ($ujian->jenis === 'acak' || $soalIds->isEmpty()) {
                $soalIds = \App\Models\Soal::where('id_mapel', $ujian->id_mapel)
                    ->where('kelas_id', $ujian->kelas_id)
                    ->inRandomOrder()
                    ->limit((int)$ujian->jumlah_soal)
                    ->pluck('id');
            }

            $attach = [];
            foreach ($soalIds as $i => $id) {
                $attach[$id] = ['urutan' => $i+1, 'bobot' => 1];
            }
            $ujian->soal()->sync($attach);
        }

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
