<?php

namespace App\Http\Controllers;

use App\Models\Ujian;
use App\Models\Soal;
use App\Models\IkutUjian;
use App\Models\Jawaban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // <-- PERUBAHAN 1: Tambahkan baris ini

class IkutUjianController extends Controller
{
    // Halaman awal
    public function index()
    {
        return view('pages.IkutUjian.index');
    }

    /**
     * Ambil ID siswa yang sedang login.
     *
     * @return int|null
     */
    protected function getSiswaId()
    {
        $id = Auth::id(); // mengembalikan null atau integer
        if (! $id) abort(401, 'Silakan login sebagai siswa.');
        return (int) $id;
    }

    // Daftar ujian tersedia
    public function daftar()
    {
        $siswa = Auth::user();

        $profilSiswa = $siswa->siswa;

        if (!$profilSiswa) {
            $ujian = collect(); 
        } else {
            // =================================================================
            // PERBAIKAN FINAL DI SINI
            // Mencari di kolom 'kode_jurusan' (milik tabel ujian)
            // Menggunakan nilai dari '$profilSiswa->jurusan_id' (milik tabel siswa)
            // =================================================================
            $ujian = Ujian::with('mapel')
                        ->where('kelas_id', $profilSiswa->kelas_id)
                        ->where('kode_jurusan', $profilSiswa->jurusan_id) 
                        ->get();
            // =================================================================
        }

        return view('pages.IkutUjian.daftar', compact('ujian'));
    }

    // Validasi token
    public function cekToken(Request $request)
    {
        $request->validate([
            'id_ujian' => 'required|integer',
            'token'    => 'required|string',
        ]);

        $ujian = Ujian::findOrFail($request->id_ujian);

        // 1. Validasi token terlebih dahulu
        if ($request->token !== $ujian->token) {
            return back()->with('error', 'Token yang Anda masukkan salah, silakan coba lagi!');
        }

        // =================================================================
        // PENAMBAHAN: Cek apakah siswa sudah pernah menyelesaikan ujian ini
        // =================================================================
        $user = Auth::user();
        $profilSiswa = \App\Models\Siswa::where('user_id', $user->id)->first();

        // Jika profil siswa tidak ditemukan (sebagai penjaga keamanan)
        if (!$profilSiswa) {
            return back()->with('error', 'Gagal memvalidasi, profil siswa Anda tidak ditemukan.');
        }

        // Cek di tabel 'ikut_ujian'
        $sudahSelesai = IkutUjian::where('id_ujian', $ujian->id)
                                   ->where('id_siswa', $profilSiswa->id)
                                   ->exists(); // Cukup periksa apakah datanya ada

        if ($sudahSelesai) {
            return back()->with('error', 'Anda sudah menyelesaikan ujian "' . $ujian->nama_ujian . '" dan tidak dapat mengerjakannya kembali.');
        }
        // =================================================================

        // Jika semua pengecekan lolos, izinkan masuk ke halaman ujian
        return redirect()->route('ikutujian.mulai', $ujian->id);
    }

    // Mulai ujian
    public function mulai($id)
    {
        $ujian = Ujian::with('mapel')->findOrFail($id);
        
        // =================================================================
        // MENGAKTIFKAN KEMBALI BLOK VALIDASI WAKTU UJIAN
        // =================================================================
        $now = Carbon::now();
        $waktuMulai = Carbon::parse($ujian->waktu_mulai);
        // Batas akhir adalah waktu selesai + toleransi keterlambatan dari database
        $batasAkhir = Carbon::parse($ujian->waktu_selesai)->addMinutes($ujian->terlambat ?? 0);

        // Cek jika ujian belum dimulai
        if ($now->isBefore($waktuMulai)) {
            return redirect()->route('ikutujian.daftar')
                ->with('error', 'Gagal memulai! Ujian "' . $ujian->nama_ujian . '" belum dimulai.');
        }

        // Cek jika waktu untuk memulai ujian sudah terlewat
        if ($now->isAfter($batasAkhir)) {
            return redirect()->route('ikutujian.daftar')
                ->with('error', 'Gagal memulai! Waktu untuk Ujian "' . $ujian->nama_ujian . '" telah berakhir.');
        }
        // =================================================================
        // AKHIR DARI BLOK VALIDASI
        // =================================================================

        $soalIds = DB::table('paket_soal')
            ->where('id_ujian', $ujian->id)
            ->pluck('id_soal');

        $soal = Soal::whereIn('id', $soalIds)->get();

        return view('pages.IkutUjian.mulai', compact('ujian', 'soal'));
    }

    // Simpan jawaban (autosave)
    public function simpanJawaban(Request $request)
    {
        $request->validate([
            'id_ujian' => 'required|integer',
            'id_soal'  => 'required|integer',
            'jawaban'  => 'nullable|string|max:5',
        ]);

        $idSiswa = $this->getSiswaId();

        Jawaban::updateOrCreate(
            [
                'id_ujian' => $request->id_ujian,
                'id_siswa' => $idSiswa,
                'id_soal'  => $request->id_soal,
            ],
            [
                'jawaban'  => $request->jawaban,
            ]
        );

        return response()->json(['success' => true]);
    }

    // Selesai ujian → simpan semua jawaban & nilai
    public function selesaiUjian(Request $request, $idUjian)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir.');
        }

        $profilSiswa = \App\Models\Siswa::where('user_id', $user->id)->first();

        if (!$profilSiswa) {
            return redirect()->route('ikutujian.daftar')
                ->with('error', 'Gagal menyimpan jawaban. Profil siswa tidak ditemukan.');
        }

        $idSiswa = $profilSiswa->id;

        // 1. Simpan semua jawaban ke tabel 'jawaban'
        $answers = $request->input('jawaban', []);
        foreach ($answers as $idSoal => $pilihan) {
            Jawaban::updateOrCreate(
                ['id_ujian' => $idUjian, 'id_siswa' => $idSiswa, 'id_soal' => $idSoal],
                ['jawaban'  => $pilihan]
            );
        }

        // 2. Hitung nilai berdasarkan jawaban yang baru disimpan
        $jawabanSiswa = Jawaban::where('id_ujian', $idUjian)->where('id_siswa', $idSiswa)->get();
        $benar = 0;
        foreach ($jawabanSiswa as $jwb) {
            $soal = Soal::find($jwb->id_soal);
            if ($soal && $jwb->jawaban !== null) {
                if (trim(strtolower($soal->jawaban)) === trim(strtolower($jwb->jawaban))) {
                    $benar++;
                    $jwb->is_benar = true;
                } else {
                    $jwb->is_benar = false;
                }
            } else {
                $jwb->is_benar = false;
            }
            $jwb->save();
        }
        $jumlahSoal = $jawabanSiswa->count();
        $nilai = ($jumlahSoal > 0) ? ($benar / $jumlahSoal) * 100 : 0;

        // =================================================================
        // 3. (BAGIAN KRUSIAL) Simpan ringkasan ke tabel 'ikut_ujian'
        // Pastikan nama kolom 'id_siswa' di sini merujuk ke tabel 'siswa'
        // =================================================================
        IkutUjian::updateOrCreate(
            ['id_ujian' => $idUjian, 'id_siswa' => $idSiswa],
            [
                'jml_benar'   => $benar,
                'nilai'       => $nilai,
                'tgl_selesai' => now(),
                'status'      => 'selesai',
            ]
        );
        // =================================================================

        return redirect()->route('ikutujian.daftar')
            ->with('success', 'Ujian telah berhasil diselesaikan. Silakan tunggu informasi selanjutnya dari guru Anda.');
    }
}