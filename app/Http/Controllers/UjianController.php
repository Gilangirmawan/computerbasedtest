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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HasilUjianExport;
use App\Models\IkutUjian;
use App\Models\Jawaban;

class UjianController extends Controller
{
    public function index()
    {
        $guru = \App\Models\Guru::where('user_id', Auth::id())->first();
        
        $query = Ujian::with(['mapel', 'kelas.jurusan', 'soal', 'guru'])
                      ->withCount('soal')
                      ->latest();

        if ($guru) {
            $query->where('id_guru', $guru->id);
        } else {
            $query->whereRaw('1 = 0');
        }

        $ujianList = $query->paginate(1);

        return view('pages.ujian.index', compact('ujianList'));
    }

    public function create()
    {
        $mapelList = Mapel::orderBy('nama')->get();
        $kelasList = Kelas::with('jurusan')->orderBy('kelas')->get();
        // Mengambil semua soal yang ada untuk ditampilkan di form
        $soalList  = Soal::with(['mapel','kelas.jurusan'])->orderBy('id', 'desc')->get();

        return view('pages.ujian.create', compact('mapelList', 'kelasList', 'soalList'));
    }

    // Ganti dari 'tambah' -> 'store'
    public function tambah(Request $request)
    {
        // $data = $this->validated($request);
        // $guru = \App\Models\Guru::where('user_id', Auth::id())->first();

        // if (!$guru) {
        //     return redirect()->back()
        //         ->withErrors(['msg' => 'Gagal menyimpan ujian. Profil guru untuk pengguna ini tidak ditemukan.'])
        //         ->withInput();
        // }

        // $mulai     = Carbon::createFromFormat('Y-m-d\TH:i', $data['waktu_mulai']);
        // $durasi    = (int) $data['waktu'];
        // $terlambat = isset($data['terlambat']) && $data['terlambat']
        //     ? Carbon::createFromFormat('Y-m-d\TH:i', $data['terlambat'])
        //     : null;

        // $ujian = new Ujian();
        // $ujian->nama_ujian   = $data['nama_ujian'];
        // $ujian->id_mapel     = $data['id_mapel'];
        // $ujian->kelas_id     = $data['kelas_id'];
        // $ujian->kode_jurusan = $data['kode_jurusan'];
        // $ujian->jumlah_soal  = (int) $data['jumlah_soal'];
        // $ujian->waktu        = $durasi;
        // $ujian->jenis        = $data['jenis'];
        // $ujian->waktu_mulai   = $mulai;
        // $ujian->waktu_selesai = $mulai->copy()->addMinutes($durasi);
        // $ujian->terlambat     = $terlambat;
        // $ujian->token         = $data['token'];
        
        // $guru = \App\Models\Guru::where('user_id', Auth::id())->first();
        // if (!$guru) {
        //     return redirect()->back()->withErrors(['msg' => 'Gagal menyimpan ujian. Profil guru tidak ditemukan.'])->withInput();
        // }
        // $ujian->id_guru = $guru->id;

        // $ujian->save();
        
        // $soalIds = collect($request->input('soal_ids', []))->map(fn($v)=>(int)$v);

        // if ($request->jenis === 'acak' || $soalIds->isEmpty()) {
        //     $soalIds = Soal::where('id_mapel', $ujian->id_mapel)
        //         ->where('kelas_id', $ujian->kelas_id)
        //         ->inRandomOrder()
        //         ->limit((int)$ujian->jumlah_soal)
        //         ->pluck('id');
        // }

        // $attach = [];
        // foreach ($soalIds as $i => $id) {
        //     $attach[$id] = ['urutan' => $i+1, 'bobot' => 1];
        // }
        // $ujian->soal()->sync($attach);

        // 1. Validasi semua input dari form
        $request->validate([
            'nama_ujian'   => 'required|string|max:255',
            'id_mapel'     => 'required|exists:mapel,id',
            'kelas_id'     => 'required|exists:kelas,id',
            'kode_jurusan' => 'required|string',
            'jumlah_soal'  => 'required|integer|min:1',
            'waktu'        => 'required|integer|min:1',
            'jenis'        => 'required|in:acak,set',
            'waktu_mulai'  => 'required|date',
            'terlambat'    => 'nullable|date|after_or_equal:waktu_mulai',
            'token'        => 'required|string|max:20',
        ]);

        // 2. Ambil profil guru untuk otorisasi
        $guru = \App\Models\Guru::where('user_id', Auth::id())->first();

        // 3. Lapis Keamanan: Hentikan jika profil guru tidak ditemukan
        if (!$guru) {
            return redirect()->back()
                ->withErrors(['msg' => 'Gagal menyimpan ujian. Profil guru untuk pengguna ini tidak ditemukan.'])
                ->withInput();
        }

        // 4. Buat dan simpan data ujian baru
        $mulai = Carbon::parse($request->waktu_mulai);
        $durasi = (int) $request->waktu;

        $ujian = new Ujian();
        $ujian->nama_ujian   = $request->nama_ujian;
        $ujian->id_mapel     = $request->id_mapel;
        $ujian->kelas_id     = $request->kelas_id;
        $ujian->kode_jurusan = $request->kode_jurusan;
        $ujian->jumlah_soal  = (int) $request->jumlah_soal;
        $ujian->waktu        = $durasi;
        $ujian->jenis        = $request->jenis;
        $ujian->waktu_mulai   = $mulai;
        $ujian->waktu_selesai = $mulai->copy()->addMinutes($durasi);
        $ujian->terlambat     = $request->terlambat ? Carbon::parse($request->terlambat) : null;
        $ujian->token         = $request->token;
        $ujian->id_guru       = $guru->id; // Gunakan ID dari profil guru
        
        $ujian->save(); // Simpan ujian

        // 5. Logika untuk membekukan paket soal
        $soalIds = collect($request->input('soal_ids', []));

        if ($request->jenis === 'acak' || $soalIds->isEmpty()) {
            $soalIds = Soal::where('id_mapel', $ujian->id_mapel)
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

        return redirect()->route('ujian.index')->with('swal_success', 'Ujian berhasil ditambahkan');
    }

    public function edit($id)
    {
        $ujian     = Ujian::findOrFail($id);
        $mapelList = Mapel::orderBy('nama')->get();
        $kelasList = Kelas::with('jurusan')->orderBy('kelas')->get();

        // Mengambil semua soal yang ada untuk ditampilkan di form
        $soalList  = Soal::with(['mapel','kelas.jurusan'])->orderBy('id', 'desc')->get();

        // Mengambil ID dari soal-soal yang sudah ada di paket ujian ini
        $paketIds = $ujian->soal()->pluck('soal.id')->toArray();

        return view('pages.ujian.edit', compact('ujian', 'mapelList', 'kelasList', 'soalList', 'paketIds'));
    }

    public function update(Request $request, $id)
    {
        // $ujian = Ujian::findOrFail($id);
        // $data  = $this->validated($request);

        // $mulai     = Carbon::createFromFormat('Y-m-d\TH:i', $data['waktu_mulai']);
        // $durasi    = (int) $data['waktu'];
        // $terlambat = isset($data['terlambat']) && $data['terlambat']
        //     ? Carbon::createFromFormat('Y-m-d\TH:i', $data['terlambat'])
        //     : null;

        // $ujian->nama_ujian   = $data['nama_ujian'];
        // $ujian->id_mapel     = $data['id_mapel'];
        // $ujian->kelas_id     = $data['kelas_id'];
        // $ujian->kode_jurusan = $data['kode_jurusan'];
        // $ujian->jumlah_soal  = (int) $data['jumlah_soal'];
        // $ujian->waktu        = $durasi;
        // $ujian->jenis        = $data['jenis'];
        // $ujian->waktu_mulai   = $mulai;
        // $ujian->waktu_selesai = $mulai->copy()->addMinutes($durasi);
        // $ujian->terlambat     = $terlambat;
        // $ujian->token         = $data['token'];

        // if ($guruId = Guru::where('user_id', Auth::id())->value('id')) {
        //     $ujian->id_guru = $guruId;   
        // }

        // $ujian->save();

        // if ($request->boolean('refresh_paket')) {
        //     $soalIds = collect($request->input('soal_ids', []))->map(fn($v)=>(int)$v);

        //     if ($ujian->jenis === 'acak' || $soalIds->isEmpty()) {
        //         $soalIds = \App\Models\Soal::where('id_mapel', $ujian->id_mapel)
        //             ->where('kelas_id', $ujian->kelas_id)
        //             ->inRandomOrder()
        //             ->limit((int)$ujian->jumlah_soal)
        //             ->pluck('id');
        //     }

        //     $attach = [];
        //     foreach ($soalIds as $i => $id) {
        //         $attach[$id] = ['urutan' => $i+1, 'bobot' => 1];
        //     }
        //     $ujian->soal()->sync($attach);
        // }

        // 2. Validasi semua input dari form
        $request->validate([
            'nama_ujian'   => 'required|string|max:255',
            'id_mapel'     => 'required|exists:mapel,id',
            'kelas_id'     => 'required|exists:kelas,id',
            'kode_jurusan' => 'required|string',
            'jumlah_soal'  => 'required|integer|min:1',
            'waktu'        => 'required|integer|min:1',
            'jenis'        => 'required|in:acak,set',
            'waktu_mulai'  => 'required|date',
            'terlambat'    => 'nullable|date|after_or_equal:waktu_mulai',
            'token'        => 'required|string|max:20',
        ]);

        // 3. Cari data ujian berdasarkan $id
        $ujian = Ujian::findOrFail($id);

        // 4. Ambil profil guru untuk otorisasi
        $guru = \App\Models\Guru::where('user_id', Auth::id())->first();

        // 5. Lapis Keamanan: Pastikan hanya pemilik yang bisa mengedit
        if (!$guru || $ujian->id_guru != $guru->id) {
            abort(403, 'ANDA TIDAK MEMILIKI IZIN UNTUK MENGEDIT UJIAN INI.');
        }

        // 6. Update data ujian satu per satu
        $mulai = Carbon::parse($request->waktu_mulai);
        $durasi = (int) $request->waktu;

        $ujian->nama_ujian   = $request->nama_ujian;
        $ujian->id_mapel     = $request->id_mapel;
        $ujian->kelas_id     = $request->kelas_id;
        $ujian->kode_jurusan = $request->kode_jurusan;
        $ujian->jumlah_soal  = (int) $request->jumlah_soal;
        $ujian->waktu        = $durasi;
        $ujian->jenis        = $request->jenis;
        $ujian->waktu_mulai   = $mulai;
        $ujian->waktu_selesai = $mulai->copy()->addMinutes($durasi);
        $ujian->terlambat     = $request->terlambat ? Carbon::parse($request->terlambat) : null;
        $ujian->token         = $request->token;
        
        $ujian->save(); // Simpan perubahan pada ujian

        // 7. Logika untuk menyegarkan paket soal jika dicentang
        if ($request->input('refresh_paket')) {
            $soalIds = collect($request->input('soal_ids', []));

            if ($request->jenis === 'acak' || $soalIds->isEmpty()) {
                $soalIds = Soal::where('id_mapel', $ujian->id_mapel)
                    ->where('kelas_id', $ujian->kelas_id)
                    ->inRandomOrder()
                    ->limit((int)$ujian->jumlah_soal)
                    ->pluck('id');
            }

            $attach = [];
            foreach ($soalIds as $i => $soalId) {
                $attach[$soalId] = ['urutan' => $i+1, 'bobot' => 1];
            }
            $ujian->soal()->sync($attach);
        }

        return redirect()->route('ujian.index')->with('swal_success', 'Ujian berhasil diperbarui');
    }

    // Ganti dari 'delete' -> 'destroy'
    public function delete($id)
    {
        $ujian = Ujian::findOrFail($id);
        $ujian->delete();

        return redirect()->route('ujian.index')->with('swal_success', 'Ujian berhasil dihapus');
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

    public function detail(Ujian $ujian)
    {
        // Ambil profil guru yang sedang login
        $guru = \App\Models\Guru::where('user_id', Auth::id())->first();

        // Lapis Keamanan: Pastikan guru yang login adalah pemilik ujian
        if (!$guru || $ujian->id_guru != $guru->id) {
            abort(403, 'ANDA TIDAK MEMILIKI AKSES KE HALAMAN INI.');
        }

        // 1. Ambil data ujian dasar dan daftar pesertanya (tanpa eager load siswa)
        $ujian->load(['mapel', 'kelas.jurusan', 'peserta']);

        $pesertaList = \App\Models\IkutUjian::where('id_ujian', $ujian->id)
                                        ->latest('tgl_selesai') // Urutkan dari yang terbaru
                                        ->paginate(3); // Tampilkan 3 data per halaman

        // 2. Kumpulkan semua ID siswa dari daftar peserta
        $siswaIds = $ujian->peserta->pluck('id_siswa');

        // 3. Ambil semua profil siswa yang dibutuhkan, beserta relasi kelas dan jurusannya
        $profilSiswaList = \App\Models\Siswa::with('kelas.jurusan') // Memuat relasi kelas dan jurusan
                                ->whereIn('id', $siswaIds)
                                ->get()
                                ->keyBy('id'); // Gunakan ID siswa sebagai kunci array

        // 4. "Suntikkan" profil siswa yang benar ke setiap data peserta
        foreach ($ujian->peserta as $peserta) {
            // Kita buat properti baru 'profil_siswa' untuk menyimpan data yang benar
            if (isset($profilSiswaList[$peserta->id_siswa])) {
                $peserta->profil_siswa = $profilSiswaList[$peserta->id_siswa];
            } else {
                $peserta->profil_siswa = null; // Jika profil siswa tidak ditemukan
            }
        }
        return view('pages.ujian.detail', compact('ujian', 'pesertaList'));
    }

    public function export(Ujian $ujian)
    {
        // Lapis Keamanan: Pastikan guru yang login adalah pemilik ujian
        $guru = \App\Models\Guru::where('user_id', Auth::id())->first();
        if (!$guru || $ujian->id_guru != $guru->id) {
            abort(403, 'ANDA TIDAK MEMILIKI AKSES UNTUK MENGEKSPOR DATA INI.');
        }

        // Membuat nama file yang dinamis
        $namaFile = 'hasil-' . \Illuminate\Support\Str::slug($ujian->nama_ujian) . '.xlsx';

        // Menggunakan kelas ekspor yang sudah kita buat
        return Excel::download(new HasilUjianExport($ujian), $namaFile);
    }

    public function lihatHasil(IkutUjian $ikutUjian)
    {
        // 1. Muat relasi yang tidak bermasalah (ujian dan jawaban)
        $ikutUjian->load(['ujian', 'jawaban.soal']);

        // 2. Ambil profil siswa yang benar secara manual menggunakan id_siswa
        //    beserta relasi kelas dan jurusannya.
        $profilSiswa = \App\Models\Siswa::with('kelas.jurusan')->find($ikutUjian->id_siswa);

        // 3. "Suntikkan" profil siswa yang benar ke dalam objek $ikutUjian
        //    Kita gunakan properti baru 'profil_siswa' agar tidak bentrok.
        $ikutUjian->profil_siswa = $profilSiswa;

        // 2. Ambil data jawaban secara manual berdasarkan id_ujian dan id_siswa
        $daftarJawaban = \App\Models\Jawaban::where('id_ujian', $ikutUjian->id_ujian)
                                            ->where('id_siswa', $ikutUjian->id_siswa)
                                            ->with('soal') // Eager load soal untuk setiap jawaban
                                            ->paginate(5);

        // Lapis Keamanan: Pastikan guru yang mengakses adalah pemilik ujian ini
        $guru = \App\Models\Guru::where('user_id', Auth::id())->first();
        if (!$guru || $ikutUjian->ujian->id_guru != $guru->id) {
            abort(403, 'ANDA TIDAK MEMILIKI AKSES KE HALAMAN INI.');
        }

        // 3. Kirim variabel baru '$daftarJawaban' ke view
        return view('pages.ujian.hasil', compact('ikutUjian', 'daftarJawaban'));
    }

    /**
     * Menghapus (membatalkan) data pengerjaan ujian seorang siswa.
     */
    public function batalkanHasil(IkutUjian $ikutUjian)
    {
        // Lapis Keamanan: Pastikan guru yang mengakses adalah pemilik ujian ini
        $guru = \App\Models\Guru::where('user_id', Auth::id())->first();
        if (!$guru || $ikutUjian->ujian->id_guru != $guru->id) {
            abort(403, 'ANDA TIDAK MEMILIKI AKSES UNTUK MELAKUKAN TINDAKAN INI.');
        }

        // Simpan id ujian untuk redirect kembali
        $idUjian = $ikutUjian->id_ujian;

        // Hapus semua jawaban terkait
        Jawaban::where('id_ujian', $ikutUjian->id_ujian)
               ->where('id_siswa', $ikutUjian->id_siswa)
               ->delete();
        
        // Hapus data riwayat ujian
        $ikutUjian->delete();

        return redirect()->route('ujian.detail', $idUjian)
            ->with('success', 'Hasil ujian siswa telah berhasil dibatalkan.');
    }
}
